<?php

namespace Tests\Unit;

use App\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_has_path()
    {  
        $project = factory(Project::class)->create();
        $this->assertEquals("api/projects/{$project->id}", $project->path());
    }

    public function test_it_can_add_task()
    {  
        $project = factory(Project::class)->create();

        $project->addTask([
            'body' => 'new task'
        ]);

        $this->assertCount(1, $project->tasks);
    }
}
