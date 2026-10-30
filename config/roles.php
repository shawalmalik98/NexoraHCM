<?php

/*
| Built-in roles and the modules each one opens by default.
| Every employee has the Employee role (self service). Every other role is given to employees one by one by
| management in Roles & Permissions (never by department); an employee can hold several roles.
| Super Admin (admin) always has complete access, so it lists no modules.
| Changes made in Roles & Permissions are kept; these defaults only fill a role the first time it is created.
| More roles (e.g. one manager role per department) are added from the Roles & Permissions screen.
|
| Role keys hr / finance / manager also decide whose records a screen shows: hr and finance see every employee in
| their modules, everyone else (manager, GM, VP, C-level ...) sees their own reporting line.
*/

$team = [
    '/attendance', '/attendance-requests', '/wfh-requests', '/employees-leaves', '/shift-management', '/shift-time-request',
    '/overtime-management', '/employee-expense', '/requisition', '/resignations', '/meetings',
];
$leadership = array_merge($team, ['/reports', '/daily-attendance-report', '/monthly-attendance', '/employees-managers']);

return [

    'defaults' => [

        'admin' => [
            'name' => 'Super Admin',
            'description' => 'Complete access to every module, employee and setting',
            'modules' => [],
        ],

        // C-level
        'ceo' => ['name' => 'CEO', 'description' => 'Chief Executive Officer', 'modules' => $leadership],
        'coo' => ['name' => 'COO', 'description' => 'Chief Operating Officer', 'modules' => $leadership],
        'cto' => ['name' => 'CTO', 'description' => 'Chief Technology Officer', 'modules' => $leadership],
        'cio' => ['name' => 'CIO', 'description' => 'Chief Information Officer', 'modules' => $leadership],

        // executives
        'vp' => ['name' => 'VP', 'description' => 'Vice President', 'modules' => $leadership],
        'general_manager' => ['name' => 'General Manager', 'description' => 'General Manager', 'modules' => $leadership],

        // department managers
        'hr' => [
            'name' => 'HR Manager',
            'description' => 'Employees, attendance, leaves, shifts and HR requests of every employee',
            'modules' => [
                '/employees', '/employees-managers', '/employee-birthdays', '/employee-work-anniversay', '/gadgets-inventory', '/gadgets-allotment',
                '/meetings', '/attendance', '/attendance-requests', '/daily-attendance-report', '/wfh-requests', '/employees-leaves', '/manage-leaves',
                '/interviews', '/employee-onboarding', '/holidays', '/departments', '/designations', '/vehicles', '/resignations', '/alerts',
                '/requisition', '/letters', '/all-tickets', '/rooms', '/locations', '/candiddate-interviews', '/employee-devices', '/linked-in-post',
                '/shift-management', '/overtime-management', '/organogram', '/employee-letter', '/letter-template', '/machine-attendance-logs',
                '/leave-management', '/polling', '/shift-time-request', '/monthly-attendance', '/employment-status', '/attendance-devices',
                '/reports', '/attendance-locker', '/employee-group', '/leave-policy', '/employee-location', '/attendance-adjustment',
            ],
        ],

        'finance' => [
            'name' => 'Finance Manager',
            'description' => 'Payroll, salary, loans, expenses and tax of every employee',
            'modules' => [
                '/payroll', '/petty-cash', '/daily-expense', '/loans', '/all-salary-request', '/all-loan-request', '/tax-slab', '/tax-collection',
                '/employee-expense', '/overtime-management', '/employee-bank', '/daily-wages-payroll', '/deduction-policy', '/funds', '/employee-fund',
            ],
        ],

        'admin_manager' => ['name' => 'Admin Manager', 'description' => 'Office administration and the team\'s approvals', 'modules' => array_merge($team, ['/gadgets-inventory', '/gadgets-allotment', '/vehicles', '/rooms', '/all-tickets'])],
        'it_manager' => ['name' => 'IT Manager', 'description' => 'IT and the team\'s approvals', 'modules' => array_merge($team, ['/all-tickets', '/employee-devices'])],
        'development_manager' => ['name' => 'Development Manager', 'description' => 'The development team\'s approvals', 'modules' => $team],

        'manager' => [
            'name' => 'Manager',
            'description' => 'Approvals and records of the employee\'s team',
            'modules' => $team,
        ],

        'employee' => [
            'name' => 'Employee',
            'description' => 'Self service, every employee has it',
            'modules' => [
                '/dashboard', '/my-attendance', '/my-attendance-request', '/my-leaves', '/my-interviews', '/payslip', '/my-meetings',
                '/my-wfh-requests', '/employee-resignation', '/my-salary-request', '/my-loan', '/my-tickets', '/my-requisition', '/my-expense',
                '/my-shift-time', '/employee-birthdays', '/employee-work-anniversay', '/organogram',
            ],
        ],
    ],
];
