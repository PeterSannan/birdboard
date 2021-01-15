<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projects() {
        return $this->hasMany(Project::class, 'user_id', 'id');
    }

    public function allProjects() { 

       // return $this->accesibleProjects->merge($this->projects);
         return $this->accesibleProjects->merge($this->projects);


        /*
        $invitedProjects = Project::where('user_id', $this->id)
        ->orWhereHas('members',function($query) {
               $query->Where('user_id', $this->id);
            })
            ->get();
         return $invitedProjects;  
         */ 
    }

    public function accesibleProjects() {
        return  $this->belongsToMany(Project::class, 'project_members', 'user_id', 'project_id');
        
    }
}
