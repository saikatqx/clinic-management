<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceFrontController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/admin', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/doctors-by-specialty/{id}', [HomeController::class, 'getBySpecialty'])
    ->name('doctors.bySpecialty');

Route::get('/services', [ServiceFrontController::class, 'index'])->name('services.index.public');
Route::get('/services/{service:slug}', [ServiceFrontController::class, 'show'])->name('services.show.public');

Route::get('/doctors', [DoctorFrontController::class, 'index'])->name('doctors.index.public');
Route::get('/doctors/{doctor}', [DoctorFrontController::class, 'show'])->name('doctors.show.public');

Route::get('/appointment', [AppointmentFrontController::class, 'create'])->name('appointments.create.public');
Route::post('/appointment', [AppointmentFrontController::class, 'store'])->name('appointments.store.public');

// Ajax slots (optional simple generator)
Route::get('/appointment/slots', [AppointmentFrontController::class, 'slots'])->name('appointments.slots');

Route::view('/contact', 'frontend.contact')->name('contact');

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
        Route::resource('appointments', AppointmentController::class);
        Route::get('appointments-data', [AppointmentController::class, 'data'])->name('appointments.data');
        Route::post('appointments/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

        // ✅ Fix: define custom routes before resource
        Route::get('banners/data', [BannerController::class, 'data'])->name('banners.data');
        Route::post('banners/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
        Route::resource('banners', BannerController::class);


        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/update', [SettingController::class, 'update'])->name('settings.update');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
