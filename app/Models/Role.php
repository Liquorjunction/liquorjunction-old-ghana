<?php

namespace App\Models;
use App\Models\RoleModulePermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles'; 

    public function role_permission()
    {
        return $this->hasone(RoleModulePermission::class,'role_id','id');
    }
}
