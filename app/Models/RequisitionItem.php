<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');
  public function item(){
    return $this->belongsTo(Item::class);
  }
  public function requisition(){
    return $this->belongsTo(Requisition::class);
  }
  public function mrs_items(){
    return $this->hasMany(MrsItem::class);
  }
}
