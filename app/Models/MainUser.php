<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MainUser extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uniqid',
        'name',
        'first_name',
        'last_name',
        'email',
        'age',
        'password',
        'confirm_password',
        'photo',
        'user_type',
        'store_name',
        'store_description',
        'phone',
        'phone_code',
        'abn_number',
        'post_code',
        'country',
        'street_address',
        'states',
        'city',
        'is_technician',
        'technician_status',
        'profile',
        'country_code',
        'current_plan_id',
        'plan_expiry_date',
        'otp',
        'otp_expire_time',
        'is_otp_verify',
        'is_phone',
        'device_token',
        'about_me',
        'google_id',
        'facebook_id',
        'apple_id',
        'category_id',
        'id_proof',
        'is_verify_user',
        'created_by',
        'updated_by',
        'instructor_since',
        'category_dance_instructor',
        'instructor_facebook_link',
        'instructor_instagram_link',
        'instructor_tiktok_link',
        'instructor_web_link',
        'instructor_location',
        'dance_group_name',
        'instructor_portfolio_image',
        'instructor_portfolio_video',
        'introduction_video',
        'bank_account_name',
        'bank_account_number',
        'bank_name',
        'gcash_number',
        'is_verify_instructor',
        'is_popular_insructor',
        'permissions_id',
        'status',
        'web_token',
        'social_type',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
      
    ];

    // relation with Permissions
    // public function permissionsGroup()
    // {
    //     return $this->belongsTo('App\Models\Permissions', 'permissions_id');
    // }
    // public function country() {
    //     return $this->hasMany(Country::class, 'id','country_id');
    // }

}
