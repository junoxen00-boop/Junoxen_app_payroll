<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [

        // Company Information
        'company_name',
        'company_logo',
        'company_email',
        'company_phone',
        'company_website',
        'company_address',

        // System Settings
        'timezone',
        'date_format',

        // Email Settings
        'sender_name',
        'sender_email',
        'reply_to_email',
        'email_notifications',

    ];
}