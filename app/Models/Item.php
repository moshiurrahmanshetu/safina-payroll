<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');
  public function category(){
    return $this->belongsTo(Category::class);
  }

}
