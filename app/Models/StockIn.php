<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
   public $timestamps = true;
   protected $guarded = array('id');
   public function item(){
    return $this->belongsTo(Item::class);
   }
   public function purchase_item(){
    return $this->belongsTo(PurchaseItem::class);
   }
   public function warehouse(){
    return $this->belongsTo(Warehouse::class);
   }
   public function purchase(){
    return $this->belongsTo(Purchase::class);
   }
}
