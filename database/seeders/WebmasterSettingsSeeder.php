<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class WebmasterSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = new Setting;
        $setting->currency_symbol = "US";
        $setting->timezone = "UTC";
        $setting->facebook_link = "www.facebook.com";
        $setting->twitter_link = "www.twitter.com";
        $setting->instagram_link = "www.instagram.com";
        $setting->tiktok_link = "www.tiktok.com";
        $setting->youtube_link = "www.youtube.com";
        $setting->support_name = "OnlyDance";
        $setting->support_email = "support@onlydance.com";
        $setting->status = 1;
        $setting->save();
    }
}
