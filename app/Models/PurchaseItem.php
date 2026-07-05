<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');
  public function category(){
    return $this->belongsTo(Category::class);
  }
  public function item(){
    return $this->belongsTo(Item::class);
  }
}
