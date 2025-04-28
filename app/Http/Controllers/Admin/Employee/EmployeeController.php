<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use Throwable;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\Employee\CreateEmployeeRequest;
use App\Http\Requests\Admin\Employee\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Base query with department eager loaded
            $employees = Employee::with('department')->orderBy('id', 'ASC');

            // Filter by date range (joining date or created_at)
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $formattedStartDate = \DateTime::createFromFormat('m-d-Y', $request->start_date)->format('Y-m-d');
                $formattedEndDate = \DateTime::createFromFormat('m-d-Y', $request->end_date)->format('Y-m-d');
                $endDateTime = Carbon::createFromFormat('Y-m-d', $formattedEndDate)->endOfDay();
                $employees->whereBetween('joining_date', [$formattedStartDate, $endDateTime]);
            }

            // Filter by employee name
            if ($request->filled('name')) {
                $employees->where('name', 'like', '%' . $request->name . '%');
            }

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

        return view('admin.employee.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('status', '1')->pluck('name', 'id');
        return view('admin.employee.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEmployeeRequest $request)
    {
        $data = $request->validated();
        $uploadedProfilePhotoPath = null; // Track uploaded file path separately

        try {
            DB::beginTransaction();

            if ($request->hasFile('profile_photo')) {
                $uploadedProfilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
                $data['profile_photo'] = $uploadedProfilePhotoPath;
            }

            Employee::create($data);

            DB::commit();

            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'Employee created successfully.');
        } catch (Throwable $e) {
            DB::rollBack();

            // If a profile photo was uploaded, delete it
            if ($uploadedProfilePhotoPath && Storage::disk('public')->exists($uploadedProfilePhotoPath)) {
                Storage::disk('public')->delete($uploadedProfilePhotoPath);
            }

            // Log the exception
            Log::error('Failed to create employee', [
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'payload'     => $data,
                'user_id'     => auth()->id(),
                'url'         => request()->fullUrl(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while saving the employee. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $departments = Department::where('status', '1')->pluck('name', 'id');
        return view('admin.employee.edit', compact('employee', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();
        $oldProfilePhoto = $employee->profile_photo;
        $uploadedProfilePhotoPath = null; // Track uploaded file path separately

        try {
            DB::beginTransaction();

            if ($request->hasFile('profile_photo')) {
                // Upload new photo
                $uploadedProfilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
                $data['profile_photo'] = $uploadedProfilePhotoPath;

                // Delete old photo if it exists
                if ($oldProfilePhoto && Storage::disk('public')->exists($oldProfilePhoto)) {
                    Storage::disk('public')->delete($oldProfilePhoto);
                }
            }

            // Format joining_date if provided
            if (isset($data['joining_date'])) {
                $data['joining_date'] = Carbon::parse($data['joining_date'])->format('Y-m-d');
            }

            // Update employee
            $employee->update($data);

            DB::commit();

            return redirect()
                ->route('admin.employees.index')
                ->with('success', 'Employee updated successfully.');

        } catch (Throwable $e) {
            DB::rollBack();

            // If a new photo was uploaded but something failed, delete the new photo
            if ($uploadedProfilePhotoPath && Storage::disk('public')->exists($uploadedProfilePhotoPath)) {
                Storage::disk('public')->delete($uploadedProfilePhotoPath);
            }

            // Log the error
            Log::error('Failed to update employee', [
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'employee_id' => $employee->id,
                'payload'     => $data,
                'user_id'     => auth()->id(),
                'url'         => request()->fullUrl(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the employee. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): JsonResponse
    {
        try {
            // Delete profile photo if exists
            if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
                Storage::disk('public')->delete($employee->profile_photo);
            }

            $ok = $employee->delete();

            return response()->json([
                'status'  => $ok ? 'success' : 'failed',
                'message' => $ok
                    ? 'Employee deleted successfully.'
                    : 'Unable to delete record.',
            ], $ok ? 200 : 422);
        } catch (Throwable $e) {
            Log::error('Failed to delete department', [
                'error'   => $e->getMessage(),
                'payload' => $employee->toArray(),
                'user'    => auth()->id(),
                'url'     => request()->fullUrl(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
