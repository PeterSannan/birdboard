<?php

namespace Tests\Feature;

use App\Project;
use App\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_project_can_have_tasks()
    { 
        $this->withoutExceptionHandling();
        $project = ProjectFactory::create();
        $task = factory(Task::class)->raw(); 

        $this->actingAs($project->user,'api')->post($project->path() . '/tasks', $task)->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'body' => $task['body'],
                        'project' => [
                            'title' => $project->title,
                            'description' => $project->description
                        ]
                    ]
                ]
            ]);
    }

    public function test_task_can_be_updated()
    {   
        $project = ProjectFactory::withTask(1)->create();

        $this->actingAs($project->user, 'api')
            ->patch($project->tasks()->first()->path(), [
            'body' => 'another body'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'body' => 'another body',
                        'project' => [
                            'title' => $project->title,
                            'description' => $project->description
                        ]
                    ]
                ]
            ]);
    }

    public function test_task_can_be_completed()
    {   
        $project = ProjectFactory::withTask(1)->create();

        $this->actingAs($project->user, 'api')
            ->patch($project->tasks()->first()->path(), [ 
            'completed' => true
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'completed' => true,
                        'project' => [
                            'title' => $project->title,
                            'description' => $project->description
                        ]
                    ]
                ]
            ]);
    }

    public function test_task_can_be_incompleted()
    {   
        $project = ProjectFactory::withTask(1)->create();

        $this->actingAs($project->user, 'api')
            ->patch($project->tasks()->first()->path(), [ 
            'completed' => true
        ]);

        $this->actingAs($project->user, 'api')
            ->patch($project->tasks()->first()->path(), [ 
            'completed' => false
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'tasks',
                    'attributes' => [
                        'completed' => false,
                        'project' => [
                            'title' => $project->title,
                            'description' => $project->description
                        ]
                    ]
                ]
            ]);
    }

    public function test_only_the_owner_can_add_task_to_the_project() {
        $this->signIn();
        $project = ProjectFactory::create();
        $task = factory(Task::class)->raw();

        $this->post($project->path() . '/tasks', $task)->assertStatus(403);
    }

    public function test_only_the_owner_can_update_task_in_the_project() {
        $this->signIn(); 
        $project = ProjectFactory::withTask(1)->create();

        $this->patch($project->tasks[0]->path(), [
            'body' => 'another body',
            'completed' => true
        ])->assertStatus(403);
    }

    public function test_guest_cannot_add_task_to_the_project() {  
        $project = factory(Project::class)->create();
        $task = factory(Task::class)->raw();

        $this->json('POST', $project->path() . '/tasks', $task)->assertStatus(401);
    }

    public function test_body_is_required_to_add_task() {
        $project = ProjectFactory::create();

        $attributes = factory(Task::class)->raw([
            'body' => ''
        ]);

        $response = $this->actingAs($project->user, 'api')->post($project->path() . '/tasks', $attributes)->assertStatus(422);
        $this->assertArrayHasKey('body', $response['errors']['meta']);
    }

    public function test_when_task_is_updated_the_project_should_be_updated(){
        $project = factory(Project::class)->create([
            'updated_at' => Carbon::now()->subDay(4)
        ]);
        $task = $project->addTask(factory(Task::class)->raw()); 

        $this->assertEquals($project->fresh()->updated_at, $task->updated_at);
    }

}
