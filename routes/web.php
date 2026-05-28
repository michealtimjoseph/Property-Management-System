<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\StaffProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeasesController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\RenterController;
use App\Http\Controllers\ViewingsController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClientViewingsController;
use App\Http\Controllers\StaffLeasesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LeaseApplicationController;
use App\Http\Controllers\PropertyListingRequestController;
use App\Http\Controllers\InspectionController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// ===== TEMP: Password Fix (remove after use) =====
Route::get('/fix-my-password', function () {
    DB::table('staff')
        ->where('email', 'clint@gmail.com')
        ->update(['password' => Hash::make('pass123')]);
    return 'Password successfully encrypted to pass123!';
});

// ===== PUBLIC ROUTES =====
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/landing', fn() => redirect()->route('welcome'));

// ===== STAFF AUTH =====
Route::get('/staff/login', [StaffLoginController::class, 'showLoginForm'])->name('staff.login');
Route::post('/staff/login', [StaffLoginController::class, 'login']);

// ===== ALL STAFF PROTECTED ROUTES =====
// Role-based access is handled inside controllers, not route groups.
// This eliminates duplicate route names and middleware conflicts.
Route::middleware('auth:staff')->group(function () {

    // ----- Dashboard -----
    Route::get('/staff/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/dashboard/report', [DashboardController::class, 'downloadReport'])->name('staff.dashboard.report');
    Route::post('/staff/viewings/feedback', [DashboardController::class, 'updateViewingFeedback'])->name('staff.viewings.feedback');
    Route::patch('/inspections/{id}/complete', [DashboardController::class, 'completeInspection'])->name('staff.inspections.complete');

    // ----- Auth / Profile -----
    Route::post('/staff/logout', [StaffLoginController::class, 'logout'])->name('staff.logout');
    Route::get('/staff/profile', [StaffProfileController::class, 'edit'])->name('staff.profile.edit');
    Route::patch('/staff/profile', [StaffProfileController::class, 'update'])->name('staff.profile.update');

    // ----- Staff List -----
    Route::get('/staff/staff-list', [StaffProfileController::class, 'index'])->name('staff.staff');
    Route::get('/staff/create', [StaffProfileController::class, 'create'])->name('staff.create');
    Route::post('/staff/store', [StaffProfileController::class, 'store'])->name('staff.store');
    Route::get('/staff/staff-list/{id}', [StaffProfileController::class, 'show'])->name('staff.show');
    Route::get('/staff/staff-list/{id}/edit', [StaffProfileController::class, 'edit'])->name('staff.edit');
    Route::patch('/staff/staff-list/{id}', [StaffProfileController::class, 'update'])->name('staff.update');

    // ----- Branches -----
    Route::get('/staff/branches/create', [StaffProfileController::class, 'createBranch'])->name('staff.branches.create');
    Route::post('/staff/branches/store', [StaffProfileController::class, 'storeBranch'])->name('staff.branches.store');

    // ----- Properties -----
    Route::get('/staff/properties', [PropertiesController::class, 'index'])->name('staff.properties.properties');
    Route::get('/staff/properties/create', [PropertiesController::class, 'create'])->name('staff.properties.create');
    Route::post('/staff/properties/store', [PropertiesController::class, 'store'])->name('staff.properties.store');
    Route::get('/staff/properties/{id}/edit', [PropertiesController::class, 'editProperty'])->name('staff.properties.edit');
    Route::patch('/staff/properties/{id}', [PropertiesController::class, 'update'])->name('staff.properties.update');
    Route::get('/staff/properties/{id}', [PropertiesController::class, 'showProperty'])->name('staff.properties.show');

    // ----- Renters -----
    Route::get('/staff/renters', [RenterController::class, 'index'])->name('staff.renters.index');
    Route::get('/staff/renters/create', [RenterController::class, 'create'])->name('staff.renters.create');
    Route::post('/staff/renters', [RenterController::class, 'store'])->name('staff.renters.store');
    Route::get('/staff/renters/{id}/leases', [RenterController::class, 'history'])->name('staff.renters.leases');
    Route::get('/staff/renters/{id}/edit', [RenterController::class, 'edit'])->name('staff.renters.edit');
    Route::patch('/staff/renters/{id}', [RenterController::class, 'update'])->name('staff.renters.update');
    Route::get('/staff/renters/{id}', [RenterController::class, 'show'])->name('staff.renters.show');

    // ----- Owners -----
    Route::get('/owners/create', [RenterController::class, 'createOwner'])->name('staff.owners.create');
    Route::post('/owners/store', [RenterController::class, 'storeOwner'])->name('staff.owners.store');
    Route::get('/owners/{id}/edit', [RenterController::class, 'editOwner'])->name('staff.owners.edit');
    Route::patch('/owners/{id}', [RenterController::class, 'updateOwner'])->name('staff.owners.update');

    // ----- Viewings -----
    Route::get('/staff/viewings', [ViewingsController::class, 'index'])->name('staff.viewings');
    Route::get('/staff/viewings/create', [ViewingsController::class, 'create'])->name('staff.viewings.create');
    Route::get('/staff/viewings/create/{request_id?}', [ViewingsController::class, 'create'])->name('staff.viewings.create.prefill');
    Route::post('/staff/viewings', [ViewingsController::class, 'store'])->name('staff.viewings.store');
    Route::patch('/staff/viewings/{id}/assign', [ViewingsController::class, 'assign'])->name('staff.viewings.assign');
    Route::get('/staff/viewings/process/{request_id}', [ViewingsController::class, 'processRequest'])->name('staff.viewings.process');

    // ----- Leases -----
    Route::get('/staff/leases', [StaffLeasesController::class, 'index'])->name('staff.leases.index');
    Route::get('/staff/leases/create', [StaffLeasesController::class, 'create'])->name('staff.leases.create');
    Route::post('/staff/leases/store', [StaffLeasesController::class, 'store'])->name('staff.leases.store');
    Route::post('/staff/leases/payment', [StaffLeasesController::class, 'processPayment'])->name('staff.leases.process_payment');
    Route::get('/staff/leases/edit/{id}', [StaffLeasesController::class, 'edit'])->name('staff.leases.edit');
    Route::patch('/staff/leases/update/{id}', [StaffLeasesController::class, 'update'])->name('staff.leases.update');
    Route::get('/staff/leases/{id}', [StaffLeasesController::class, 'show'])->name('staff.leases.show');

    // ----- Lease Applications -----
    Route::get('/staff/applications', [LeaseApplicationController::class, 'staffIndex'])->name('staff.applications');
    Route::patch('/staff/applications/{id}/approve', [LeaseApplicationController::class, 'approve'])->name('staff.applications.approve');
    Route::patch('/staff/applications/{id}/reject', [LeaseApplicationController::class, 'reject'])->name('staff.applications.reject');

    // ----- Property Listing Requests -----
    Route::get('/staff/listing-requests', [PropertyListingRequestController::class, 'staffIndex'])->name('staff.listing-requests.index');
    Route::patch('/staff/listing-requests/{id}/approve', [PropertyListingRequestController::class, 'approve'])->name('staff.listing-requests.approve');
    Route::patch('/staff/listing-requests/{id}/reject', [PropertyListingRequestController::class, 'reject'])->name('staff.listing-requests.reject');

    // ----- Inspections -----
    Route::get('/staff/inspections', [InspectionController::class, 'index'])->name('staff.inspections');
    Route::post('/staff/inspections', [InspectionController::class, 'store'])->name('staff.inspections.store');

    // ----- Reports (manager only — enforced in ReportController) -----
    Route::get('/staff/reports', [ReportController::class, 'index'])->name('staff.reports');
    Route::get('/staff/reports/generate', [ReportController::class, 'generate'])->name('staff.reports.generate');

});

