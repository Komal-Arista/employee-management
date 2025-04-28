<?php

namespace App\Http\Controllers\Admin\Department;

use App\Http\Controllers\Controller;
use Throwable;
use Illuminate\Http\JsonResponse;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use App\Http\Requests\admin\department\CreateDepartmentRequest;
use App\Http\Requests\admin\department\UpdateDepartmentRequest;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // Base query
            $departments = Department::query()->orderBy('id', 'ASC');

                // Filter by date range
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $formattedStartDate = \DateTime::createFromFormat('m-d-Y', $request->start_date)->format('Y-m-d');
                    $formattedEndDate = \DateTime::createFromFormat('m-d-Y', $request->end_date)->format('Y-m-d');
                    $endDateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $formattedEndDate)->endOfDay();
                    $departments->whereBetween('created_at', [$formattedStartDate, $endDateTime]);
                }

                if ($request->filled('name')) {
                    $departments->where('name', 'like', '%' . $request->name . '%');
                }

                return DataTables::of($departments)

                ->editColumn('name', function($department){
                    return $department->name ?? '-';
                })

                ->editColumn('status', function ($department) {
                    return $department->status
                        ? "<span class='inline-block rounded‑full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700'>Active</span>"
                        : "<span class='inline-block rounded‑full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700'>Inactive</span>";
                })


                ->editColumn('created_at', function ($department) {
                    return $department->created_at
                    ? Carbon::parse($department->created_at)->format('d-M-Y')
                    : '-';
                })

                ->addColumn('action', function ($department) {
                    $editButton = '<a href="javascript:void(0)" data-id="' . $department->id . '" class="px-2 py-1 text-sm text-white rounded-md bg-slate-700 hover:bg-slate-600 edit-button editButton">
                        <i class="fa fa-pen"></i>
                    </a>';


                    $deleteButton = '<button data-id="'.$department->id.'" class="px-2 py-1 text-sm text-white bg-red-600 rounded-md hover:bg-red-500 deleteButton">
                                        <i class="fa fa-trash"></i>
                                    </button>';

                    $action = '<div class="btnCenter">' . $editButton . $deleteButton . ' </div>';

                    return $action;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('admin.department.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.department.create");
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(CreateDepartmentRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            Department::create($data);

            DB::commit();

            return redirect()
                ->route('admin.departments.index')
                ->with('success', 'Department created successfully!');
        } catch (Throwable $e) {
            DB::rollBack();

            // Log the exception
            Log::error('Failed to create department', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'payload'    => $data,
                'user_id'    => auth()->id(),
                'url'        => request()->fullUrl(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while saving the department. Please try again.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('admin.department.edit',[
            'department' => $department
        ]);
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

            return redirect()
                ->route('admin.departments.index')
                ->with('success', 'Department updated successfully!');
        } catch (Throwable $e) {
            DB::rollBack();

            // Log the exception
            Log::error('Failed to update department', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'payload'    => $data,
                'user_id'    => auth()->id(),
                'url'        => request()->fullUrl(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the department. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department): JsonResponse
    {
        try {
                $ok = $department->delete();

                return response()->json([
                    'status'  => $ok ? 'success' : 'failed',
                    'message' => $ok
                        ? 'Record deleted successfully.'
                        : 'Unable to delete record.',
                ], $ok ? 200 : 422);
            } catch (Throwable $e) {
            Log::error('Failed to delete department', [
                'error'   => $e->getMessage(),
                'payload' => $department->toArray(),
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
