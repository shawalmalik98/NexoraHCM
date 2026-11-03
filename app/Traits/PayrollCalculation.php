<?php

namespace App\Traits;

use App\Models\{Attendance, EmployeeSalary, Leave, LeaveDeduction, LeaveManagement, LeaveRequest, SalaryDivision};
use DateInterval;
use DatePeriod;
use DateTime;

/**
 * Monthly payroll calculation shared by the web and API payroll controllers.
 *
 * Leave policy: an absent day takes 1 leave; the first 2 late comings (early outs count as late) and the first
 * half day of a payroll period are free and every further one takes half a leave. Leaves are taken Annual first,
 * then Casual, then Sick; with no leave left the day (absent) or half a day (late / half day) is deducted from salary.
 */
trait PayrollCalculation
{
    protected $payroll_late_allowed = 2;
    protected $payroll_halfday_allowed = 1;
    // order in which payroll takes leaves (absent / extra late / half day): Annual, then Casual, then Sick
    protected $payroll_deduction_leave_names = ['Annual Leave', 'Casual Leave', 'Sick Leave'];

    // sandwich (club) rule for this run: the generate form's toggle when sent, otherwise the system setting
    protected function payroll_club_rule_applied($request, $fetch_system_setting)
    {
        if ($request->has('sandwich_rule_choice')) {
            return $request->input('apply_sandwich_rule') == 1 ? 1 : 0;
        }
        return (!empty($fetch_system_setting) && !empty($fetch_system_setting[0]['club_rule_apply'])) ? $fetch_system_setting[0]['club_rule_apply'] : 0;
    }

    // attendance rows of the employee in the payroll period
    protected function get_employee_attendance($date_from, $date_to, $employee_id)
    {
        return Attendance::where('att_employee_id', $employee_id)
            ->whereRaw('STR_TO_DATE(CONCAT(att_for_year,"-",att_for_month,"-",att_for_date),"%Y-%m-%d") >= ? and STR_TO_DATE(CONCAT(att_for_year,"-",att_for_month,"-",att_for_date),"%Y-%m-%d") <= ?', [$date_from, $date_to])
            ->get()->toArray();
    }

    // status of every date of the period for one employee
    protected function get_employee_complete_attendance_status($date_from, $date_to, $attendances, $club_rule_is_applied)
    {
        $manage_leaves = LeaveManagement::pluck('leave_name')->toArray();

        $present_count = 0;
        $absent_count = 0;
        $halfday_count = 0;
        $late_count = 0;
        $leave_count = 0;

        $date_with_status = [];
        $absent_dates = [];
        // days before joining / after deactivation: not absent, just not paid (gross is the employed days only)
        $not_employed_dates = [];

        $attendances['employee_offdays'] = (!empty($attendances['employee_offdays'])) ? explode(",", $attendances['employee_offdays']) : [];

        $join_date = strtotime($attendances['employee_doj']);
        $deactive_date = strtotime($attendances['employee_deactivate_date']);

        // rows keyed by Y-m-d, so an unpadded month / day still matches
        $attendance_by_date = [];
        foreach ($attendances['attendance'] as $atty) {
            $attendance_by_date[date('Y-m-d', strtotime($atty['att_for_year'] . '-' . $atty['att_for_month'] . '-' . $atty['att_for_date']))][] = $atty;
        }

        $date_to = new DateTime($date_to);
        $date_to->modify('+1 day');
        $period = new DatePeriod(new DateTime($date_from), new DateInterval('P1D'), $date_to);
        $all_dates_of_periods = [];
        foreach ($period as $dt) {
            $all_dates_of_periods[] = $dt->format('Y-m-d');
        }

        foreach ($all_dates_of_periods as $date) {

            if ($join_date > strtotime($date) || (!empty($deactive_date) && $deactive_date < strtotime($date))) {

                $not_employed_dates[] = $date;
                $date_with_status[$date] = "Not Employed";
                continue;
            }

            $absent = true;
            foreach ($attendance_by_date[$date] ?? [] as $atty) {

                $absent = false;
                $status = $atty['att_check_overall_status'];

                if ($status == 'Present') {
                    $present_count++;
                    $date_with_status[$date] = "Present";
                } elseif ($status == 'Absent') {
                    $absent_count++;
                    $date_with_status[$date] = "Absent";
                } elseif ($status == 'Half day') {
                    $halfday_count++;
                    $date_with_status[$date] = "Half day";
                } elseif ($status == 'Late Coming') {
                    $late_count++;
                    $date_with_status[$date] = "Late Coming";
                } elseif (in_array($status, $manage_leaves)) {
                    $leave_count++;
                    $date_with_status[$date] = "Leave";
                } elseif ($status == 'Early out') {
                    $late_count++;
                    $date_with_status[$date] = "Late Coming";
                } elseif (substr($status, 0, 7) == 'Holiday') {
                    $date_with_status[$date] = "Holiday";
                }
            }

            if ($absent) {
                if (in_array(date('l', strtotime($date)), $attendances['employee_offdays'])) {
                    $date_with_status[$date] = "Off Day";
                } else {
                    $absent_count++;
                    $absent_dates[] = $date;
                    $date_with_status[$date] = "Absent";
                }
            }
        }

        $club_rule_absent = 0;
        if ($club_rule_is_applied == 1) {
            $club_rule = $this->apply_club_rule($all_dates_of_periods, $date_with_status);
            $date_with_status = $club_rule['date_with_status'];
            $club_rule_absent = $club_rule['rule_absent'];
        }

        return [
            'present_count' => $present_count,
            'absent_count' => $absent_count + $club_rule_absent,
            'halfday_count' => $halfday_count,
            'late_count' => $late_count,
            'leave_count' => $leave_count,
            'days_in_period' => count($all_dates_of_periods),
            'date_with_status' => $date_with_status,
            'not_employed_dates' => $not_employed_dates,
        ];
    }

