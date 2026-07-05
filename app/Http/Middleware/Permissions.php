<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\RolePermission;
use App\Models\Permission;
use Route;

class permissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      
      $controllerAction=class_basename(Route::currentRouteAction()); 
      if(isset($controllerAction)){
      list($current_location['controller'], $current_location['action']) = explode('@', $controllerAction);
     
      $pid = Permission::where('name', $current_location['controller'])
        ->pluck('id'); 
      if(count($pid)>0){
      $check=RolePermission::where('role_id',$request->user()->role_id)->where('permission_id',function($query) use($current_location,$pid,$controllerAction){
      $query->select('id')
            ->from('permissions')
            ->where('name', $current_location['action'])
            ->where('parent_id', $pid);
       })->get();
      if(count($check)>0){
        $response = $next($request);
       return $response;
      }
      else{
        //App::abort(404, 'Not Allowed');
       return redirect()->back()->with('flash_warning', 'you are not allowed for requesting page');
      }
     }else{
      //App::abort(404, 'Not Allowed');
      return redirect()->back()->with('flash_warning', 'you are not allowed for requesting page');
     }
    }  
  }
}
