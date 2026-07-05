<?php
namespace App\Http\Controllers;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class DesignationController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $designations = Designation::all();
      return view('admin.designations.create',compact('designations')); 
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
          'name'      =>'required|unique:designations',
        )
      );

      if($validator->fails()){
        return redirect()->back()->withErrors($validator)->withInput();
      }
      //print_r($data); exit;
      $designations=Designation::create($data);

      if($designations){
        $message="You have successfully created";
        return redirect()->back()->with('flash_success',$message);
      }else{
        return redirect()->back();
      }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $data = request()->only('designation_id');
      $applyed = User::where('designation_id', $id)->update($data);

      $designation=Designation::destroy($id);
      if($designation){
        $message="You have successfully deleted";
        return redirect()->route('designation.create')
        ->with('flash_success',$message);
      }  
    }
  }
