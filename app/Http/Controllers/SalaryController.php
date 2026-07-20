<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalaryController extends Controller
{
    /**
     * Display a listing of salary history.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Salary::with(['user', 'creator'])->orderBy('id', 'desc');

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply user filter
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Apply current filter
        if ($request->has('is_current') && $request->is_current !== '') {
            $query->where('is_current', $request->is_current);
        }

        $salaries = $query->get();

        return view('admin.salaries.index', compact('salaries'));
    }

    /**
     * Show the form for creating a new salary revision.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get users with salary_processing = 1
        $users = User::where('salary_processing', 1)->where('status', 1)->get();
        
        return view('admin.salaries.create', compact('users'));
    }

    /**
     * Store a newly created salary revision.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'user_id' => 'required|exists:users,id',
            'basic_salary' => 'required|numeric|min:0',
            'house_rent' => 'nullable|numeric|min:0',
            'medical' => 'nullable|numeric|min:0',
            'transport' => 'nullable|numeric|min:0',
            'food' => 'nullable|numeric|min:0',
            'mobile' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'festival_bonus' => 'nullable|numeric|min:0',
            'late_fine' => 'nullable|numeric|min:0',
            'absent_deduction' => 'nullable|numeric|min:0',
            'advance_salary' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'pf' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'salary_increment_reason' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Business Rule 3: Set previous current salary is_current = 0
        Salary::where('user_id', $data['user_id'])
              ->where('is_current', 1)
              ->update(['is_current' => 0]);

        // Create new salary revision with is_current = 1
        $salary = new Salary();
        $salary->user_id = $data['user_id'];
        $salary->basic_salary = $data['basic_salary'] ?? 0;
        $salary->house_rent = $data['house_rent'] ?? 0;
        $salary->medical = $data['medical'] ?? 0;
        $salary->transport = $data['transport'] ?? 0;
        $salary->food = $data['food'] ?? 0;
        $salary->mobile = $data['mobile'] ?? 0;
        $salary->other_allowance = $data['other_allowance'] ?? 0;
        $salary->festival_bonus = $data['festival_bonus'] ?? 0;
        $salary->late_fine = $data['late_fine'] ?? 0;
        $salary->absent_deduction = $data['absent_deduction'] ?? 0;
        $salary->advance_salary = $data['advance_salary'] ?? 0;
        $salary->tax = $data['tax'] ?? 0;
        $salary->pf = $data['pf'] ?? 0;
        $salary->other_deduction = $data['other_deduction'] ?? 0;
        $salary->effective_from = $data['effective_from'];
        $salary->salary_increment_reason = $data['salary_increment_reason'];
        $salary->remarks = $data['remarks'] ?? null;
        $salary->is_current = 1;
        $salary->revision_locked = 0;
        $salary->salary_locked = 0;
        $salary->status = 1;
        $salary->created_by = auth()->user()->id;
        $salary->updated_by = auth()->user()->id;
        $salary->save();

        return redirect()->route('salaries.index')->with('flash_success', 'Salary revision created successfully');
    }

    /**
     * Display the specified salary history.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $salary = Salary::with(['user', 'creator', 'updater'])->findOrFail($id);
        
        return view('admin.salaries.show', compact('salary'));
    }

    /**
     * Show the form for editing the specified salary.
     * REDIRECTED: Direct editing is not allowed. Use salary revisions instead.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Business Rule: Direct salary editing is not allowed
        // HR must create a new salary revision instead
        return redirect()->route('salary_revisions.create')
                        ->with('flash_info', 'Direct salary editing is not allowed. Please create a new salary revision to modify salary.');
    }

    /**
     * Update the specified salary.
     * REDIRECTED: Direct editing is not allowed. Use salary revisions instead.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Business Rule: Direct salary editing is not allowed
        // HR must create a new salary revision instead
        return redirect()->route('salary_revisions.create')
                        ->with('flash_info', 'Direct salary editing is not allowed. Please create a new salary revision to modify salary.');
    }

    /**
     * Remove the specified salary.
     * Only allowed if not locked and not current.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $salary = Salary::findOrFail($id);
        
        // Cannot delete locked salary
        if ($salary->revision_locked) {
            return redirect()->back()->withErrors(['revision_locked' => 'Locked salary cannot be deleted'])->withInput();
        }

        // Cannot delete current salary
        if ($salary->is_current) {
            return redirect()->back()->withErrors(['is_current' => 'Current salary cannot be deleted'])->withInput();
        }

        $salary->delete();

        return redirect()->route('salaries.index')->with('flash_success', 'Salary deleted successfully');
    }

    /**
     * Display salary timeline for a specific employee.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function timeline($user_id)
    {
        $user = User::findOrFail($user_id);
        $salaries = Salary::where('user_id', $user_id)
                          ->orderBy('effective_from', 'desc')
                          ->with(['creator', 'updater'])
                          ->get();
        
        return view('admin.salaries.timeline', compact('user', 'salaries'));
    }

    /**
     * Lock the specified salary.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lock($id)
    {
        $salary = Salary::findOrFail($id);
        $salary->revision_locked = 1;
        $salary->updated_by = auth()->user()->id;
        $salary->save();

        return redirect()->back()->with('flash_success', 'Salary locked successfully');
    }

    /**
     * Unlock the specified salary.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unlock($id)
    {
        $salary = Salary::findOrFail($id);
        $salary->revision_locked = 0;
        $salary->updated_by = auth()->user()->id;
        $salary->save();

        return redirect()->back()->with('flash_success', 'Salary unlocked successfully');
    }
}
