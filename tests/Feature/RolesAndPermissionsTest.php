<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $doctorUser;

    public function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions using our new seeder
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        // Find or create seeded users/admins
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');

        $this->doctorUser = User::factory()->create(['role' => 'doctor']);
        $this->doctorUser->assignRole('doctor');
    }

    /** @test */
    public function admin_can_access_roles_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.roles.index'));
        $response->assertStatus(200);
        $response->assertSee('Role List');
    }

    /** @test */
    public function non_admin_is_blocked_from_roles_index()
    {
        $response = $this->actingAs($this->doctorUser)->get(route('admin.roles.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_custom_role()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.roles.store'), [
            'name' => 'Receptionist Staff',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['name' => 'receptionist staff']);
    }

    /** @test */
    public function role_name_must_be_unique()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.roles.store'), [
            'name' => 'admin', // already exists
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_can_update_role_permissions()
    {
        $customRole = Role::create(['name' => 'custom role']);

        $response = $this->actingAs($this->admin)->put(route('admin.roles.permissions.update', $customRole->id), [
            'permissions' => ['view specialties', 'view bookings'],
        ]);

        $response->assertRedirect();
        $this->assertTrue($customRole->hasPermissionTo('view specialties'));
        $this->assertTrue($customRole->hasPermissionTo('view bookings'));
    }

    /** @test */
    public function core_roles_cannot_be_deleted()
    {
        $adminRole = Role::findByName('admin');

        $response = $this->actingAs($this->admin)->delete(route('admin.roles.destroy', $adminRole->id));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    /** @test */
    public function admin_can_assign_roles_to_users()
    {
        $patientUser = User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($this->admin)->post(route('admin.assign-role.update'), [
            'user_id' => $patientUser->id,
            'roles' => ['doctor'],
        ]);

        $response->assertRedirect();
        $this->assertTrue($patientUser->fresh()->hasRole('doctor'));
        $this->assertEquals('doctor', $patientUser->fresh()->role); // Fallback database column updated
    }
}
