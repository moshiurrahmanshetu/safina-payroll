<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalaryStructure;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class SalaryStructureController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $query = SalaryStructure::orderBy('id','desc');

    // Apply search filter
    if ($request->has('search') && $request->search) {
      $query->whereHas('user', function($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%');
      });
    }

    // Apply status filter
    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    $salary_structures = $query->with(['user'])->get();

    return view('admin.salary_structures.index', compact('salary_structures'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    // Get users with salary_processing = 1 and who don't have a salary structure yet
    $existingUserIds = SalaryStructure::pluck('user_id')->toArray();
    $users = User::where('salary_processing', 1)
                 ->where('status', 1)
                 ->whereNotIn('id', $existingUserIds)
                 ->orderBy('name','asc')
                 ->pluck('name','id');

    return view('admin.salary_structures.create', compact('users'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = request()->all();

    $validator = Validator::make($data, [
      'user_id' => 'required|unique:salary_structures,user_id',
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
      'status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;
    $data['created_by'] = $data['updated_by'] = $user_id;

    // Set default values for nullable fields
    $data['house_rent'] = $data['house_rent'] ?? 0;
    $data['medical'] = $data['medical'] ?? 0;
    $data['transport'] = $data['transport'] ?? 0;
    $data['food'] = $data['food'] ?? 0;
    $data['mobile'] = $data['mobile'] ?? 0;
    $data['other_allowance'] = $data['other_allowance'] ?? 0;
    $data['festival_bonus'] = $data['festival_bonus'] ?? 0;
    $data['late_fine'] = $data['late_fine'] ?? 0;
    $data['absent_deduction'] = $data['absent_deduction'] ?? 0;
    $data['advance_salary'] = $data['advance_salary'] ?? 0;
    $data['tax'] = $data['tax'] ?? 0;
    $data['pf'] = $data['pf'] ?? 0;
    $data['other_deduction'] = $data['other_deduction'] ?? 0;

    unset($data['_token']);

    $salary_structure = SalaryStructure::create($data);

    if ($salary_structure) {
      // Sync with salaries table - create new salary revision
      // Set previous current salary is_current = 0
      Salary::where('user_id', $data['user_id'])
            ->where('is_current', 1)
            ->update(['is_current' => 0]);

      // Create new salary record with same data
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
      $salary->effective_from = now()->format('Y-m-d');
      $salary->salary_increment_reason = 'Salary Structure Update';
      $salary->remarks = 'Synced from salary_structures';
      $salary->is_current = 1;
      $salary->revision_locked = 0;
      $salary->salary_locked = 0;
      $salary->status = $data['status'];
      $salary->created_by = $user_id;
      $salary->updated_by = $user_id;
      $salary->save();

      $message = "You have successfully created";
      return redirect()->route('salary_structures.index')->with('flash_success', $message);
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
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
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $salary_structure = SalaryStructure::with(['user'])->findorfail($id);

    return view('admin.salary_structures.edit', compact('salary_structure'));
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
    $data = request()->except('_method');

    $validator = Validator::make($data, [
      'user_id' => 'required|unique:salary_structures,user_id,'.$id,
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
      'status' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    $user_id = Auth::user()->id;
    $data['updated_by'] = $user_id;

    // Set default values for nullable fields
    $data['house_rent'] = $data['house_rent'] ?? 0;
    $data['medical'] = $data['medical'] ?? 0;
    $data['transport'] = $data['transport'] ?? 0;
    $data['food'] = $data['food'] ?? 0;
    $data['mobile'] = $data['mobile'] ?? 0;
    $data['other_allowance'] = $data['other_allowance'] ?? 0;
    $data['festival_bonus'] = $data['festival_bonus'] ?? 0;
    $data['late_fine'] = $data['late_fine'] ?? 0;
    $data['absent_deduction'] = $data['absent_deduction'] ?? 0;
    $data['advance_salary'] = $data['advance_salary'] ?? 0;
    $data['tax'] = $data['tax'] ?? 0;
    $data['pf'] = $data['pf'] ?? 0;
    $data['other_deduction'] = $data['other_deduction'] ?? 0;

    unset($data['_token']);

    $salary_structure = SalaryStructure::where('id', $id)->update($data);

    if ($salary_structure) {
      // Sync with salaries table - create new salary revision
      // Set previous current salary is_current = 0
      Salary::where('user_id', $data['user_id'])
            ->where('is_current', 1)
            ->update(['is_current' => 0]);

      // Create new salary record with same data
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
      $salary->effective_from = now()->format('Y-m-d');
      $salary->salary_increment_reason = 'Salary Structure Update';
      $salary->remarks = 'Synced from salary_structures';
      $salary->is_current = 1;
      $salary->revision_locked = 0;
      $salary->salary_locked = 0;
      $salary->status = $data['status'];
      $salary->created_by = $user_id;
      $salary->updated_by = $user_id;
      $salary->save();

      $message = "You have successfully Updated";
      return redirect()->back()->with('flash_success', $message);
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
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
    $deleted = SalaryStructure::where('id', $id)->delete();
    $message = "You have successfully Deleted";
    if ($deleted) {
      return redirect()->back()->with('flash_success', $message);
    } else {
      return redirect()->back()->withInput();
    }
  }
}
