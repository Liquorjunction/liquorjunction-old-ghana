<?php

namespace App\Console\Commands;
use DB;
use App\Models\Setting;
use App\Models\MainUser;
use App\Models\EmailTemplate;
use Mail;
use Illuminate\Console\Command;

class EmailsendCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cartData = DB::table('cart')->where('status',1)->get();

        foreach ($cartData as $cart) {
            
        $user = DB::table('main_users')->where('id',$cart->user_id)->first();

        $ismail = $this->attachment_register_email($user);
        }


        return Command::SUCCESS;
    }

    public function attachment_register_email($user)
    {
        $setting = Setting::find(1);
        // $emaildetail = EmailTemplate::find(9);
        $name = @$user->first_name.' '.@$user->last_name;
       $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('14');

        $data = array('email' => @$user->email, 'name' => $name,'id'=>14,'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);

        Mail::send('emails.register_user', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);

        $message->from($data['from_email'], $data['support_name']);
        });
    }
}
