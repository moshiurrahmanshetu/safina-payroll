<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Purchase extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');
  public function supplier(){
    return $this->belongsTo(Supplier::class);
  }
  public function purchase_items(){
    return $this->hasMany(PurchaseItem::class);
  }
  public function purchase_transactions(){
    return $this->hasMany(PurchaseTransaction::class);
  }
}
