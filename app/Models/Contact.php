<?php

namespace App\Models;

use App\Models\ContactMerge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'profile_image',
        'additional_file',
        'is_merged',
        'merged_into',
    ];

    /**
     * Custom field values of this contact
     */
    public function customFields()
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }

    /**
     * Master contact (if merged)
     */
    public function masterContact()
    {
        return $this->belongsTo(Contact::class, 'merged_into');
    }

    /**
     * Contacts merged into this contact
     */
    // public function mergedContacts()
    // {
    //     return $this->hasMany(Contact::class, 'merged_into');
    // }
    

    // Contact.php
    public function mergedContacts()
    {
        return $this->hasMany(ContactMerge::class, 'master_contact_id')
                    ->with('secondaryContact');
    }

}
