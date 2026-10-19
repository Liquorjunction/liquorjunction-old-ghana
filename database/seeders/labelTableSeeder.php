<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\Label;
class labelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('labels')->truncate();
       $response =  '{
  "email": "Email",
  "password": "Password",
  "login": "Login",
  "forgot_password": "Forgot Password?",
  "forgot_password_title": "Forgot Password",
  "dont_have_an_account": "Don’t have an account? ",
  "register": "Register",
  "remember_password": "Remember password? ",
  "send": "Send",
  "welcome_to": "Welcome to",
  "upskild": "UpSkild",
  "won_up": "Won UP",
  "please_enter_email": "Please enter email",
  "enter_valid_email_address": "Please enter valid email",
  "please_enter_password": "Please enter password",
  "please_enter_atleast_6_digits": "Please enter atleast 6 character",
  "please_enter_email_for_reset_password": "Please enter email for reset password",
  "email_to_reset_password_has_been_sent_to_your_email": "Email to reset password has been sent to your email, Kindly check it",
  "email_to_reset_password_has_been_sent": "Email to reset password has been sent Kindly check it",
  "first_name": "First Name",
  "last_name": "Last Name",
  "email_address": "Email Address",
  "mobile_number": "Mobile Number",
  "optional": "*Optional",
  "continue": "Continue",
  "please_enter_first_name": "Please enter first name",
  "please_enter_last_name": "Please enter last name",
  "personal_information": "Personal Information",
  "school_information": "School Information",
  "school_code": "School Code",
  "select_school": "Select School",
  "select_school_location": "Select School Location",
  "select_program": "Select Program",
  "home": "Home",
  "leaderboard": "Leaderboard",
  "shoutout": "Shout out",
  "take_action": "Take Action",
  "settings": "Settings",
  "profile": "Profile",
  "hello": "Hello",
  "current_leaderboard": "Current Leaderboard",
  "total_times_leaderboard": "Total Times Leaderboard",
  "refreshes": "Refreshes:",
  "rank": "Rank",
  "points": "Points",
  "current": "Current",
  "all_time": "All Time",
  "career_success": "Career Success",
  "rewards": "Rewards",
  "gold": "Gold",
  "silver": "Silver",
  "bronze": "Bronze",
  "earn_the_most_points_100": "Earn the most points in your program and recieve the $100 Visa Gift Card",
  "earn_the_most_points_50": "Earn the most points in your program and recieve the $50 Visa Gift Card",
  "earn_the_most_points_25": "Earn the most points in your program and recieve the $25 Visa Gift Card",
  "back": "Back",
  "shout_out_questions": "Shout Out Questions",
  "classroom": "Classroom",
  "must_be_completed_by_due_date": "Must be completed by due date; Quizzes are reset every week.",
  "are_you_sure_want_to_close_this_app": "Are you sure you want to close this app?",
  "yes": "Yes",
  "no": "No",
  "resume_writing_quiz": "Resume Writing Quiz",
  "question": "Question",
  "total_questions": "Total Questions:",
  "correct": "Correct:",
  "refresh_date": "Refresh Date:",
  "start_date": "Start Date:",
  "next": "Next",
  "correct_title": "Correct",
  "incorrect": "Incorrect",
  "view_correct_answers": "View Correct Answers",
  "thank_you": "Thank You",
  "you_have_successfully_completed": "You have successfully completed",
  "points_earned": "Points Earned",
  "review": "Review",
  "due_date": "Due date",
  "take_action_details": "Take action details",
  "take_action_details_title": "Take Action details",
  "prize": "Prize",
  "scan_to_complete": "Scan to complete",
  "certificate": "Certificate",
  "submit": "Submit",
  "scan_QR_code": "Scan QR Code",
  "logout": "Logout",
  "lets_play": "Let’s Play",
  "click_on_the_crowns_to_see": "Click on the crowns to see the rewards for earning the most points in your program",
  "game_on": "Game On!",
  "achievements": "Achievements",
  "notification": "Notification",
  "more": "More",
  "skip": "Skip",
  "shout_out_pin": "Shout Out Pin",
  "prizes": "Prizes",
  "certifications": "Certifications",
  "download": "Download",
  "take_action_results": "Take Action Results",
  "you_won": "You won:",
  "total_points_to_date": "Total Points To Date:",
  "assigned": "Assigned",
  "completed": "Completed",
  "total_take_actions": "Total Take Actions",
  "back_to_home": "Back to Home",
  "update": "Update",
  "student_ID": "Student ID",
  "program_name": "Program Name",
  "change": "Change",
  "notification_setting": "Notification Setting",
  "password_not_match": "Password not match",
  "email_not_exist": "Email not exist",
  "invalid_token": "Invalid token. please login/register again",
  "account_inactive": "Your account is inactive by admin. please try again",
  "ok": "OK"
}';

        $d = json_decode($response, true);

        foreach($d as $key => $mkm)
        {
            $labels = new Label();
            $labels->language_id = 1;
            $labels->labelname = $key;
            $labels->labelvalue = $mkm;
            $labels->status = 1;
            $labels->save();
        }
       
    }
}