    // Sandwich rule: a run of off days (any length) with an absent day right before and right after
    // it is counted absent too. Runs touching the start or end of the period are left as they are.
    protected function apply_club_rule($all_dates_of_periods, $date_with_status)
    {
        $rule_absent = 0;
        $total_dates = count($all_dates_of_periods);
        $i = 0;

        while ($i < $total_dates) {

            if (($date_with_status[$all_dates_of_periods[$i]] ?? '') != 'Off Day') {
                $i++;
                continue;
            }

            $run_end = $i;
            while ($run_end + 1 < $total_dates && ($date_with_status[$all_dates_of_periods[$run_end + 1]] ?? '') == 'Off Day') {
                $run_end++;
            }

            $before = $i - 1;
            $after = $run_end + 1;
            if ($before >= 0 && $after < $total_dates
                && ($date_with_status[$all_dates_of_periods[$before]] ?? '') == 'Absent'
                && ($date_with_status[$all_dates_of_periods[$after]] ?? '') == 'Absent') {

                for ($k = $i; $k <= $run_end; $k++) {
                    $date_with_status[$all_dates_of_periods[$k]] = "Absent Club";
                    $rule_absent++;
                }
            }

            $i = $run_end + 1;
        }

        return [
            'rule_absent' => $rule_absent,
            'date_with_status' => $date_with_status,
        ];
    }

    // Remaining balance of a leave type: allotment - availed (attendance) - pending requests - earlier late / half day deductions
    protected function payroll_leave_balance($employee_id, $leave_type_id, $leave_name)
    {
        $allotment = Leave::where('leaves_employee_id', $employee_id)->where('leave_type_id', $leave_type_id)->value('leave_allotement');
        if ($allotment === null) {
            return 0;
        }

        $availed = Attendance::where('att_employee_id', $employee_id)->where('att_check_overall_status', $leave_name)->count();
        $pending = LeaveRequest::where('leave_applied_by', (string) $employee_id)->where('leave_applied_type', $leave_type_id)->where('leave_approved_by_hr', 1)->count();

        return (float) $allotment - $availed - $pending - LeaveDeduction::deducted_count($employee_id, $leave_type_id);
    }

