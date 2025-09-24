<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\DoctorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Route::get('/dashboard', function () {
//     // return view('dashboard');
//     return view('admin.dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

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
        // Route::resource('services', ServiceController::class);

        // Appointments CRUD
        // Route::resource('appointments', AppointmentController::class);
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
