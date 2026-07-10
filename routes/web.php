<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceFrontController;
use App\Http\Controllers\Public\DoctorFrontController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\AppointmentFrontController;
use App\Http\Controllers\Frontend\ChatBotController;
use App\Http\Controllers\Admin\DiagnosticCategoryController;
use App\Http\Controllers\Admin\DiagnosticController;
use App\Http\Controllers\Admin\HealthPackageController;
use App\Http\Controllers\Admin\DiagnosticBookingController;
use App\Http\Controllers\Admin\HealthPackageBookingController;
use App\Http\Controllers\Admin\PathologyBookingController;
use App\Http\Controllers\Public\PublicDiagnosticBookingController;
use App\Http\Controllers\Public\PublicHealthPackageController;
use App\Http\Controllers\Public\PublicPathologyBookingController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/admin', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/clear-cache', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    Artisan::call('permission:cache-reset');

    return "Cache Cleared!";
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');
Route::get('/chatbot', [ChatBotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/message', [ChatBotController::class, 'send'])->name('chatbot.send');
Route::post('/chatbot/triage', [ChatBotController::class, 'triage'])->name('chatbot.triage');

Route::get('/doctors-by-specialty/{id}', [HomeController::class, 'getBySpecialty'])
    ->name('doctors.bySpecialty');

Route::get('/services', [ServiceFrontController::class, 'index'])->name('services.index.public');
Route::get('/services/{id}', [ServiceFrontController::class, 'show'])->name('services.show.public');

Route::get('/doctors', [DoctorFrontController::class, 'index'])->name('doctors.index.public');
Route::get('/doctors/{doctor}', [DoctorFrontController::class, 'show'])->name('doctors.show.public');

Route::resource('appointments', AppointmentFrontController::class)->names([
    'index' => 'appointments.index.public',
    // 'create' => 'appointments.create.public',
    'store' => 'appointments.store.public',
    'show' => 'appointments.show.public',
]);
Route::get('/appointment/status', [AppointmentFrontController::class, 'status'])->name('appointments.status');
Route::post('/appointment/status', [AppointmentFrontController::class, 'checkStatus'])->name('appointments.status.check');

Route::get('/appointments/{id}/prescription', [AppointmentFrontController::class, 'downloadPrescription'])
     ->name('appointments.prescription.download');

// Ajax slots (optional simple generator)
Route::get('/appointment/slots', [AppointmentFrontController::class, 'slots'])->name('appointments.slots');

// Diagnostics public routes
Route::get('/diagnostics', [PublicDiagnosticBookingController::class, 'index'])->name('diagnostics.index.public');
Route::post('/diagnostics/book', [PublicDiagnosticBookingController::class, 'book'])->name('diagnostics.book.public');
Route::post('/diagnostics/store', [PublicDiagnosticBookingController::class, 'store'])->name('diagnostics.store.public');
Route::get('/diagnostics/success', [PublicDiagnosticBookingController::class, 'success'])->name('diagnostics.success');

// Health Packages public routes
Route::get('/packages', [PublicHealthPackageController::class, 'index'])->name('packages.index.public');
Route::post('/packages/book', [PublicHealthPackageController::class, 'book'])->name('packages.book.public');
Route::post('/packages/store', [PublicHealthPackageController::class, 'store'])->name('packages.store.public');
Route::get('/packages/success', [PublicHealthPackageController::class, 'success'])->name('packages.success');

// Pathology public routes
Route::get('/pathology', [PublicPathologyBookingController::class, 'index'])->name('pathology.index.public');
Route::post('/pathology/book', [PublicPathologyBookingController::class, 'book'])->name('pathology.book.public');
Route::post('/pathology/store', [PublicPathologyBookingController::class, 'store'])->name('pathology.store.public');
Route::get('/pathology/success', [PublicPathologyBookingController::class, 'success'])->name('pathology.success');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        Route::get('/specialties/data', [SpecialtyController::class, 'data'])->name('specialties.data');
        // Specialties CRUD
        Route::resource('specialties', SpecialtyController::class);
        Route::post('specialties/toggle-status', [SpecialtyController::class, 'toggleStatus'])
            ->name('specialties.toggleStatus');

        // Doctors CRUD
        Route::get('/doctors/data', [DoctorController::class, 'data'])->name('doctors.data');
        Route::resource('doctors', DoctorController::class);
        Route::post('doctors/toggle-status', [DoctorController::class, 'toggleStatus'])
            ->name('doctors.toggleStatus');

        // Services CRUD
        Route::get('/services/data', [ServiceController::class, 'data'])->name('services.data');
        Route::resource('services', ServiceController::class);
        Route::post('services/toggle-status', [ServiceController::class, 'toggleStatus'])
            ->name('services.toggleStatus');

        // Appointments CRUD
        Route::get('appointments/calendar-events', [AppointmentController::class, 'calendarEvents'])->name('appointments.calendarEvents');
        Route::post('appointments/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::resource('appointments', AppointmentController::class);
        Route::get('appointments-data', [AppointmentController::class, 'data'])->name('appointments.data');
        Route::post('appointments/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

        Route::get('appointments/{id}/prescription', [AppointmentController::class, 'createPrescription'])->name('appointments.prescription');


        Route::post('appointments/{id}/prescription', [AppointmentController::class, 'storePrescription'])
            ->name('appointments.prescription.store');

        // ✅ Fix: define custom routes before resource
        Route::get('banners/data', [BannerController::class, 'data'])->name('banners.data');
        Route::post('banners/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
        Route::resource('banners', BannerController::class);

        // Doctor Availabilities
        Route::get('availabilities/data', [AvailabilityController::class, 'data'])->name('availabilities.data');
        Route::post('availabilities', [AvailabilityController::class, 'store'])->name('availabilities.store');
        Route::delete('availabilities/{id}', [AvailabilityController::class, 'destroy'])->name('availabilities.destroy');
        Route::get('availabilities', [AvailabilityController::class, 'index'])->name('availabilities.index');

        // Diagnostic Categories CRUD
        Route::get('diagnostic-categories/data', [DiagnosticCategoryController::class, 'data'])->name('diagnostic-categories.data');
        Route::post('diagnostic-categories/toggle-status', [DiagnosticCategoryController::class, 'toggleStatus'])->name('diagnostic-categories.toggleStatus');
        Route::resource('diagnostic-categories', DiagnosticCategoryController::class);

        // Diagnostics (Tests) CRUD
        Route::get('diagnostics/diag', [DiagnosticController::class, 'indexDiag'])->name('diagnostics.indexDiag');
        Route::get('diagnostics/path', [DiagnosticController::class, 'indexPath'])->name('diagnostics.indexPath');
        Route::get('diagnostics/data/{type?}', [DiagnosticController::class, 'data'])->name('diagnostics.data');
        Route::post('diagnostics/toggle-status', [DiagnosticController::class, 'toggleStatus'])->name('diagnostics.toggleStatus');
        Route::resource('diagnostics', DiagnosticController::class)->except(['index']);

        // Health Packages CRUD
        Route::get('health-packages/data', [HealthPackageController::class, 'data'])->name('health-packages.data');
        Route::post('health-packages/toggle-status', [HealthPackageController::class, 'toggleStatus'])->name('health-packages.toggleStatus');
        Route::resource('health-packages', HealthPackageController::class);

        // Diagnostic Bookings
        Route::get('diagnostic-bookings/data', [DiagnosticBookingController::class, 'data'])->name('diagnostic-bookings.data');
        Route::post('diagnostic-bookings/update-status', [DiagnosticBookingController::class, 'updateStatus'])->name('diagnostic-bookings.updateStatus');
        Route::post('diagnostic-bookings/reschedule', [DiagnosticBookingController::class, 'reschedule'])->name('diagnostic-bookings.reschedule');
        Route::post('diagnostic-bookings/upload-report', [DiagnosticBookingController::class, 'uploadReport'])->name('diagnostic-bookings.uploadReport');
        Route::resource('diagnostic-bookings', DiagnosticBookingController::class);

        // Health Package Bookings
        Route::get('health-package-bookings/data', [HealthPackageBookingController::class, 'data'])->name('health-package-bookings.data');
        Route::post('health-package-bookings/update-status', [HealthPackageBookingController::class, 'updateStatus'])->name('health-package-bookings.updateStatus');
        Route::post('health-package-bookings/reschedule', [HealthPackageBookingController::class, 'reschedule'])->name('health-package-bookings.reschedule');
        Route::post('health-package-bookings/upload-report', [HealthPackageBookingController::class, 'uploadReport'])->name('health-package-bookings.uploadReport');
        Route::resource('health-package-bookings', HealthPackageBookingController::class);

        // Pathology Bookings
        Route::get('pathology-bookings/data', [PathologyBookingController::class, 'data'])->name('pathology-bookings.data');
        Route::post('pathology-bookings/update-status', [PathologyBookingController::class, 'updateStatus'])->name('pathology-bookings.updateStatus');
        Route::post('pathology-bookings/reschedule', [PathologyBookingController::class, 'reschedule'])->name('pathology-bookings.reschedule');
        Route::post('pathology-bookings/upload-report', [PathologyBookingController::class, 'uploadReport'])->name('pathology-bookings.uploadReport');
        Route::resource('pathology-bookings', PathologyBookingController::class);

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/update', [SettingController::class, 'update'])->name('settings.update');

        // Access Control — permission-based (not role-based)
        // Roles CRUD
        Route::get('/roles', [RolePermissionController::class, 'rolesIndex'])->name('roles.index')->middleware('can:view roles');
        Route::post('/roles', [RolePermissionController::class, 'rolesStore'])->name('roles.store')->middleware('can:create roles');
        Route::put('/roles/{id}', [RolePermissionController::class, 'rolesUpdate'])->name('roles.update')->middleware('can:edit roles');
        Route::delete('/roles/{id}', [RolePermissionController::class, 'rolesDestroy'])->name('roles.destroy')->middleware('can:delete roles');

        // Manage Permissions
        Route::get('/roles/{id}/permissions', [RolePermissionController::class, 'manageRolePermissions'])->name('roles.permissions')->middleware('can:assign role permissions');
        Route::put('/roles/{id}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.permissions.update')->middleware('can:assign role permissions');

        // Assign Role
        Route::get('/assign-role', [RolePermissionController::class, 'assignRoleIndex'])->name('assign-role.index')->middleware('can:assign user roles');
        Route::post('/assign-role', [RolePermissionController::class, 'assignRoleUpdate'])->name('assign-role.update')->middleware('can:assign user roles');

        // Permissions CRUD
        Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])->name('permissions.index')->middleware('can:view permissions');
        Route::post('/permissions', [RolePermissionController::class, 'permissionsStore'])->name('permissions.store')->middleware('can:create permissions');
        Route::put('/permissions/{id}', [RolePermissionController::class, 'permissionsUpdate'])->name('permissions.update')->middleware('can:edit permissions');
        Route::delete('/permissions/{id}', [RolePermissionController::class, 'permissionsDestroy'])->name('permissions.destroy')->middleware('can:delete permissions');

        // Users CRUD
        Route::get('/users/data', [UserController::class, 'data'])->name('users.data')->middleware('can:view users');
        Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('can:view users');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('can:create users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('can:create users');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('can:edit users');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:edit users');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:delete users');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
