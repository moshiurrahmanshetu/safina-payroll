<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use App\Models\Category;
use App\Models\Item;

class CategoryController extends Controller
{

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $categories = Category::all();
    return view('admin.categories.create',compact('categories')); 
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
        'name'   =>'required|unique:categories',
      )
    );

    if($validator->fails()){
      return redirect()->back()->withErrors($validator)
      ->withInput();
    }
    $categories=Category::create($data);

    if($categories){
      $message="You have successfully created";
      return redirect()->back()->with('flash_success',$message);
    }else{
      return redirect()->back()->withInput();
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
    $data = request()->only('category_id');
    $applyed = Item::where('category_id', $id)->update($data);
    $category=Category::destroy($id);   
    if($category){              
      $message="You have successfully deleted";
      return redirect()->route('category.create')
      ->with('flash_success',$message);
    }  
  }
}
