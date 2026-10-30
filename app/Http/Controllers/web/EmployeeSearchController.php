<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employees;
use App\Helpers\CustomHelper;
use App\Traits\{LoginEmployee, LogGenerateTrait};

/**
 * Search endpoint behind the employee dropdowns (select2 ajax), so pages no longer
 * render every employee as an <option>.
 *
 * scope:  team     - HR / Admin roles see everyone, managers their hierarchy, others themselves
 *         active   - every employee
 *         managers - employees marked as manager
 * status: 1 active (default), 0 inactive, all
 * departments: optional department ids to search in
 * ids:    comma separated ids to resolve labels of already selected values
 */
class EmployeeSearchController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const PAGE_SIZE = 20;

    protected function search(Request $request)
    {
        try {
            $login_user = $this->get_login_user_detail();
            if (empty($login_user)) {
                return response()->json(['results' => [], 'pagination' => ['more' => false]]);
            }

            $query = Employees::query();

            $status = $request->input('status', '1');
            if ($status !== 'all') {
                $query->where('employee_isactive', $status == '0' ? 0 : 1);
            }

            $scope = $request->input('scope', 'team');
            $is_hr = CustomHelper::has_role($login_user[0]['employee_id'], 'hr');
            if ($scope === 'managers') {
                $query->where('employee_is_manager', 1);
            } elseif ($scope === 'team' && !$is_hr) {
                if ($login_user[0]['employee_is_manager'] == 1) {
                    $team = $this->get_employee_hierarchy_unique($login_user[0]['employee_id'], $status === 'all' ? 1 : ($status == '0' ? 0 : 1));
                    $team[] = $login_user[0]['employee_id'];
                    $query->whereIn('employee_id', $team);
                } else {
                    $query->where('employee_id', $login_user[0]['employee_id']);
                }
            }

            // optional: only these departments (e.g. the departments picked next to the dropdown)
            $departments = array_filter(array_map('intval', (array) $request->input('departments', [])));
            if (!empty($departments)) {
                $query->whereIn('employee_department', $departments);
            }

            if ($request->filled('ids')) {
                $ids = array_filter(array_map('intval', explode(',', $request->input('ids'))));
                $rows = $query->whereIn('employee_id', $ids)->get(['employee_id', 'employee_code', 'employee_name']);
                return response()->json(['results' => $this->format($rows), 'pagination' => ['more' => false]]);
            }

            $term = trim((string) $request->input('q'));
            if ($term !== '') {
                $query->where(function ($query) use ($term) {
                    $query->where('employee_name', 'like', '%' . $term . '%')
                        ->orWhere('employee_code', 'like', '%' . $term . '%');
                });
            }

            $page = max(1, (int) $request->input('page', 1));
            $rows = $query->orderBy('employee_name')
                ->offset(($page - 1) * self::PAGE_SIZE)->limit(self::PAGE_SIZE + 1)
                ->get(['employee_id', 'employee_code', 'employee_name']);

            return response()->json([
                'results' => $this->format($rows->take(self::PAGE_SIZE)),
                'pagination' => ['more' => $rows->count() > self::PAGE_SIZE],
            ]);
        } catch (\Exception $e) {
            $this->create_error_log('Employee Search', $e);
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }
    }

    private function format($rows)
    {
        return $rows->map(function ($row) {
            return ['id' => $row->employee_id, 'text' => $row->employee_code . ' - ' . $row->employee_name];
        })->values();
    }
}
