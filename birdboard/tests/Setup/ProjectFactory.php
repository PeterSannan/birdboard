<?php

namespace Tests\Setup;

use App\Project;
use App\Task;
use App\User;

class ProjectFactory
{
    private $task_count = 0;
    private $owned_by = null;

    public function withTask($task_count)
    {
        $this->task_count = $task_count;
        return $this;
    }

    public function owned_by($user)
    { 
        $this->owned_by = $user;
        return $this;
    }

    public function create()
    { 
        $project = factory(Project::class)->create([
            'user_id' => $this->owned_by ?: factory(User::class)
        ]); 
       
        factory(Task::class, $this->task_count)->create([
            'project_id' => $project->id
        ]);

        return $project;
    }
}