// ===== CLIENT AUTH ROUTES =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Leases
    Route::get('/leases', [LeasesController::class, 'index'])->name('leases');
    Route::get('/leases/pdf', [LeasesController::class, 'downloadPdf'])->name('leases.pdf');
    Route::post('/leases/renewal', [LeasesController::class, 'requestRenewal'])->name('leases.renewal');
    Route::post('/leases/support', [LeasesController::class, 'contactSupport'])->name('leases.support');
    Route::post('/leases/pay-advance', [LeasesController::class, 'processPayment'])->name('renter.payments.process');

    // Lease Applications
    Route::get('/applications', [LeaseApplicationController::class, 'index'])->name('applications');
    Route::post('/applications/apply', [LeaseApplicationController::class, 'store'])->name('applications.store');

    // Property Listing Requests
    Route::get('/listing-requests', [PropertyListingRequestController::class, 'index'])->name('listing-requests');
    Route::post('/listing-requests/submit', [PropertyListingRequestController::class, 'store'])->name('listing-requests.store');

    // Viewings
    Route::get('/viewings', [ClientViewingsController::class, 'index'])->name('viewings');
    Route::post('/viewings/book', [ClientViewingsController::class, 'store'])->name('viewings.book');
    Route::get('/viewings/book', fn() => redirect()->route('home')); // GET fallback
});

require __DIR__.'/auth.php';