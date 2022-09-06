<?php

namespace App\Http\Controllers\API\Users;

# controllers
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Auth\AuthController;

# requests
use App\Http\Requests\Employees\EmployeeRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\RegisterRequest;

# models
use App\Models\Users\Employee;

class EmployeesController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    public function store(EmployeeRequest $request)
    {
        $request->merge([
            'username' => pick_username($request->name),
            'password' => $request->password,
        ]);
        
        $user = (new AuthController)
            ->register(new RegisterRequest(array_merge($request->all(), ['role' => 'employee'])), false)
            ->original;
        
        $employee = $user->employee()->set_roles($request->role_ids);
        return response()->json($employee);
    }

    public function update(Employee $employee, EmployeeRequest $request)
    {
        $employee->update($request->only(['name', 'email', 'phone']));
        $employee->set_roles($request->role_ids);
        return response()->json($employee);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(null);
    }

    public function changePassword(Employee $employee, ChangePasswordRequest $request)
    {
        // if (Hash::check($request->oldPassword, $employee->password))
        // {
        $employee->update(['password' => bcrypt($request->confirmNewPassword)]);
        return response()->json(null, 204);
        // }

        // return response()->json(['errors' => ['oldPassword' => ['wrong']]], 422);
    }
}
