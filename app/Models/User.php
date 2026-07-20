<?php
namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'role_id','designation_id', 'email', 'password','department_id','supervisor_id','mobile_no','address','photo','status','signature'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];
  
  public function role(){
    return $this->belongsTo(Role::class);
  }
  public function designation(){
    return $this->belongsTo(Designation::class);
  }
  public function department(){
    return $this->belongsTo(Department::class);
  }

  public function shift(){
    return $this->belongsTo(Shift::class);
  }

  public function gates(){
    return $this->belongsToMany(Gate::class, 'user_gates', 'user_id', 'gate_id');
  }

  public function counters(){
    return $this->belongsToMany(Counter::class, 'counter_user', 'user_id', 'counter_id');
  }
  
  public function bookings(){
    return $this->hasMany(Booking::class, 'created_by');
  }

  public function waterParkCounters(){
    return $this->belongsToMany(WaterParkCounter::class, 'water_park_counter_user', 'user_id', 'water_park_counter_id');
  }
  public function parkingCounters(){
    return $this->belongsToMany(ParkingCounter::class, 'parking_counter_user', 'user_id', 'parking_counter_id');
  }

  public function packageCounters(){
    return $this->belongsToMany(PackageCounter::class, 'package_counter_user', 'user_id', 'package_counter_id');
  }

  public function lockerGearCounters(){
    return $this->belongsToMany(LockerGearCounter::class, 'locker_gear_counter_user', 'user_id', 'locker_gear_counter_id');
  }


}
