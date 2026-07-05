<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  public function services(){
    return $this->hasMany(Service::class);
  }

  public function categoryMetaFields(){
    return $this->hasMany(CategoryMetaField::class);
  }
}
