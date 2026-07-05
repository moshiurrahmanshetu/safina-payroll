<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Requisition extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');
  public function user(){
    return $this->belongsTo(User::class);
  }
  public function supervisor(){
    return $this->belongsTo(User::class,'counter_sign_by','id');
  }
  public function requisition_items(){
    return $this->hasMany(RequisitionItem::class);
  }
  public function purpose(){
    return $this->belongsTo(Purpose::class);
  }    
}
