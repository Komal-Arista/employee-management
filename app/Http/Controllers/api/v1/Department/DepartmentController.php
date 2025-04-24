<?php

namespace App\Http\Controllers\api\v1\Department;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Resources\Department\DepartmentCollection;
use App\Http\Requests\admin\department\CreateDepartmentRequest;
use App\Http\Requests\admin\department\UpdateDepartmentRequest;
use App\Http\Resources\Department\DepartmentResource;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 2);

        // Build the paginated query
        $departments = Department::orderBy('id')
                       ->paginate($perPage)
                       ->withQueryString();

        // Return the custom collection
        return new DepartmentCollection($departments);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(CreateDepartmentRequest $request)
    {
        $data = $request->validated();
        try {

            DB::beginTransaction();

            $department = Department::create($data);

            DB::commit();


            return (new DepartmentResource($department))
                    ->additional(['message' => 'Department created successfully.'])
                    ->response()
                    ->setStatusCode(201);

        } catch (Throwable $e) {
            DB::rollBack();

            // Log the exception
            Log::error('Failed to create department', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'payload'    => $data,
                'user_id'    => auth()->id() ?? null,
                'url'        => request()->fullUrl(),
            ]);

            // Send a safe JSON error payload
            return response()->json([
                'message' => 'Unable to create department at this time.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
    */
    public function show($id)
    {
        $department = Department::find($id);

        if (! $department) {
            return response()->json([
                'message' => 'Department not found.',
            ], 404);
        }

        return new DepartmentResource($department);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $data = $request->validated();

        try {

            DB::beginTransaction();
            $department->update($data);
            DB::commit();

            return (new DepartmentResource($department))
                    ->response()
                    ->setStatusCode(200);

        } catch (Throwable $e) {

            DB::rollBack();

            // Log the exception
            Log::error('Failed to update department', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'payload' => $data,
                'user_id' => auth()->id() ?? null,
                'url'     => request()->fullUrl(),
            ]);

            return response()->json([
                'message' => 'Unable to update department at this time.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
    */
    public function destroy(Department $department)
    {
        try {

            DB::beginTransaction();
            $department->delete();
            DB::commit();

            return response()->json([
                'message' => 'Department deleted successfully.',
            ], 200);          // 200

        } catch (Throwable $e) {
            Log::error('Failed to delete department', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'dept_id' => $department->id ?? null,
                'user_id' => auth()->id() ?? null,
                'url'     => request()->fullUrl(),
            ]);

            return response()->json([
                'message' => 'Unable to delete department at this time.',
            ], 500);
        }
    }

}
