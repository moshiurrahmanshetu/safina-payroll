<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CategoryMetaField extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  public function serviceCategory(){
    return $this->belongsTo(ServiceCategory::class);
  }
}