    // Leave policy of the payroll, applied in date order:
    //  - an absent day takes 1 leave (Annual, then Casual, then Sick); with no leave left the day's salary is deducted
    //  - 2 late comings and 1 half day a month are free; each extra one takes half a leave (two make one leave);
    //    with no leave left, half a day's salary is deducted
    // Returns one entry per day: ['date', 'reason', 'days' (leave taken: 1 or 0.5), 'leave_type_id' (null = salary)].
    protected function payroll_late_halfday_deductions($employee_id, $date_with_status)
    {
        $late_dates = array_keys($date_with_status, 'Late Coming', true);
        $halfday_dates = array_keys($date_with_status, 'Half day', true);
        sort($late_dates);
        sort($halfday_dates);

        $chargeable = [];
        foreach ($date_with_status as $date => $status) {
            if ($status == 'Absent' || $status == 'Absent Club') {
                $chargeable[] = ['date' => $date, 'reason' => 'Absent', 'days' => 1];
            }
        }
        foreach (array_slice($late_dates, $this->payroll_late_allowed) as $date) {
            $chargeable[] = ['date' => $date, 'reason' => 'Late Coming', 'days' => 0.5];
        }
        foreach (array_slice($halfday_dates, $this->payroll_halfday_allowed) as $date) {
            $chargeable[] = ['date' => $date, 'reason' => 'Half day', 'days' => 0.5];
        }
        if (empty($chargeable)) {
            return [];
        }
        usort($chargeable, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        $leave_types = LeaveManagement::whereIn('leave_name', $this->payroll_deduction_leave_names)->pluck('leave_manage_id', 'leave_name')->toArray();
        $balances = [];
        foreach ($this->payroll_deduction_leave_names as $leave_name) {
            if (isset($leave_types[$leave_name])) {
                $balances[$leave_types[$leave_name]] = $this->payroll_leave_balance($employee_id, $leave_types[$leave_name], $leave_name);
            }
        }

        $deductions = [];
        foreach ($chargeable as $item) {
            $item['leave_type_id'] = null;
            foreach ($balances as $leave_type_id => $balance) {
                if ($balance >= $item['days']) {
                    $item['leave_type_id'] = $leave_type_id;
                    $balances[$leave_type_id] -= $item['days'];
                    break;
                }
            }
            $deductions[] = $item;
        }

        return $deductions;
    }

    protected function record_late_halfday_deductions($employee_id, $payroll_id, array $deductions)
    {
        foreach ($deductions as $deduction) {
            LeaveDeduction::create([
                'ld_employee_id' => $employee_id,
                'ld_leave_type_id' => $deduction['leave_type_id'],
                'ld_date' => $deduction['date'],
                'ld_reason' => $deduction['reason'],
                'ld_days' => $deduction['days'] ?? 1,
                'ld_payroll_id' => $payroll_id,
            ]);
        }
    }

    // EOBI / provident fund employee shares for one payslip, as [title => amount]; empty when switched off in settings.
    // Basic salary is the "Basic" salary division share of the period's gross (all of gross when no such division exists).
    protected function statutory_deductions($gross_salary, $setting)
    {
        $deductions = [];

        if (!empty($setting['eobi_apply']) && $setting['eobi_apply'] == 1) {
            $eobi = round(($setting['eobi_minimum_wage'] ?? 0) * ($setting['eobi_employee_percentage'] ?? 0) / 100, 2);
            if ($eobi > 0) {
                $deductions['EOBI Contribution'] = $eobi;
            }
        }

        $pf = $this->provident_fund_shares($gross_salary, $setting);
        if ($pf['member'] > 0) {
            $deductions['Provident Fund'] = $pf['member'];
        }

        return $deductions;
    }

    // Provident fund of one payslip: employee share (deducted from salary) and company share (paid by the company
    // into the fund, not into the salary), each a % of basic salary — Pakistan practice 8.33% each.
    protected function provident_fund_shares($gross_salary, $setting)
    {
        if (empty($setting['pf_apply']) || $setting['pf_apply'] != 1) {
            return ['member' => 0, 'company' => 0];
        }

        $basic_percentage = SalaryDivision::whereRaw('LOWER(salary_division_title) LIKE ?', ['%basic%'])->value('salary_division_percentage');
        $basic_salary = $basic_percentage !== null ? $gross_salary * $basic_percentage / 100 : $gross_salary;
        $company_percentage = isset($setting['pf_company_percentage']) && $setting['pf_company_percentage'] !== null && $setting['pf_company_percentage'] !== '' ? $setting['pf_company_percentage'] : ($setting['pf_employee_percentage'] ?? 0);

        return [
            'member' => round($basic_salary * ($setting['pf_employee_percentage'] ?? 0) / 100, 2),
            'company' => round($basic_salary * $company_percentage / 100, 2),
        ];
    }

    // Income Tax Ordinance 2001, Sixth Schedule Part I rule 3(a): the company's yearly contribution to a recognised
    // provident fund above one-tenth of the salary or Rs 150,000 (whichever is lower) is the employee's taxable income.
    // Returns that excess per month, to add to the monthly salary the tax is worked out on.
    protected function provident_fund_taxable_excess($monthly_salary, $setting)
    {
        $company_yearly = $this->provident_fund_shares($monthly_salary, $setting)['company'] * 12;
        $tax_free = min($monthly_salary * 12 / 10, 150000);

        return round(max(0, $company_yearly - $tax_free) / 12, 2);
    }

    // Gross / net salary of the period. Returns null when the employee has no monthly salary record.
    // Salary is on a fixed 30 day basis: one day = monthly salary / 30 whether the month has 28, 29, 30 or 31 days.
    // A full month pays the full salary; every absent / late / half day salary deduction is salary / 30;
    // a month worked only in part (joined / left in it) pays days employed x salary / 30, never more than the salary.
    protected $payroll_days_per_month = 30;

    protected function calculate_salary_of_employee($date_from, $date_to, $attendance_count, $employee_id, $period_count)
    {
        $date_with_status = $attendance_count['date_with_status'];
        $not_employed = array_flip($attendance_count['not_employed_dates'] ?? []);

        $salary_period = EmployeeSalary::where('salary_employee_id', $employee_id)->where('salary_effected_date', '>=', $date_from)->where('salary_effected_date', '<=', $date_to)->where('salary_earning_type', 1)->orderBy('salary_effected_date', 'ASC')->get()->toArray();

        if (empty($salary_period)) {
            $salary_period = EmployeeSalary::where('salary_employee_id', $employee_id)->where('salary_earning_type', 1)->orderBy('salary_effected_date', 'DESC')->limit(1)->get()->toArray();
        } else {
            $salary_period_last = EmployeeSalary::where('salary_employee_id', $employee_id)->where('salary_effected_date', '<', $salary_period[0]['salary_effected_date'])->where('salary_earning_type', 1)->orderBy('salary_effected_date', 'DESC')->limit(1)->get()->toArray();
            $salary_period = array_merge($salary_period_last, $salary_period);
        }

        if (empty($salary_period)) {
            return null;
        }

        $salary_effected_date = array_combine(array_column($salary_period, 'salary_effected_date'), array_column($salary_period, 'salary_amount'));

        $salary_chunk = [];
        $salary_chunk_attendance_status = [];
        $splite_count = 0;

        if (count($salary_effected_date) > 1) {
            foreach ($salary_effected_date as $salary_effected => $value) {
                $splite_date = array_search($salary_effected, array_keys($date_with_status));
                $salary_chunk[$splite_count] = array_slice(array_keys($date_with_status), $splite_date, count($date_with_status));
                $salary_chunk_attendance_status[$splite_count] = array_slice(array_values($date_with_status), $splite_date, count($date_with_status));
                $splite_count++;
            }
            for ($i = 0; $i < count($salary_chunk) - 1; $i++) {
                $salary_chunk[$i] = array_diff($salary_chunk[$i], $salary_chunk[$i + 1]);
                $salary_chunk_attendance_status[$i] = array_slice($salary_chunk_attendance_status[$i], 0, count($salary_chunk_attendance_status[$i]) - count($salary_chunk_attendance_status[$i + 1]));
            }
        } else {
            $salary_chunk[0] = array_keys($date_with_status);
            $salary_chunk_attendance_status[0] = array_values($date_with_status);
        }

        $salary_list = array_values($salary_effected_date);

        $total_each_chunk_absent = 0;
        $total_chunk_salary_net = 0;

        $each_chunk_late = 0;
        $each_chunk_half_day = 0;
        $each_chunk_present = 0;
        $each_chunk_offday = 0;
        $each_chunk_leave = 0;
        $each_chunk_holiday = 0;
        $each_chunk_gross_salary = 0;
        // income tax base: the monthly gross salary (salary rate of the period, a mid period change pro rata by days);
        // absent days and joining / leaving in the month do not change it
        $tax_salary = 0;
        $total_absent_days = 0;
        $latest_salary_per_day = 0;

        // leave policy (absent / extra late / extra half day -> leave first, then salary)
        $late_halfday_deductions = $this->payroll_late_halfday_deductions($employee_id, $date_with_status);
        $absent_on_leave = [];
        foreach ($late_halfday_deductions as $deduction) {
            if ($deduction['reason'] == 'Absent' && $deduction['leave_type_id'] !== null) {
                $absent_on_leave[$deduction['date']] = true;
            }
        }

        for ($i = 0; $i < count($salary_chunk_attendance_status); $i++) {

            $each_chunk_attendance_status = array_count_values($salary_chunk_attendance_status[$i]);

            $each_chunk_late += $each_chunk_attendance_status['Late Coming'] ?? 0;

            $each_chunk_absent = $each_chunk_attendance_status['Absent'] ?? 0;
            $each_chunk_absent += $each_chunk_attendance_status['Absent Club'] ?? 0;
            $total_absent_days += $each_chunk_absent;

            $each_chunk_half_day += $each_chunk_attendance_status['Half day'] ?? 0;
            $each_chunk_present += $each_chunk_attendance_status['Present'] ?? 0;
            $each_chunk_offday += $each_chunk_attendance_status['Off Day'] ?? 0;
            $each_chunk_leave += $each_chunk_attendance_status['Leave'] ?? 0;
            $each_chunk_holiday += $each_chunk_attendance_status['Holiday'] ?? 0;

            $chunk_days = count($salary_chunk_attendance_status[$i]);
            $chunk_not_employed = count(array_intersect_key(array_flip(array_values($salary_chunk[$i])), $not_employed));

            // this salary's share of the month (the whole salary when it applies to the whole period)
            $each_chunk_salary_complete = $salary_list[$i] * $chunk_days / $period_count;
            $tax_salary += $each_chunk_salary_complete;

            // fixed day rate
            $each_chunk_salary_per_day = $salary_list[$i] / $this->payroll_days_per_month;

            // joined / left in the month: the gross is only the employed days at the day rate (never more than the share);
            // the days before joining / after leaving are not absent, they are simply not part of the gross
            $payable = $each_chunk_salary_complete;
            if ($chunk_not_employed > 0) {
                $payable = min($each_chunk_salary_complete, $each_chunk_salary_per_day * ($chunk_days - $chunk_not_employed));
            }
            $each_chunk_gross_salary += $payable;

            // absent days while employed and not covered by a leave, at the day rate; the deduction never exceeds the gross
            $chunk_absent_on_leave = count(array_intersect_key(array_flip(array_values($salary_chunk[$i])), $absent_on_leave));
            $each_chunk_absent_total = min($each_chunk_salary_per_day * max(0, $each_chunk_absent - $chunk_absent_on_leave), $payable);
            $total_each_chunk_absent += $each_chunk_absent_total;
            $total_chunk_salary_net += $payable - $each_chunk_absent_total;

            $latest_salary_per_day = $each_chunk_salary_per_day;
        }

        // extra late comings / half days with no leave left: half a day's salary each
        $late_salary_days = 0;
        $halfday_salary_days = 0;
        foreach ($late_halfday_deductions as $deduction) {
            if ($deduction['leave_type_id'] === null) {
                if ($deduction['reason'] == 'Late Coming') {
                    $late_salary_days += $deduction['days'];
                } elseif ($deduction['reason'] == 'Half day') {
                    $halfday_salary_days += $deduction['days'];
                }
            }
        }

        $final_late_day_deduction = $latest_salary_per_day * $late_salary_days;
        $final_half_day_deduction = $latest_salary_per_day * $halfday_salary_days;

        return [
            'net_salary' => max(0, $total_chunk_salary_net - $final_half_day_deduction - $final_late_day_deduction),

            'deduction_of_late' => $final_late_day_deduction,
            'deduction_of_halfday' => $final_half_day_deduction,
            'deduction_of_absent' => $total_each_chunk_absent,
            'late_halfday_deductions' => $late_halfday_deductions,

            'total_days' => count($date_with_status),
            'each_chunk_late' => $each_chunk_late,
            'each_chunk_half_day' => $each_chunk_half_day,
            'each_chunk_present' => $each_chunk_present,
            'each_chunk_offday' => $each_chunk_offday,
            'each_chunk_absent' => $total_absent_days,
            'each_chunk_leave' => $each_chunk_leave,
            'each_chunk_holiday' => $each_chunk_holiday,
            'each_chunk_gross_salary' => $each_chunk_gross_salary,
            'tax_salary' => $tax_salary,
        ];
    }
}
