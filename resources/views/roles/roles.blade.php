@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div class="app-container col-12 d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Roles &amp; Permissions</h1>
                <span class="text-muted fs-7 mt-1">Every employee has the Employee (self service) role. Management gives any other role to employees one by one; an employee can hold several roles and gets every module of all of them.</span>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add_role_modal">Add Role</a>
            </div>
        </div>
    </div>

    <div class="app-content flex-column-fluid">
        <div class="app-container">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row g-5">

                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body p-4">
                            @foreach($roles as $role)
                                <a href="{{ url('roles?role='.$role->role_id) }}" class="d-block rounded p-3 mb-2 text-gray-800 text-hover-primary @if($role->role_id == $selected_role->role_id) bg-light-primary @endif">
                                    <div class="fw-bold fs-6">{{ $role->role_name }} @if(!$role->role_is_system)<span class="badge badge-light ms-1">Custom</span>@endif</div>
                                    <div class="text-muted fs-8">
                                        @if($role->role_key === 'admin') Complete access @else {{ $role->modules_count }} modules @endif
                                        &middot; @if($role->role_key === 'employee') everyone @else {{ $role->employees_count }} employees @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <form method="POST" action="{{ url('roles/update-role') }}" class="card">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $selected_role->role_id }}" />
                        <div class="card-body">

                            <div class="row mb-6">
                                <div class="col-md-5">
                                    <label class="required fs-6 fw-semibold mb-2">Role Name</label>
                                    <input type="text" class="form-control" name="role_name" value="{{ $selected_role->role_name }}" maxlength="100" required />
                                </div>
                                <div class="col-md-7">
                                    <label class="fs-6 fw-semibold mb-2">Description</label>
                                    <input type="text" class="form-control" name="role_description" value="{{ $selected_role->role_description }}" maxlength="255" />
                                </div>
                            </div>

                            @if($selected_role->role_key !== 'employee')
                                <div class="mb-6">
                                    <label class="fs-6 fw-semibold mb-2">Employees</label>
                                    <div class="text-muted fs-7 mb-2">
                                        The employees management gives this role to (any department).
                                    </div>
                                    <select class="form-select" name="employees[]" multiple data-employee-select data-scope="active" data-placeholder="Search employees">
                                        @include('include.employee-selected-options', ['selected' => $role_employees])
                                    </select>
                                </div>
                            @else
                                <div class="alert alert-light-primary mb-6">Every employee has this role.</div>
                            @endif

                            <div class="mb-2 d-flex flex-stack">
                                <label class="fs-6 fw-semibold">Modules</label>
                                @if($selected_role->role_key !== 'admin')
                                    <div>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-light me-2" onclick="$('.role-module').prop('checked', true)">Select all</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-light" onclick="$('.role-module').prop('checked', false)">Clear</a>
                                    </div>
                                @endif
                            </div>

                            @if($selected_role->role_key === 'admin')
                                <div class="alert alert-light-primary">Super Admin has complete access: every module (including modules switched off for others), every employee and every setting. The Administrator Account always keeps this role.</div>
                            @else
                                @foreach([['0', 'Self Service'], ['1', 'Management']] as [$module_type, $module_group])
                                    <div class="fw-bold text-gray-600 fs-7 text-uppercase mt-4 mb-3">{{ $module_group }}</div>
                                    <div class="row">
                                        @foreach($modules as $module)
                                            @if((string) $module['module_type'] === $module_type)
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input role-module" type="checkbox" name="modules[]" value="{{ $module['module_url'] }}" @if(in_array($module['module_url'], $role_modules)) checked @endif />
                                                        <span class="form-check-label">{{ $module['module_title'] }}</span>
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif

                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                @if(!$selected_role->role_is_system)
                                    <button type="submit" form="delete_role_form" class="btn btn-light-danger" onclick="return confirm('Delete this role? Employees lose the modules it gave them.')">Delete Role</button>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ url('roles/delete-role') }}" id="delete_role_form">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $selected_role->role_id }}" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ url('roles/add-role') }}">
    @csrf
    <div class="modal fade" id="add_role_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Role</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <label class="required fs-6 fw-semibold mb-2">Role Name</label>
                    <input type="text" class="form-control mb-5" name="role_name" maxlength="100" required />
                    <label class="fs-6 fw-semibold mb-2">Description</label>
                    <input type="text" class="form-control" name="role_description" maxlength="255" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
