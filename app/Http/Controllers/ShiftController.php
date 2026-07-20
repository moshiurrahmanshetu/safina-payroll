<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Shift;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Shift::query();

        // Search by shift name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $shifts = $query->orderBy('name', 'asc')->paginate(10);

        return view('admin.shifts.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.shifts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'late_grace_minutes' => 'integer|min:0',
            'early_leave_grace_minutes' => 'integer|min:0',
            'auto_checkout_after_minutes' => 'nullable|integer|min:0',
            'status' => 'required|in:Active,Inactive',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        
        // Determine if cross-day shift
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $data['is_cross_day'] = ($endTime < $startTime);

        $data['created_by'] = Auth::user()->id;
        $data['updated_by'] = Auth::user()->id;

        Shift::create($data);

        return redirect()->route('shifts.index')->with('flash_success', 'Shift created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shift = Shift::with(['creator', 'updater'])->findOrFail($id);
        return view('admin.shifts.show', compact('shift'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        return view('admin.shifts.edit', compact('shift'));
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'late_grace_minutes' => 'integer|min:0',
            'early_leave_grace_minutes' => 'integer|min:0',
            'auto_checkout_after_minutes' => 'nullable|integer|min:0',
            'status' => 'required|in:Active,Inactive',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $shift = Shift::findOrFail($id);
        $data = $request->all();
        
        // Determine if cross-day shift
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $data['is_cross_day'] = ($endTime < $startTime);

        $data['updated_by'] = Auth::user()->id;

        $shift->update($data);

        return redirect()->route('shifts.index')->with('flash_success', 'Shift updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->route('shifts.index')->with('flash_success', 'Shift deleted successfully');
    }
}
