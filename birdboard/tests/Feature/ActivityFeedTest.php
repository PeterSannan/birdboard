<?php

namespace Tests\Feature;

use App\Project;
use App\Task;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_creating_a_project_generate_activity_feed()
    {
        $project = factory(Project::class)->create();

        $this->assertCount(1, $project->activites);
        $this->assertEquals('created_project', $project->activites[0]->description);
        $this->assertNull($project->activites[0]->changes);
    }

    public function test_updating_a_project_generate_activity_feed()
    {
        $project = factory(Project::class)->create();
        $old = $project->title;
 
        $project->update([
            'title' => 'changed'
        ]);

        $this->assertCount(2, $project->activites);
        $this->assertEquals('updated_project', $project->activites[1]->description);
        $expected = [
            'old' => [
                'title' => $old
            ],
            'new' => [
                'title' => 'changed'
            ]
        ]; 
        $this->assertEquals($project->activites->last()->changes, $expected);
    }

    public function test_creating_task_for_a_project_generate_activity_feed()
    {
        $project = ProjectFactory::withTask(1)->create();

        $this->assertCount(2, $project->activites);
        $this->assertEquals('created_task', $project->activites->last()->description);
        $this->assertInstanceOf(Task::class, $project->activites->last()->subject);
    }

    public function test_completing_task_for_a_project_generate_activity_feed()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::withTask(1)->create();

        $this->actingAs($project->user, 'api')
            ->patch($project->tasks()->first()->path(), [
                'body' => 'sss',
                'completed' => true
            ]);

        $this->assertCount(3, $project->activites);
        $this->assertEquals('completed_task', $project->activites->last()->description);
        $this->assertInstanceOf(Task::class, $project->activites->last()->subject);
    }

    public function test_incompleting_task_for_a_project_generate_activity_feed()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::withTask(1)->create();

        $this->actingAs($project->user, 'api')
            ->patch($project->tasks()->first()->path(), [
                'body' => 'sss',
                'completed' => true
            ]);

        $this->assertCount(3, $project->activites);

        $this->patch($project->tasks()->first()->path(), [
            'completed' => false
        ]);
        $project->refresh();

        $this->assertCount(4, $project->activites);
        $this->assertEquals('incompleted_task', $project->activites->last()->description);
        $this->assertInstanceOf(Task::class, $project->activites->last()->subject);
    }

    public function test_deleting_task_for_a_project_generate_activity_feed()
    {
        $project = ProjectFactory::withTask(1)->create();
        $project->tasks[0]->delete();

        $this->assertCount(3, $project->activites);
        $this->assertEquals('deleted_task', $project->activites->last()->description);
    }
}
