<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCustomFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'custom_field_id',
        'field_value',
    ];

    public $timestamps = false;

    /**
     * Contact relation
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Custom field relation
     */
   public function customField()
    {
        return $this->belongsTo(CustomField::class)
                    ->where('status', 'active')
                    ->whereNull('deleted_at');
    }


}
