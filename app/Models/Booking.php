<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
  public $timestamps = true;
  protected $guarded = array('id');
  protected $fillable = ['service_id', 'name', 'phone', 'email', 'emergency_contact', 'address', 'check_in_date', 'check_in_time', 'check_out_date', 'check_out_time', 'date', 'time_slot', 'time_slot_id', 'start_time', 'end_time', 'total_price', 'discount_amount', 'manual_discount', 'final_price', 'promo_code', 'status', 'meta_values', 'user_id', 'counter_id', 'created_by', 'updated_by'];

  /**
   * Cast attributes
   */
  protected $casts = [
    'meta_values' => 'array', // Cast JSON column to array
    'check_in_date' => 'date',
    'check_out_date' => 'date',
    'date' => 'date',
  ];

  public function service(){
    return $this->belongsTo(Service::class);
  }

  public function user(){
    return $this->belongsTo(User::class);
  }

  /**
   * DEPRECATED: Old relation to booking_meta_values table
   * Kept for backward compatibility during transition
   * New code should use getMetaValues() or meta_values attribute
   */
  public function meta_values_relation(){
    return $this->hasMany(BookingMetaValue::class, 'booking_id');
  }

  /**
   * DEPRECATED: Alias to avoid conflict with meta_values JSON column
   * Kept for backward compatibility
   */
  public function bookingMetaValues(){
    return $this->hasMany(BookingMetaValue::class, 'booking_id');
  }

  /**
   * Get all meta values from JSON column
   * Returns array with structure: [field_name => ['label' => ..., 'value' => ..., 'file_path' => ...]]
   */
  public function getMetaValues(){
    return $this->meta_values ?? [];
  }

  /**
   * Get a specific meta value by field name
   */
  public function getMetaValue($fieldName, $key = 'value'){
    $metaValues = $this->getMetaValues();
    return $metaValues[$fieldName][$key] ?? null;
  }

  /**
   * Set meta values to JSON column
   * @param array $metaValues Array of [field_name => ['label' => ..., 'value' => ..., 'file_path' => ...]]
   */
  public function setMetaValues(array $metaValues){
    $this->meta_values = $metaValues;
    return $this;
  }

  /**
   * Add a single meta value to JSON column
   */
  public function addMetaValue($fieldName, $label, $value, $filePath = null){
    $metaValues = $this->getMetaValues();
    $metaValues[$fieldName] = [
      'label' => $label,
      'value' => $value,
      'file_path' => $filePath
    ];
    $this->meta_values = $metaValues;
    return $this;
  }

  /**
   * Check if booking has a specific meta field
   */
  public function hasMetaValue($fieldName){
    $metaValues = $this->getMetaValues();
    return isset($metaValues[$fieldName]);
  }

  /**
   * Get meta value for simple value access (backward compatibility)
   * This mimics the old behavior where $meta_values_array[$field_name] gave the value
   */
  public function getSimpleMetaValues(){
    $simple = [];
    $metaValues = $this->getMetaValues();

    foreach ($metaValues as $fieldName => $data) {
      $simple[$fieldName] = $data['value'] ?? null;
    }

    return $simple;
  }

  public function gate(){
    return $this->belongsTo(Gate::class);
  }

  public function timeSlot(){
    return $this->belongsTo(TimeSlot::class);
  }

  public function counter(){
    return $this->belongsTo(Counter::class);
  }

  public function creator(){
    return $this->belongsTo(User::class, 'created_by');
  }
}
