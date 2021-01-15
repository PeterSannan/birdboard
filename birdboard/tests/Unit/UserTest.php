<?php

namespace Tests\Unit;

use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_invite_user()
    {
        $project = ProjectFactory::create();

        $project->invite($newuser = factory(User::class)->create());

        $this->assertTrue($project->members->contains($newuser));
    }

    public function test_it_has_accessible_projects() 
    {
        ProjectFactory::owned_by($john = factory(User::class)->create())->create();
 
        $project = ProjectFactory::owned_by($alex = factory(User::class)->create())->create();


        $project1 = ProjectFactory::owned_by($alex)->create();
        $project1->invite($sami = factory(User::class)->create());

        $this->assertCount(1, $john->allProjects());
        
        $project->invite($john);
        $this->assertCount(2, $john->fresh()->allProjects());
    }
}
