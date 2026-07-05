<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Supplier extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');
  public function purchases(){
    return $this->hasMany(Purchase::class);
  }
}
