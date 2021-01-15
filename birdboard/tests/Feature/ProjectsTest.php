<?php

namespace Tests\Feature;

use App\Project;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_create_post()
    {
        $this->signIn();
        $attributes = [
            'title' => 'my title',
            'description' => 'my description',
            'notes' => 'my notes'
        ];

        $response = $this->post('/api/projects', $attributes)->assertStatus(201);

        $response->assertJson([
            'data' => [
                'type' => 'projects',
                'attributes' => [
                    'title' => $attributes['title'],
                    'notes' => $attributes['notes'],
                    'description' => $attributes['description'],
                    'user_id' => auth()->id()
                ]
            ]
        ]);
    }

    public function test_user_can_see_project_has_been_invited_too_on_dashboard()
    {
        $project = ProjectFactory::create();

        $project->invite($this->signIn());

        $response = $this->get('/api/projects')->assertJson([
            'data' => [
                [
                    'type' => 'projects',
                    'attributes' => [
                        'title' => $project->title,
                        'description' => $project->description
                    ]
                ]
            ]
        ]);
    }

    public function test_not_owner_cannot_update_post()
    {
        $this->signIn();
        $project = ProjectFactory::create();

        $response = $this->patch($project->path(), [
            'notes' => 'new notes'
        ])->assertStatus(403);
    }

    public function test_only_owner_can_update_post()
    {
        $project = ProjectFactory::create();

        $response = $this->actingAs($project->user, 'api')->patch($project->path(), [
            'notes' => 'new notes',
            'title' => 'new title',
            'description' => 'new description'
        ])->assertStatus(200);

        $response->assertJson([
            'data' => [
                'type' => 'projects',
                'attributes' => [
                    'notes' => 'new notes',
                    'title' => 'new title',
                    'description' => 'new description'
                ]
            ]
        ]);
    }

    public function test_only_owner_can_update_only_note_project()
    {
        $project = ProjectFactory::create();

        $response = $this->actingAs($project->user, 'api')->patch($project->path(), [
            'notes' => 'new notes'
        ])->assertStatus(200);

        $response->assertJson([
            'data' => [
                'type' => 'projects',
                'attributes' => [
                    'notes' => 'new notes'
                ]
            ]
        ]);
    }

    public function test_only_authorized_users_can_delte_a_project()
    {
        $project = ProjectFactory::create();

        $this->json('DELETE', $project->path())->assertStatus(401);

        $this->signIn();

        $this->delete($project->path())->assertStatus(403);
    }

    public function test_user_can_delte_a_project()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->user, 'api')->delete($project->path())->assertStatus(204);

        $this->assertNull($project->fresh());
    }

    public function test_invited_user_cannot_delte_a_project()
    {
        $project = ProjectFactory::create();

        $another_user = factory(User::class)->create();
        $project->invite($another_user);

        $this->actingAs($another_user, 'api')->delete($project->path())->assertStatus(403); 
    }

    public function test_guest_cannot_manage_project()
    {
        $project = ProjectFactory::create();
        $response = $this->json('POST', '/api/projects', $project->toArray())->assertStatus(401);
        $response = $this->json('GET', '/api/projects')->assertStatus(401);
        $response = $this->json('GET', $project->path())->assertStatus(401);
    }


    public function test_user_can_fetch_a_specific_project_for_him()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->user, 'api')->get($project->path())
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'projects',
                    'id' => $project->id,
                    'attributes' => [
                        'title' => $project->title,
                        'description' => $project->description
                    ]
                ]
            ]);
    }

    public function test_user_cant_fetch_a_not_valid_project()
    {
        $this->signIn();
        $this->get('api/projects/11')
            ->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'title' => 'Model not found',
                    'description' => 'Resource requested not found'
                ]
            ]);
    }

    public function test_user_cannot_fetch_a_specific_project_for_others()
    {
        $this->signIn();
        $project = ProjectFactory::create();

        $this->get($project->path())
            ->assertStatus(403);
    }


    public function test_title_is_required_to_create_project()
    {
        $this->signIn();
        $attributes = factory(Project::class)->raw([
            'title' => ''
        ]);
        $response = $this->post('/api/projects', $attributes)->assertStatus(422);
        $this->assertArrayHasKey('title', $response['errors']['meta']);
    }

    public function test_description_is_required_to_create_project()
    {
        $this->signIn();
        $attributes = factory(Project::class)->raw([
            'description' => ''
        ]);

        $response = $this->post('/api/projects', $attributes)->assertStatus(422);
        $this->assertArrayHasKey('description', $response['errors']['meta']);
    }
}
