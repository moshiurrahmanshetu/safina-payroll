<?php

namespace App\Http\Controllers;

use App\Models\EmployeeShift;
use App\Models\User;
use App\Models\Shift;
use App\Services\EmployeeShiftService;
use Illuminate\Http\Request;

class EmployeeShiftController extends Controller
{
    protected $employeeShiftService;

     public function __construct(EmployeeShiftService $employeeShiftService)
{
    parent::__construct();

    $this->employeeShiftService = $employeeShiftService;
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employeeShifts = EmployeeShift::with(['user', 'shift', 'creator', 'updater'])
                                       ->orderBy('effective_from', 'desc')
                                       ->get();

        return view('admin.employee_shifts.index', compact('employeeShifts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        $employees = User::where('salary_processing', 1)
                         ->where('status', 1)
                         ->pluck('name', 'id');

        $shifts = Shift::where('status', 'Active')
                      ->pluck('name', 'id');

        return view('admin.employee_shifts.create', compact('employees', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'is_default' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        try {
            $employeeShift = $this->employeeShiftService->assignShift(
                $validated['user_id'],
                $validated['shift_id'],
                $validated['effective_from'],
                $validated['effective_to'] ?? null,
                $request->has('is_default'),
                $validated['remarks'] ?? null,
                auth()->id()
            );

            return redirect()->route('employee_shifts.index')
                            ->with('success', 'Employee shift assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', $e->getMessage());
        }
    }

   

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employeeShift = EmployeeShift::with(['user', 'shift', 'creator', 'updater'])
                                     ->findOrFail($id);

        return view('admin.employee_shifts.show', compact('employeeShift'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeeShift = EmployeeShift::findOrFail($id);

        $employees = User::where('salary_processing', 1)
                         ->where('status', 'Active')
                         ->get()
                         ->pluck('name', 'id');

        $shifts = Shift::where('status', 'Active')
                      ->pluck('name', 'id');

        return view('admin.employee_shifts.edit', compact('employeeShift', 'employees', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'is_default' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        $employeeShift = EmployeeShift::findOrFail($id);

        try {
            $employeeShift = $this->employeeShiftService->updateShift(
                $employeeShift,
                [
                    'user_id' => $validated['user_id'],
                    'shift_id' => $validated['shift_id'],
                    'effective_from' => $validated['effective_from'],
                    'effective_to' => $validated['effective_to'] ?? null,
                    'is_default' => $request->has('is_default'),
                    'remarks' => $validated['remarks'] ?? null,
                ],
                auth()->id()
            );

            return redirect()->route('employee_shifts.index')
                            ->with('success', 'Employee shift updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employeeShift = EmployeeShift::findOrFail($id);

        try {
            $this->employeeShiftService->destroyShift($employeeShift, auth()->id());

            return redirect()->route('employee_shifts.index')
                            ->with('success', 'Employee shift deactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', $e->getMessage());
        }
    }
}
