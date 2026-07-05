<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseTransaction extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  public function supplier(){
    return $this->belongsTo(Supplier::class);
  }
  public function purchase(){
    return $this->belongsTo(Purchase::class);
  }

}
