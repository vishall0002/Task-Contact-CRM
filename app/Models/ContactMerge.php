<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;

class ContactMerge extends Model
{
   protected $table = 'contact_merges';

    protected $fillable = [
        'master_contact_id',
        'secondary_contact_id',
        'name',
        'email',
        'phone',
        'gender',
        'profile_image',
        'additional_file',
        'merged_by',
    ];

    /**
     * Master contact relationship
     */
    public function masterContact()
    {
        return $this->belongsTo(Contact::class, 'master_contact_id');
    }

    /**
     * Secondary contact relationship
     */
    public function secondaryContact()
    {
        return $this->belongsTo(Contact::class, 'secondary_contact_id');
    }

    

}
