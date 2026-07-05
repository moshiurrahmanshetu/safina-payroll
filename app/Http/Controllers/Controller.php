<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\RolePermission;
use View;
use Auth;
class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
  public $menu_list;
  protected $auth_role_id, $auth_user_id, $paginateNum;

  public function __construct(){
    $this->middleware(function ($request, $next) {
      if($request->user()){
        $this->menu_list=RolePermission::select('permission_id')->with(['permission'=>function($q){
          $q->select('id','name','parent_id');
        }])->where('role_id',$request->user()->role_id)->get()->toArray();
        //dump($this->menu_list);
        $controllerArr = [];
        $allIndexes = [];
        foreach( $this->menu_list as $key => $value){
          if($value['permission']['parent_id'] == null){
            $controllerArr[$value['permission']['id']] = $value['permission']['name'];
          }else{  
            $allIndexes[] = $controllerArr[$value['permission']['parent_id']].'@'.$value['permission']['name'];
            $this->menu_list = $allIndexes;
          }  
        }
        view()->share('menu_list',$allIndexes);
      }

      if(Auth::check()) {
        $this->auth_user_id= Auth::user()->id; 
        view()->share('auth_user_id', $this->auth_user_id);
        $this->auth_user_name= Auth::user()->name; 
        view()->share('auth_user_name', $this->auth_user_name);
        $this->auth_user_photo= Auth::user()->photo; 
        view()->share('auth_user_photo', $this->auth_user_photo);
        $this->auth_role_id = Auth::user()->role_id; 
        view()->share('auth_role_id', $this->auth_role_id);
        $this->paginateNum=30;
      }

      return $next($request);
    }); 
  }
}
