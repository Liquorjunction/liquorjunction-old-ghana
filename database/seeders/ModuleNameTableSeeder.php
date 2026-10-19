<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModulesName;
use DB;

class ModuleNameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_names')->truncate();
        $modules1 = new ModulesName();
        $modules1->module_name = 'Location Manage';
        $modules1->status = 1;
        $modules1->save();
      

        $modules2 = new ModulesName();
        $modules2->module_name = 'Student Manage';
        $modules2->status = 1;
        $modules2->save();


        $modules3 = new ModulesName();
        $modules3->module_name = 'Program Manage';
        $modules3->status = 1;
        $modules3->save();

        $modules4 = new ModulesName();
        $modules4->module_name = 'Package Manage';
        $modules4->status = 1;
        $modules4->save();

        $modules5 = new ModulesName();
        $modules5->module_name = 'Staff Manage';
        $modules5->status = 1;
        $modules5->save();

        $modules6 = new ModulesName();
        $modules6->module_name = 'Certificate';
        $modules6->status = 1;
        $modules6->save();

        $modules7 = new ModulesName();
        $modules7->module_name = 'Reports';
        $modules7->status = 1;
        $modules7->save();

        $modules8 = new ModulesName();
        $modules8->module_name = 'Web link';
        $modules8->status = 1;
        $modules8->save();

        $modules9 = new ModulesName();
        $modules9->module_name = 'Notifications';
        $modules9->status = 1;
        $modules9->save();

        $modules10 = new ModulesName();
        $modules10->module_name = 'Custome notifications';
        $modules10->status = 1;
        $modules10->save();


        $modules11 = new ModulesName();
        $modules11->module_name = 'General Settings';
        $modules11->status = 1;
        $modules11->save();

       
    }
}
