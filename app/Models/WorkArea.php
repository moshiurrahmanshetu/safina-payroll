<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkArea extends Model
{
    public $timestamps = true;
    protected $guarded = array('id');

    protected $casts = [
        'status' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
