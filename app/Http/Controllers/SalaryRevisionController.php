<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\SalaryStructure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalaryRevisionController extends Controller
{
    /**
     * Display a listing of salary revisions.
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

        $salaryRevisions = $query->get();

        return view('admin.salary_revisions.index', compact('salaryRevisions'));
    }

    /**
     * Show the form for creating a new salary revision.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get users with salary_processing = 1 and status = 1
        $users = User::where('salary_processing', 1)
                     ->where('status', 1)
                     ->orderBy('name', 'asc')
                     ->pluck('name', 'id');

        return view('admin.salary_revisions.create', compact('users'));
    }

    /**
     * Store a newly created salary revision.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->all();

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
            'revision_reason' => 'required|string|max:255',
            'salary_locked' => 'nullable|boolean',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if effective_from is duplicate for same employee
        $duplicateEffectiveFrom = Salary::where('user_id', $data['user_id'])
                                       ->where('effective_from', $data['effective_from'])
                                       ->exists();
        if ($duplicateEffectiveFrom) {
            return redirect()->back()->withErrors(['effective_from' => 'Effective date already exists for this employee'])->withInput();
        }

        // Check if user's current salary is locked
        $currentSalary = Salary::where('user_id', $data['user_id'])
                              ->where('is_current', 1)
                              ->first();
        if ($currentSalary && $currentSalary->salary_locked) {
            return redirect()->back()->withErrors(['salary_locked' => 'Current salary is locked. Unlock it before creating new revision.'])->withInput();
        }

        $user_id = Auth::user()->id;

        // Use transaction for data integrity
        DB::beginTransaction();
        try {
            // Set previous current salary is_current = 0
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
            $salary->salary_increment_reason = $data['revision_reason'];
            $salary->remarks = $data['remarks'] ?? null;
            $salary->is_current = 1;
            $salary->revision_locked = 0;
            $salary->salary_locked = $data['salary_locked'] ?? 0;
            $salary->status = 1;
            $salary->created_by = $user_id;
            $salary->updated_by = $user_id;
            $salary->save();

            // Update salary_structures to latest salary
            $this->updateSalaryStructure($data['user_id'], $salary);

            DB::commit();

            return redirect()->route('salary_revisions.index')->with('flash_success', 'Salary revision created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error creating salary revision: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified salary revision.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $salary = Salary::with(['user', 'creator', 'updater'])->findOrFail($id);

        // Get previous salary for comparison
        $previousSalary = Salary::where('user_id', $salary->user_id)
                                 ->where('effective_from', '<', $salary->effective_from)
                                 ->orderBy('effective_from', 'desc')
                                 ->first();

        return view('admin.salary_revisions.show', compact('salary', 'previousSalary'));
    }

    /**
     * Update salary_structures to latest salary
     *
     * @param  int  $userId
     * @param  \App\Models\Salary  $salary
     * @return void
     */
    private function updateSalaryStructure($userId, $salary)
    {
        $salaryStructure = SalaryStructure::where('user_id', $userId)->first();

        if ($salaryStructure) {
            // Update existing salary structure
            $salaryStructure->basic_salary = $salary->basic_salary;
            $salaryStructure->house_rent = $salary->house_rent;
            $salaryStructure->medical = $salary->medical;
            $salaryStructure->transport = $salary->transport;
            $salaryStructure->food = $salary->food;
            $salaryStructure->mobile = $salary->mobile;
            $salaryStructure->other_allowance = $salary->other_allowance;
            $salaryStructure->festival_bonus = $salary->festival_bonus;
            $salaryStructure->late_fine = $salary->late_fine;
            $salaryStructure->absent_deduction = $salary->absent_deduction;
            $salaryStructure->advance_salary = $salary->advance_salary;
            $salaryStructure->tax = $salary->tax;
            $salaryStructure->pf = $salary->pf;
            $salaryStructure->other_deduction = $salary->other_deduction;
            $salaryStructure->updated_by = Auth::user()->id;
            $salaryStructure->save();
        } else {
            // Create new salary structure
            $salaryStructure = new SalaryStructure();
            $salaryStructure->user_id = $userId;
            $salaryStructure->basic_salary = $salary->basic_salary;
            $salaryStructure->house_rent = $salary->house_rent;
            $salaryStructure->medical = $salary->medical;
            $salaryStructure->transport = $salary->transport;
            $salaryStructure->food = $salary->food;
            $salaryStructure->mobile = $salary->mobile;
            $salaryStructure->other_allowance = $salary->other_allowance;
            $salaryStructure->festival_bonus = $salary->festival_bonus;
            $salaryStructure->late_fine = $salary->late_fine;
            $salaryStructure->absent_deduction = $salary->absent_deduction;
            $salaryStructure->advance_salary = $salary->advance_salary;
            $salaryStructure->tax = $salary->tax;
            $salaryStructure->pf = $salary->pf;
            $salaryStructure->other_deduction = $salary->other_deduction;
            $salaryStructure->status = 1;
            $salaryStructure->created_by = Auth::user()->id;
            $salaryStructure->updated_by = Auth::user()->id;
            $salaryStructure->save();
        }
    }

    /**
     * Lock salary revision
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lock($id)
    {
        $salary = Salary::findOrFail($id);
        $salary->salary_locked = 1;
        $salary->updated_by = Auth::user()->id;
        $salary->save();

        return redirect()->back()->with('flash_success', 'Salary locked successfully');
    }

    /**
     * Unlock salary revision
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unlock($id)
    {
        $salary = Salary::findOrFail($id);
        $salary->salary_locked = 0;
        $salary->updated_by = Auth::user()->id;
        $salary->save();

        return redirect()->back()->with('flash_success', 'Salary unlocked successfully');
    }

    /**
     * Get current salary for a user via AJAX
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function getCurrentSalary($userId)
    {
        $currentSalary = Salary::where('user_id', $userId)
                              ->where('is_current', 1)
                              ->where('status', 1)
                              ->first();

        return response()->json([
            'current_salary' => $currentSalary
        ]);
    }
}
