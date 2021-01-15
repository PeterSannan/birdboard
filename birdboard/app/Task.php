<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Task extends Model
{
    use recordActivity;

    protected $guarded = []; 

    public static $recordedEvents = ['created', 'deleted'];

    protected $touches = ['project'];
 

    public function project() {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    } 

    public function path() {
        return "/api/projects/{$this->project->id}/tasks/{$this->id}";
    }

    public function complete() {
        $this->update([
            'completed' => true
        ]);

        $this->recordActivity('completed_task');
    }

    public function incomplete() {
        $this->update([
            'completed' => false
        ]); 

        $this->recordActivity('incompleted_task');
    }  
}
