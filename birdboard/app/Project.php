<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Project extends Model
{
    use RecordActivity;

    protected $guarded = [];  
    
    public static $recordedEvents = ['created', 'updated'];

    public function path()
    {
        return "api/projects/{$this->id}";
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'id')->orderByDesc('updated_at');
    }

    public function addTask($attributes)
    {
        return $this->tasks()->create($attributes);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function activites()
    {
        return $this->hasMany(Activity::class, 'project_id', 'id');
    } 

    public function invite($user){
        $this->members()->attach($user);
    }

    public function members() {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id');
    }
}
