<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Validator};
use Illuminate\Support\Str;
use App\Models\{Employees, SystemModule};
use App\Traits\{LoginEmployee, LogGenerateTrait};
use App\Helpers\{ActivityTracker, CustomHelper};

/**
 * Roles & Permissions: which modules each role opens and which employees management gave it.
 * Everyone has the Employee (self service) role; any other role (C-level, VP, GM, HR / Finance / IT ... Manager)
 * is given to employees one by one, and an employee's access is the merge of all their roles.
 */
class RoleController extends Controller
{
    use LoginEmployee, LogGenerateTrait;

    const MAIN_TITLE = 'roles';

    protected function index(Request $request)
    {
        try {
            $title = self::MAIN_TITLE;
            $login_user = $this->get_login_user_detail();

            $roles = DB::table('tbl_roles')
                ->select('tbl_roles.*')
                ->selectSub(DB::table('tbl_role_permissions')->whereColumn('tbl_role_permissions.role_id', 'tbl_roles.role_id')->selectRaw('COUNT(*)'), 'modules_count')
                ->selectSub(DB::table('tbl_employee_roles')->whereColumn('tbl_employee_roles.role_id', 'tbl_roles.role_id')->selectRaw('COUNT(*)'), 'employees_count')
                ->orderBy('role_id')
                ->get();

            $selected_role = $roles->firstWhere('role_id', (int) $request->input('role')) ?? $roles->first();

            $role_modules = DB::table('tbl_role_permissions')->where('role_id', $selected_role->role_id)->pluck('module_url')->toArray();
            $role_employees = DB::table('tbl_employee_roles')->where('role_id', $selected_role->role_id)->pluck('employee_id')->toArray();

            $modules = SystemModule::where('module_accessibility', 1)->orderBy('module_title')->get(['module_url', 'module_title', 'module_type'])->toArray();

            $send_data = compact('title', 'login_user', 'roles', 'selected_role', 'role_modules', 'role_employees', 'modules');
            return view('roles.roles', $send_data);
        } catch (\Exception $e) {
            $this->create_error_log('View Roles', $e);
            return view('errors.500');
        } finally {
            ActivityTracker::track_activity('View Roles');
        }
    }

    protected function update_role(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role_id' => 'required|integer',
                'role_name' => 'required|max:100',
                'role_description' => 'nullable|max:255',
                'modules' => 'nullable|array',
                'employees' => 'nullable|array',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $role = DB::table('tbl_roles')->where('role_id', $request->input('role_id'))->first();
            if (!$role) {
                return redirect()->back()->with('error', 'Role not found.');
            }

            $valid_modules = SystemModule::pluck('module_url')->toArray();
            $modules = array_values(array_unique(array_intersect((array) $request->input('modules', []), $valid_modules)));
            $employees = array_values(array_unique(array_map('intval', (array) $request->input('employees', []))));

            // the Administrator Account always keeps the Admin role
            if ($role->role_key === 'admin' && Employees::where('employee_id', 1)->exists() && !in_array(1, $employees)) {
                $employees[] = 1;
            }

            DB::transaction(function () use ($role, $request, $modules, $employees) {
                DB::table('tbl_roles')->where('role_id', $role->role_id)->update([
                    'role_name' => $request->input('role_name'),
                    'role_description' => $request->input('role_description'),
                ]);

                // Admin opens every module, nothing to store
                DB::table('tbl_role_permissions')->where('role_id', $role->role_id)->delete();
                if ($role->role_key !== 'admin') {
                    DB::table('tbl_role_permissions')->insert(array_map(function ($module_url) use ($role) {
                        return ['role_id' => $role->role_id, 'module_url' => $module_url];
                    }, $modules));
                }

                // roles belong to employees; everyone has Employee, so it keeps no list
                DB::table('tbl_employee_roles')->where('role_id', $role->role_id)->delete();
                if ($role->role_key !== 'employee') {
                    DB::table('tbl_employee_roles')->insert(array_map(function ($employee_id) use ($role) {
                        return ['employee_id' => $employee_id, 'role_id' => $role->role_id];
                    }, $employees));
                }
            });

            CustomHelper::forget_roles();
            return redirect('roles?role=' . $role->role_id)->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            $this->create_error_log('Update Role', $e);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        } finally {
            ActivityTracker::track_activity('Update Role');
        }
    }

    protected function add_role(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role_name' => 'required|max:100',
                'role_description' => 'nullable|max:255',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $key = Str::slug($request->input('role_name'), '_');
            if ($key === '' || DB::table('tbl_roles')->where('role_key', $key)->exists()) {
                return redirect()->back()->with('error', 'A role with this name already exists.');
            }

            $role_id = DB::table('tbl_roles')->insertGetId([
                'role_key' => $key,
                'role_name' => $request->input('role_name'),
                'role_description' => $request->input('role_description'),
                'role_is_system' => false,
            ]);

            return redirect('roles?role=' . $role_id)->with('success', 'Role added. Choose its modules and the employees who hold it.');
        } catch (\Exception $e) {
            $this->create_error_log('Add Role', $e);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        } finally {
            ActivityTracker::track_activity('Add Role');
        }
    }

    protected function delete_role(Request $request)
    {
        try {
            $role = DB::table('tbl_roles')->where('role_id', (int) $request->input('role_id'))->first();
            if (!$role || $role->role_is_system) {
                return redirect()->back()->with('error', 'Built-in roles cannot be deleted.');
            }

            DB::transaction(function () use ($role) {
                DB::table('tbl_role_permissions')->where('role_id', $role->role_id)->delete();
                DB::table('tbl_role_departments')->where('role_id', $role->role_id)->delete();
                DB::table('tbl_employee_roles')->where('role_id', $role->role_id)->delete();
                DB::table('tbl_roles')->where('role_id', $role->role_id)->delete();
            });

            CustomHelper::forget_roles();
            return redirect('roles')->with('success', 'Role deleted.');
        } catch (\Exception $e) {
            $this->create_error_log('Delete Role', $e);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        } finally {
            ActivityTracker::track_activity('Delete Role');
        }
    }
}
