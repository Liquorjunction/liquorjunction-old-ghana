<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_type')->default('1')->comment('1 => ADMIN, 2 => USER, 3 => INSTRUCTOR');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile')->nullable();
            $table->string('phone')->nullable();
            $table->string('country_code')->nullable();
            $table->integer('current_plan_id')->nullable();
            $table->string('otp')->nullable();
            $table->string('about_me')->nullable();
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->integer('is_verify_user')->default('1');
            $table->date('plan_expiry_date')->nullable();
            $table->date('instructor_since')->nullable();
            $table->integer('category_dance_instructor')->nullable();
            $table->string('instructor_facebook_link')->nullable();
            $table->string('instructor_instagram_link')->nullable();
            $table->string('instructor_tiktok_link')->nullable();
            $table->string('instructor_web_link')->nullable();
            $table->string('instructor_location')->nullable();
            $table->string('dance_group_name')->nullable();
            $table->string('instructor_portfolio_image')->nullable();
            $table->string('instructor_portfolio_video')->nullable();
            $table->integer('is_verify_instructor')->default('1');
            $table->integer('is_popular_insructor')->default('1');
            $table->integer('status')->default('1')->comment('1 => ACTIVE, 0 => INACTIVE, 2 => DELETE');
            $table->integer('permissions_id')->nullable();
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('main_users');
    }
};
