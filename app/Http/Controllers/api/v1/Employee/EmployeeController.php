<?php

namespace App\Http\Controllers\api\v1\Employee;

use App\Http\Controllers\Controller;
use Throwable;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Employee\EmployeeCollection;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Requests\Admin\Employee\CreateEmployeeRequest;
use App\Http\Requests\Admin\Employee\UpdateEmployeeRequest;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 2);

        // Build the paginated query
        $departments = Employee::with('department')->orderBy('id')
                       ->paginate($perPage)
                       ->withQueryString();

        // Return the custom collection
        return new EmployeeCollection($departments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEmployeeRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            $employee = Employee::create($data);

            // Reload employee with department to show department details in API call
            $employee->load('department');

            DB::commit();

            return (new EmployeeResource($employee))
                ->additional(['message' => 'Employee created successfully.'])
                ->response()
                ->setStatusCode(201);

        } catch (Throwable $e) {
            DB::rollBack();

            // Log the exception
            Log::error('Failed to create employee', [
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'payload'     => $data,
                'user_id'     => auth()->id() ?? null,
                'url'         => request()->fullUrl(),
            ]);

            return response()->json([
                'message' => 'Unable to create employee at this time.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return response()->json([
                'message' => 'Employee not found.',
            ], 404);
        }

        return (new EmployeeResource($employee))
        ->additional(['message' => 'Employee searched successfully.'])
        ->response()
        ->setStatusCode(201);
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

            // Update employee
            $employee->update($data);

            // Reload employee with department to show department details in API call
            $employee->load('department');

            DB::commit();

            return (new EmployeeResource($employee))
                    ->additional(['message' => 'Employee updated successfully.'])
                    ->response()
                    ->setStatusCode(200);

        } catch (Throwable $e) {

            DB::rollBack();

            // Log the exception
            Log::error('Failed to update employee', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'payload' => $data,
                'user_id' => auth()->id() ?? null,
                'url'     => request()->fullUrl(),
            ]);

            return response()->json([
                'message' => 'Unable to update employee at this time.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {

            DB::beginTransaction();
            $employee->delete();
            DB::commit();

            return response()->json([
                'message' => 'Employee deleted successfully.',
            ], 200);          // 200

        } catch (Throwable $e) {
            Log::error('Failed to delete employee', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'dept_id' => $employee->id ?? null,
                'user_id' => auth()->id() ?? null,
                'url'     => request()->fullUrl(),
            ]);

            return response()->json([
                'message' => 'Unable to delete employee at this time.',
            ], 500);
        }
    }
}
