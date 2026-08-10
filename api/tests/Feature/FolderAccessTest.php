<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FolderAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    private function createUser(string $roleName): User
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_admin_can_create_folder(): void
    {
        Sanctum::actingAs($this->createUser('Administrator'));

        $response = $this->postJson('/api/folders', ['name' => 'Folder Baru']);

        $response->assertStatus(201);
        $this->assertDatabaseHas('folders', ['name' => 'Folder Baru']);
    }

    public function test_viewer_cannot_create_folder(): void
    {
        Sanctum::actingAs($this->createUser('Viewer'));

        $response = $this->postJson('/api/folders', ['name' => 'Folder Ditolak']);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('folders', ['name' => 'Folder Ditolak']);
    }

    public function test_viewer_can_view_folders(): void
    {
        Folder::create(['name' => 'Folder Publik']);
        Sanctum::actingAs($this->createUser('Viewer'));

        $response = $this->getJson('/api/folders');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', 'Folder Publik');
    }
}
