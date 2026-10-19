<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'language_id', 
        'email', 
        'website_logo', 
        'phone', 
        'address', 
        'fax',
        'delivery_fee',
        'delivery_amount',
        'currency_symbol',
        'timezone',
        'android_version',
        'ios_version',
        'android_version_update',
        'ios_version_update',
        'facebook_link', 
        'twitter_link', 
        'instagram_link',
        'youtube_link',
        'tiktok_link',
        'youtube_link',
        'support_name',
        'support_email',
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_no_replay',
        'mail_title',
        'mail_template',
        'intro_video',
        'status',
        'site_maintenance',
        'site_maintenance_at',
    ];

    public function SettingLanguage()
    {
      return $this->hasOne('App\Models\Language','id','language_id');
    }
}
