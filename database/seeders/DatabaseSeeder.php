<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Permission;
class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
      // $this->call(UsersTableSeeder::class);
    $now = date("Y-m-d H:i:s");
    //start roles seeder
    DB::table('roles')->insert([
      ['id' => 1,'name' => 'Super Admin', 'description' => 'Super Power', 'status' => 1, 'is_deletable'=>0, 'created_at' => $now]
    ]);
    //end roles seeder

   //start designations seeder
    DB::table('designations')->insert([
      ['id' => 1,'name' => 'Global Admin']
    ]);
    //end designations seeder
    //start departments seeder
    DB::table('departments')->insert([
      ['id' => 1,'name' => 'General Department']
    ]);
    //end departments seeder

    //start users seeder
    DB::table('users')->insert([
      ['id' => 1,'name' => 'Admin', 'email' => 'safina@gmail.com', 'password' => bcrypt('safina@39'), 'role_id' => 1, 'status' => 1, 'created_at' => $now, 'designation_id' => 1, 'department_id' => 1, 'supervisor_id' => 0, 'mobile_no'=>'01923760310', 'address'=>'', 'photo'=>'']     
    ]);
    //end users seeder

    //start SiteSettings seeder
    DB::table('site_settings')->insert([
      ['id' => 1,'name' => 'eStore Management', 'email' => 'safina@gmail.com', 
      'logo' => 'logo.png', 'logo_alt' => 'Insert logo alter', 'pdf_no_header_footer' => 0]     
    ]);
    //end SiteSettings seeder

      //start permission seeder 
    $allRoutes=Route::getRoutes();
    $controllers =array();
    foreach ($allRoutes as $route){
      $action = $route->getAction();
      if (array_key_exists('controller', $action)){
       $controllerAction =explode('@', $action['controller']);
       if(count($controllerAction)>1){
        $controllers[class_basename($controllerAction[0])][$controllerAction[1]] = $controllerAction[1];
      }
    }
  }
    // permission not need for this following controlles
  unset($controllers['ForgotPasswordController'],$controllers['ResetPasswordController'],$controllers['HomeController'],$controllers['LoginController'],$controllers['AjaxController'],$controllers['CsrfCookieController']);
    //dd($controllers);
  foreach($controllers as $key=>$controller){
   $data['name']=$key;
   $parent=Permission::firstOrCreate($data);
   if($parent){
    $data2['parent_id']=$parent->id;
    foreach($controller as $elements){
      $data2['name']=$elements;
      $all_done = Permission::firstOrCreate($data2);
      }
    }
  }
    //end permission seeder

    //start role_permission seeder
  $permissions= Permission::all();
  foreach ($permissions as $value) 
  {
    DB::table('role_permissions')->insert([
      ['role_id' => 1,'permission_id' => $value->id]
    ]);

  } 
    //end role_permission seeder
  }
}
