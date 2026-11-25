<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'email', 
        'website_logo', 
        'phone', 
        'address', 
        'fax',
        'delivery_fee',
        'delivery_amount',
        'guest_daily_order_limit',
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

    public function getNocaptchaStatusAttribute($value)
    {
        return $value !== null ? $value : (int) env('RECAPTCHA_STATUS', 1);
    }

    public function getNocaptchaSitekeyAttribute($value)
    {
        return $value !== null ? $value : env('RECAPTCHA_SITE_KEY', env('NOCAPTCHA_SITEKEY', ''));
    }

    public function getNocaptchaSecretAttribute($value)
    {
        return $value !== null ? $value : env('RECAPTCHA_SECRET_KEY', env('NOCAPTCHA_SECRET', ''));
    }
}
