<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalDepartments = Department::count();
        $totalEmployees = Employee::count();
        $recentEmployees = Employee::latest()->take(5)->get();

        if ($request->ajax()) {
            // Base query with department eager loaded
            $employees = Employee::with('department')->orderBy('id', 'ASC');

            return DataTables::of($employees)

                ->editColumn('name', function($employee) {
                    return $employee->name ?? '-';
                })

                ->editColumn('email', function($employee) {
                    return $employee->email ?? '-';
                })

                ->editColumn('phone', function($employee) {
                    return $employee->phone ?? '-';
                })

                ->editColumn('joining_date', function($employee) {
                    return $employee->joining_date
                                ? Carbon::parse($employee->joining_date)->format('d-M-Y')
                                : '-';
                })

                ->editColumn('department', function($employee) {
                    return $employee->department->name ?? '-';
                })

                ->editColumn('profile_photo', function($employee) {
                    if ($employee->profile_photo) {
                        return '<img src="' . asset('storage/' . $employee->profile_photo) . '" alt="Profile" class="object-cover w-10 h-10 rounded-full">';
                    }
                    return '-';
                })

                ->editColumn('created_at', function($employee) {
                    return $employee->created_at
                        ? Carbon::parse($employee->created_at)->format('d-M-Y')
                        : '-';
                })

                ->addColumn('action', function($employee) {
                    $editButton = '<a href="javascript:void(0)" data-id="' . $employee->id . '" class="px-2 py-1 text-sm text-white rounded-md bg-slate-700 hover:bg-slate-600 editButton">
                        <i class="fa fa-pen"></i>
                    </a>';

                    $deleteButton = '<button data-id="' . $employee->id . '" class="px-2 py-1 text-sm text-white bg-red-600 rounded-md hover:bg-red-500 deleteButton">
                        <i class="fa fa-trash"></i>
                    </button>';

                    return '<div class="btnCenter">' . $editButton . $deleteButton . '</div>';
                })

                ->rawColumns(['action', 'profile_photo']) // Allow HTML rendering
                ->make(true);
        }

        return view('admin.dashboard', compact('totalDepartments', 'totalEmployees', 'recentEmployees'));
    }
}
