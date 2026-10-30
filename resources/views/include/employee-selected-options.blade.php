{{-- Options of an employee dropdown backed by /employee-search: only the selected employee(s) are rendered. Expects $selected (id, ids array or null). --}}
@php
    $selected_employee_ids = array_values(array_filter((array) ($selected ?? []), function ($id) { return $id !== null && $id !== ''; }));
    $selected_employees = empty($selected_employee_ids) ? [] : \App\Models\Employees::whereIn('employee_id', $selected_employee_ids)->get(['employee_id', 'employee_code', 'employee_name'])->toArray();
@endphp
@foreach ($selected_employees as $selected_employee)
    <option value="{{ $selected_employee['employee_id'] }}" selected>{{ $selected_employee['employee_code'] }} - {{ $selected_employee['employee_name'] }}</option>
@endforeach
