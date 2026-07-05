<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');

  public function service(){
    return $this->belongsTo(Service::class);
  }

  public function category(){
    return $this->belongsTo(ServiceCategory::class, 'category_id');
  }
}
