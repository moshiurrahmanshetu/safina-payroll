<?php
namespace App\Http\Controllers\Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\Designation;
use App\Models\Department;
use Auth;
use Input;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Register Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles the registration of new users as well as their
  | validation and creation. By default this controller uses a trait to
  | provide this functionality without requiring any additional code.
  |
  */

  use RegistersUsers;

  /**
  * Where to redirect users after registration.
  *
  * @var string
  */
  protected $redirectTo = '/myadmin';

  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
    parent::__construct();
    $this->middleware('auth');

    $this->middleware(function ($request, $next) {
      $roles=Role::pluck('name','id');
      view()->share('roles',$roles);
      $designations=Designation::pluck('name','id');
      view()->share('designations',$designations);
      $departments=Department::pluck('name','id');
      view()->share('departments',$departments);
      $supervisors=User::pluck('name','id')->toArray();
      view()->share('supervisors',$supervisors);
      return $next($request);
    }); 
  }

  /**
  * Get a validator for an incoming registration request.
  *
  * @param  array  $data
  * @return \Illuminate\Contracts\Validation\Validator
  */
  protected function validator(array $data)
  {
    return Validator::make($data, [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'role_id' => ['required'],
      'designation_id' => ['required'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);
  }

  /**
  * Create a new user instance after a valid registration.
  *
  * @param  array  $data
  * @return \App\User
  */
  public function create(\Request $request){
    return view('auth.create');
  }

  protected function store(Request $request)
  {
    $data=request()->all();
    //dd($data);
    $validator=Validator::make($data, [
      'name' => 'required|string|max:255',
      'role_id' => 'required',
      'designation_id' => 'required',
      'department_id' => 'required',
      'mobile_no' => 'required',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6|confirmed',
      'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
      'signature' => 'image|mimes:jpeg,png,jpg,gif,svg|max:200',
      'salary_processing' => 'required'
    ]);
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $data['password'] = Hash::make($data['password']);
    $imageName=null;
    $file=request()->file('photo');
    if($file != null){
      $imageName = time().'_'.get_file_name($file->getClientOriginalName());
      $data['photo']=$imageName;
    }

    $signatureName=null;
    $signature=request()->file('signature');
    if($signature != null){
      $signatureName = time().'_'.get_file_name($signature->getClientOriginalName());
      $data['signature']=$signatureName;
    }

    $all_done = User::create($data);
    if($all_done){
      if($imageName!=null){
        $file->storeAs('admin/users',$imageName);
      }
      if($signatureName!=null){
        $signature->storeAs('admin/users',$signatureName);
      }
      $message="You have successfully created";
      return redirect()->route('users.index')
      ->with('flash_success',$message);
    }
  }

  public function showUserLists(\Request $request){
    $users=User::with('role')->with('designation')->with('department')->get();
    return view('auth.show_user_lists',compact('users'));
  }

  public function showUser($id){
    $users=User::findOrFail($id);
    //dd($users);
  //return view('auth.show_user_lists',compact('users'));
  }

  public function editUser($id){
    $user=User::findOrFail($id);
    return view('auth.edit_user',compact('user'));
  }

  public function updateUser($id){
    $data = request()->except('_method', '_token');
    $validator=Validator::make($data,
      array(
        'name' => 'required|string|max:255',
        'role_id' => 'required',
        'designation_id' => 'required',
        'department_id' => 'required',
        'mobile_no' => 'required',
        'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        'signature' => 'image|mimes:jpeg,png,jpg,gif,svg|max:200',
        'salary_processing' => 'required'
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $imageName=null;
    $file=request()->file('photo');
    if($file != null){
      $imageName = time().'_'.get_file_name($file->getClientOriginalName());
      $data['photo']=$imageName;
    }
    $old_image_name = $data['old_image']; 
    unset($data['old_image']);
    $signatureName=null;
    $signature=request()->file('signature');
    if($signature != null){
      $signatureName = time().'_'.get_file_name($signature->getClientOriginalName());
      $data['signature']=$signatureName;
    }
    $old_signature_name = $data['old_signature']; 
    unset($data['old_signature']);
    $update = User::where('id', $id)->update($data);
    if($update){
      if($imageName!=null){
        if($old_image_name !=null){
          $img_path=storage_path('app/admin/users/'.$old_image_name);
          if (file_exists($img_path)){
            unlink($img_path);
          }
        }
        $file->storeAs('admin/users',$imageName);
      }
      if($signatureName!=null){
        if($old_signature_name !=null){
          $signature_path=storage_path('app/admin/users/'.$old_signature_name);
          if (file_exists($signature_path)){
            unlink($signature_path);
          }
        }
        $signature->storeAs('admin/users',$signatureName);
      }
      $message = "You have successfully updated";
      return redirect()->back()->with('flash_success', $message);
    }else{
      $error = "data can't updated please check again";
      return redirect()->back()->with('flash_success', $error);
    }
  }

  public function password(){
    return view('auth.password');
  }

  public function changePassword(Request $request){  
    $data = request()->except('_method', '_token');

    $validator=Validator::make($data,
      array(
        'password' => 'required|string|min:6|confirmed',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $data['password'] = Hash::make($data['password']);
    $auth_user = Auth::user();
    unset($data['password_confirmation']);
    //dd($data);
    $update = User::where('id', $auth_user->id)->update($data);
    if($update){
      $message = "You have successfully updated";
      return redirect()->back()->with('flash_success', $message);
    }else{
      $error = "data can't updated please check again";
      return redirect()->back()->with('flash_success', $error);
    }
  }

  public function destroyUser($id){
    $users=User::findOrFail($id);
  //dd($users);
  }


  public function profile(){
    $id = Auth::user()->id;
    $users=User::findOrFail($id);
    return view('auth.profile',compact('users'));
  }

  public function updateProfile(){
    $data = request()->except('_method', '_token');
    $id = Auth::user()->id;
    $validator=Validator::make($data,
      array(
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,id,'.$id,
        'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        'signature' => 'image|mimes:jpeg,png,jpg,gif,svg|max:200'
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $imageName=null;
    $file=request()->file('photo');
    if($file != null){
      $imageName = time().'_'.get_file_name($file->getClientOriginalName());
      $data['photo']=$imageName;
    }
    $old_image_name = $data['old_image']; 
    unset($data['old_image']);
    $signatureName=null;
    $signature=request()->file('signature');
    if($signature != null){
      $signatureName = time().'_'.get_file_name($signature->getClientOriginalName());
      $data['signature']=$signatureName;
    }
    $old_signature_name = $data['old_signature']; 
    unset($data['old_signature']);
    $update = User::where('id', $id)->update($data);
    if($update){
      if($imageName!=null){
        if($old_image_name !=null){
          $img_path=storage_path('app/admin/users/'.$old_image_name);
          if (file_exists($img_path)){
            unlink($img_path);
          }
        }
        $file->storeAs('admin/users',$imageName);
      }
      if($signatureName!=null){
        if($old_signature_name !=null){
          $signature_path=storage_path('app/admin/users/'.$old_signature_name);
          if (file_exists($signature_path)){
            unlink($signature_path);
          }
        }
        $signature->storeAs('admin/users',$signatureName);
      }
      $message = "You have successfully updated";
      return redirect()->back()->with('flash_success', $message);
    }else{
      $error = "Your data can't updated, please check again";
      return redirect()->back()->with('flash_success', $error);
    }
  }
  
  public function changeAllUserPassword(Request $request){  
    $data = request()->except('_method', '_token');

    $validator=Validator::make($data,
      array(
        'user_id' => 'required',
        'password' => 'required|string|min:6|confirmed',
      )
    );
    if($validator->fails()){
      return redirect()->back()->withErrors($validator)->withInput();
    }
    $data['password'] = Hash::make($data['password']);
    $user_id = $data['user_id'];
    unset($data['password_confirmation']); unset($data['user_id']);
    //dd($data);
    $update = User::where('id', $user_id)->update($data);
    if($update){
      $message = "You have successfully updated";
      return redirect()->back()->with('flash_success', $message);
    }else{
      $error = "data can't updated please check again";
      return redirect()->back()->with('flash_warning', $error);
    }
  }


}
