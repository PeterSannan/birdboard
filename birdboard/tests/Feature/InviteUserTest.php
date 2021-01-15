<?php

namespace Tests\Feature;

use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InviteUserTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
        
    }
    public function test_user_can_upload_picture(){ 
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $file = UploadedFile::fake()->image('user-image.png');

        $this->actingAs($user,'api')->post('api/userimage',[
            'image' => $file
        ])->assertStatus(200);

        Storage::disk('s3')->assertExists('user-images/'. $file->hashName());
    }

    public function test_project_can_invite_user(){ 
        $project = ProjectFactory::create();
        $anotherUser = factory(User::class)->create();
        $this->actingAs($project->user, 'api')->post($project->path().'/invite',[
            'invited_user_email' => $anotherUser->email
        ])->assertStatus(201);
        $this->assertTrue($project->members->contains($anotherUser));
    }

    public function test_user_invited_must_be_birboard_account(){ 
        $project = ProjectFactory::create();
        $anotherUser = factory(User::class)->create();
        $response = $this->actingAs($project->user, 'api')->post($project->path().'/invite',[
            'invited_user_email' =>'peter.ssss@gmail.com'
        ])->assertStatus(422);

        $this->assertArrayHasKey('invited_user_email', $response['errors']['meta']);
    }

    public function test_owner_only_can_invite_users(){ 
        $project = ProjectFactory::create();
        $anotherUser = factory(User::class)->create();
        $response = $this->actingAs($anotherUser, 'api')->post($project->path().'/invite',[
            'invited_user_email' =>factory(User::class)->create()->email
        ])->assertStatus(403);

        $project->invite($anotherUser);

        $response = $this->actingAs($anotherUser, 'api')->post($project->path().'/invite',[
            'invited_user_email' =>factory(User::class)->create()->email
        ])->assertStatus(403);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_invited_user_can_create_task()
    {
        $project = ProjectFactory::create();
        
        $project->invite($newuser = factory(User::class)->create());

        $this->signIn($newuser);

        $this->post($project->path() . '/tasks', [
            'body' => 'test'
        ])->assertStatus(201);
    }
}
