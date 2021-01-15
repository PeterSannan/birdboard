<?php

namespace Tests\Unit;

use App\Project;
use App\Task;
use Tests\TestCase;

class TasksTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_has_path()
    {
        $project = factory(Project::class)->create(); 
        $task = $project->addTask(factory(Task::class)->raw());

        $this->assertEquals("/api/projects/{$project->id}/tasks/{$task->id}", $task->path());
    }

    public function test_it_can_be_completed()
    { 
        $task = factory(Task::class)->create();

        $this->assertFalse($task->completed);

        $task->complete();

        $this->assertTrue($task->completed);
 
    }

    public function test_it_can_be_incompleted()
    { 
        $task = factory(Task::class)->create();

        $this->assertFalse($task->completed);

        $task->complete();

        $this->assertTrue($task->completed);
        
        $task->incomplete();
       
        $this->assertFalse($task->completed);
 
    }
}
