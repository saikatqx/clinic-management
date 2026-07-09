<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by feature for clinic management
        $permissions = [
            // Dashboard
            ['name' => 'view dashboard', 'group_name' => 'Dashboard'],

            // Specialties
            ['name' => 'view specialties', 'group_name' => 'Specialties'],
            ['name' => 'create specialties', 'group_name' => 'Specialties'],
            ['name' => 'edit specialties', 'group_name' => 'Specialties'],
            ['name' => 'delete specialties', 'group_name' => 'Specialties'],
            ['name' => 'toggle specialties', 'group_name' => 'Specialties'],

            // Doctors
            ['name' => 'view doctors', 'group_name' => 'Doctors'],
            ['name' => 'create doctors', 'group_name' => 'Doctors'],
            ['name' => 'edit doctors', 'group_name' => 'Doctors'],
            ['name' => 'delete doctors', 'group_name' => 'Doctors'],
            ['name' => 'toggle doctors', 'group_name' => 'Doctors'],

            // Availabilities
            ['name' => 'view availabilities', 'group_name' => 'Availabilities'],
            ['name' => 'create availabilities', 'group_name' => 'Availabilities'],
            ['name' => 'edit availabilities', 'group_name' => 'Availabilities'],
            ['name' => 'delete availabilities', 'group_name' => 'Availabilities'],
            ['name' => 'toggle availabilities', 'group_name' => 'Availabilities'],

            // Services
            ['name' => 'view services', 'group_name' => 'Services'],
            ['name' => 'create services', 'group_name' => 'Services'],
            ['name' => 'edit services', 'group_name' => 'Services'],
            ['name' => 'delete services', 'group_name' => 'Services'],
            ['name' => 'toggle services', 'group_name' => 'Services'],

            // Appointments
            ['name' => 'view appointments', 'group_name' => 'Appointments'],
            ['name' => 'create appointments', 'group_name' => 'Appointments'],
            ['name' => 'edit appointments', 'group_name' => 'Appointments'],
            ['name' => 'update appointment status', 'group_name' => 'Appointments'],
            ['name' => 'delete appointments', 'group_name' => 'Appointments'],

            // Banners
            ['name' => 'view banners', 'group_name' => 'Banners'],
            ['name' => 'create banners', 'group_name' => 'Banners'],
            ['name' => 'edit banners', 'group_name' => 'Banners'],
            ['name' => 'delete banners', 'group_name' => 'Banners'],
            ['name' => 'toggle banners', 'group_name' => 'Banners'],

            // Diagnostic Categories
            ['name' => 'view diagnostic categories', 'group_name' => 'Diagnostic Categories'],
            ['name' => 'create diagnostic categories', 'group_name' => 'Diagnostic Categories'],
            ['name' => 'edit diagnostic categories', 'group_name' => 'Diagnostic Categories'],
            ['name' => 'delete diagnostic categories', 'group_name' => 'Diagnostic Categories'],
            ['name' => 'toggle diagnostic categories', 'group_name' => 'Diagnostic Categories'],

            // Diagnostic Tests
            ['name' => 'view diagnostic tests', 'group_name' => 'Diagnostic Tests'],
            ['name' => 'create diagnostic tests', 'group_name' => 'Diagnostic Tests'],
            ['name' => 'edit diagnostic tests', 'group_name' => 'Diagnostic Tests'],
            ['name' => 'delete diagnostic tests', 'group_name' => 'Diagnostic Tests'],
            ['name' => 'toggle diagnostic tests', 'group_name' => 'Diagnostic Tests'],

            // Bookings (Lab / Diagnostic / Pathology / Package)
            ['name' => 'view bookings', 'group_name' => 'Lab Bookings'],
            ['name' => 'update booking status', 'group_name' => 'Lab Bookings'],
            ['name' => 'upload reports', 'group_name' => 'Lab Bookings'],

            // Health Packages
            ['name' => 'view health packages', 'group_name' => 'Health Packages'],
            ['name' => 'create health packages', 'group_name' => 'Health Packages'],
            ['name' => 'edit health packages', 'group_name' => 'Health Packages'],
            ['name' => 'delete health packages', 'group_name' => 'Health Packages'],
            ['name' => 'toggle health packages', 'group_name' => 'Health Packages'],

            // Access Control
            ['name' => 'view roles', 'group_name' => 'Access Control'],
            ['name' => 'create roles', 'group_name' => 'Access Control'],
            ['name' => 'edit roles', 'group_name' => 'Access Control'],
            ['name' => 'delete roles', 'group_name' => 'Access Control'],
            ['name' => 'assign role permissions', 'group_name' => 'Access Control'],
            ['name' => 'view permissions', 'group_name' => 'Access Control'],
            ['name' => 'create permissions', 'group_name' => 'Access Control'],
            ['name' => 'edit permissions', 'group_name' => 'Access Control'],
            ['name' => 'delete permissions', 'group_name' => 'Access Control'],
            ['name' => 'assign user roles', 'group_name' => 'Access Control'],
            ['name' => 'view users', 'group_name' => 'Access Control'],
            ['name' => 'create users', 'group_name' => 'Access Control'],
            ['name' => 'edit users', 'group_name' => 'Access Control'],
            ['name' => 'delete users', 'group_name' => 'Access Control'],

            // Settings
            ['name' => 'view settings', 'group_name' => 'Settings'],
            ['name' => 'update settings', 'group_name' => 'Settings'],
        ];

        // Seed permissions
        $dbPermissions = [];
        foreach ($permissions as $perm) {
            $dbPermissions[] = Permission::firstOrCreate(
                ['name' => $perm['name']],
                [
                    'group_name' => $perm['group_name'],
                    'guard_name' => 'web'
                ]
            );
        }

        // Get or Create Roles (lowercase to match route middleware in clinic-management)
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $receptionist = Role::firstOrCreate(['name' => 'receptionist']);
        $patient = Role::firstOrCreate(['name' => 'patient']);

        // Assign permissions to roles
        $permissionIds = collect($dbPermissions)->pluck('id')->toArray();

        // Super Admin and Admin get all permissions
        if ($superAdmin) {
            $superAdmin->permissions()->sync($permissionIds);
        }
        if ($admin) {
            $admin->permissions()->sync($permissionIds);
        }

        // Doctor permissions
        if ($doctor) {
            $doctorPermissions = Permission::whereIn('group_name', [
                'Dashboard', 'Availabilities'
            ])->orWhereIn('name', [
                'view appointments', 'edit appointments', 'update appointment status', 'view specialties', 'view doctors', 'view services'
            ])->pluck('id')->toArray();
            $doctor->permissions()->sync($doctorPermissions);
        }

        // Receptionist permissions
        if ($receptionist) {
            $receptionistPermissions = Permission::whereIn('group_name', [
                'Dashboard', 'Appointments', 'Lab Bookings'
            ])->orWhereIn('name', [
                'view specialties', 'view doctors', 'view services', 'view health packages'
            ])->pluck('id')->toArray();
            $receptionist->permissions()->sync($receptionistPermissions);
        }

        // Patient permissions (none or view dashboard only)
        if ($patient) {
            $patientPermissions = Permission::whereIn('name', [
                'view dashboard'
            ])->pluck('id')->toArray();
            $patient->permissions()->sync($patientPermissions);
        }

        // Assign Admin role to the default user test@example.com
        $testUser = User::where('email', 'test@example.com')->first();
        if ($testUser && $admin) {
            $testUser->roles()->sync([$admin->id]);
        }
    }
}
