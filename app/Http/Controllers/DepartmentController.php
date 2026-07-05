<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class DepartmentController extends Controller
{

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $departments = Department::all();
    return view('admin.departments.create',compact('departments')); 
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data=request()->all();    
    $validator=Validator::make($data,
      array(
        'name'      =>'required|unique:departments',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $departments=Department::create($data);
    if($departments){
      $message="You have successfully created";
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back();
    }
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Department  $department
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $data = request()->only('department_id');
    $applyed = User::where('department_id', $id)->update($data);
    $department=Department::destroy($id);   
    if($department){              
      $message="You have successfully deleted";
      return redirect()->route('department.create')
      ->with('flash_success',$message);
    }  
  }
}
