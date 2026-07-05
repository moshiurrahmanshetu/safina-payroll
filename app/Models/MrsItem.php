<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MrsItem extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');
  public function requisition_item(){
    return $this->belongsTo(RequisitionItem::class);
  }
  public function requisition(){
    return $this->belongsTo(Requisition::class);
  }
  public function warehouse(){
    return $this->belongsTo(Warehouse::class);
  }
  public function item(){
    return $this->belongsTo(Item::class);
  }
}
