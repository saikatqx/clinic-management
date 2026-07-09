<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DiagnosticCategory;
use App\Models\Diagnostic;
use App\Models\DiagnosticBooking;
use App\Models\HealthPackage;
use App\Models\HealthPackageBooking;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class DiagnosticsAndPackagesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diagCategory;
    protected $pathCategory;
    protected $diagTest;
    protected $pathTest;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        // Diagnostic category and test
        $this->diagCategory = DiagnosticCategory::create([
            'name' => 'Cardiology Imaging',
            'type' => 'diag',
            'description' => 'Heart imaging',
            'status' => true,
        ]);

        $this->diagTest = Diagnostic::create([
            'diagnostic_category_id' => $this->diagCategory->id,
            'name' => 'Electrocardiogram (ECG)',
            'price' => 500.00,
            'status' => true,
        ]);

        // Pathology category and test
        $this->pathCategory = DiagnosticCategory::create([
            'name' => 'Blood Pathology',
            'type' => 'path',
            'description' => 'Blood testing',
            'status' => true,
        ]);

        $this->pathTest = Diagnostic::create([
            'diagnostic_category_id' => $this->pathCategory->id,
            'name' => 'Complete Blood Count (CBC)',
            'price' => 350.00,
            'status' => true,
        ]);
    }

    public function test_admin_can_access_diagnostic_categories_index()
    {
        $response = $this->actingAs($this->admin)->get('/admin/diagnostic-categories');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_diagnostic_category()
    {
        $response = $this->actingAs($this->admin)->post('/admin/diagnostic-categories', [
            'name' => 'Neurology Screen',
            'type' => 'diag',
            'description' => 'Brain imaging',
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.diagnostic-categories.index'));
        $this->assertDatabaseHas('diagnostic_categories', [
            'name' => 'Neurology Screen',
            'type' => 'diag'
        ]);
    }

    public function test_admin_can_create_diagnostic_test()
    {
        $response = $this->actingAs($this->admin)->post('/admin/diagnostics', [
            'diagnostic_category_id' => $this->diagCategory->id,
            'name' => 'Echocardiogram',
            'price' => 1200.00,
            'status' => 1,
        ]);

        $response->assertRedirect(route('admin.diagnostics.indexDiag'));
        $this->assertDatabaseHas('diagnostics', [
            'name' => 'Echocardiogram',
            'price' => 1200.00
        ]);
    }

    public function test_patient_can_book_diagnostic_test()
    {
        $response = $this->post('/diagnostics/store', [
            'patient_name' => 'John Doe',
            'mobile' => '9876543210',
            'email' => 'john@example.com',
            'collection_type' => 'clinic',
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '10:00 AM - 12:00 PM',
            'payment_method' => 'Cash',
            'test_ids' => [$this->diagTest->id]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('diagnostic_bookings', [
            'patient_name' => 'John Doe',
            'type' => 'diag',
            'total_amount' => 500.00,
            'payment_status' => 'pending'
        ]);
    }

    public function test_patient_can_book_pathology_test()
    {
        $response = $this->post('/pathology/store', [
            'patient_name' => 'Jane Smith',
            'mobile' => '9998887776',
            'email' => 'jane@example.com',
            'collection_type' => 'home',
            'address' => '123 Pathology Lane',
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '08:00 AM - 10:00 AM',
            'payment_method' => 'Card',
            'test_ids' => [$this->pathTest->id]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('diagnostic_bookings', [
            'patient_name' => 'Jane Smith',
            'type' => 'path',
            'total_amount' => 350.00,
            'payment_status' => 'paid'
        ]);
    }

    public function test_admin_can_reschedule_diagnostic_booking()
    {
        $booking = DiagnosticBooking::create([
            'booking_no' => 'DIAG-TEST123',
            'type' => 'diag',
            'patient_name' => 'John Doe',
            'mobile' => '9876543210',
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '10:00 AM - 12:00 PM',
            'collection_type' => 'clinic',
            'subtotal' => 500.00,
            'total_amount' => 500.00,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        $newDate = Carbon::tomorrow()->addDay()->toDateString();

        $response = $this->actingAs($this->admin)->post('/admin/diagnostic-bookings/reschedule', [
            'id' => $booking->id,
            'booking_date' => $newDate,
            'booking_time' => '12:00 PM - 02:00 PM',
        ]);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('diagnostic_bookings', [
            'id' => $booking->id,
            'booking_date' => $newDate,
            'booking_time' => '12:00 PM - 02:00 PM',
            'is_rescheduled' => true
        ]);
    }

    public function test_admin_can_reschedule_pathology_booking()
    {
        $booking = DiagnosticBooking::create([
            'booking_no' => 'PATH-TEST123',
            'type' => 'path',
            'patient_name' => 'Jane Smith',
            'mobile' => '9998887776',
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '08:00 AM - 10:00 AM',
            'collection_type' => 'home',
            'subtotal' => 350.00,
            'total_amount' => 350.00,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        $newDate = Carbon::tomorrow()->addDay()->toDateString();

        $response = $this->actingAs($this->admin)->post('/admin/pathology-bookings/reschedule', [
            'id' => $booking->id,
            'booking_date' => $newDate,
            'booking_time' => '12:00 PM - 02:00 PM',
        ]);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('diagnostic_bookings', [
            'id' => $booking->id,
            'booking_date' => $newDate,
            'booking_time' => '12:00 PM - 02:00 PM',
            'is_rescheduled' => true
        ]);
    }

    public function test_admin_can_upload_diagnostic_report_pdf()
    {
        $booking = DiagnosticBooking::create([
            'booking_no' => 'DIAG-REPORT123',
            'type' => 'diag',
            'patient_name' => 'John Doe',
            'mobile' => '9876543210',
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '10:00 AM - 12:00 PM',
            'collection_type' => 'clinic',
            'subtotal' => 500.00,
            'total_amount' => 500.00,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('report.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post('/admin/diagnostic-bookings/upload-report', [
            'id' => $booking->id,
            'report' => $file,
        ]);

        $response->assertRedirect();
        
        $booking->refresh();
        $this->assertNotNull($booking->report_pdf_path);
        $this->assertEquals('completed', $booking->booking_status);

        $filePath = public_path('reports/diagnostic/' . $booking->report_pdf_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function test_admin_can_upload_pathology_report_pdf()
    {
        $booking = DiagnosticBooking::create([
            'booking_no' => 'PATH-REPORT123',
            'type' => 'path',
            'patient_name' => 'Jane Smith',
            'mobile' => '9998887776',
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '08:00 AM - 10:00 AM',
            'collection_type' => 'home',
            'subtotal' => 350.00,
            'total_amount' => 350.00,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('report.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post('/admin/pathology-bookings/upload-report', [
            'id' => $booking->id,
            'report' => $file,
        ]);

        $response->assertRedirect();
        
        $booking->refresh();
        $this->assertNotNull($booking->report_pdf_path);
        $this->assertEquals('completed', $booking->booking_status);

        $filePath = public_path('reports/pathology/' . $booking->report_pdf_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function test_patient_can_book_health_package()
    {
        $package = HealthPackage::create([
            'name' => 'Full Body Screening',
            'description' => 'Comprehensive checkup',
            'actual_price' => 1000.00,
            'package_price' => 699.00,
            'status' => true,
            'gender' => 'BOTH',
        ]);

        $response = $this->post('/packages/store', [
            'patient_name' => 'Alice',
            'mobile' => '9998887776',
            'collection_type' => 'home',
            'address' => '123 Test Street',
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '08:00 AM - 10:00 AM',
            'payment_method' => 'Card',
            'health_package_id' => $package->id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('health_package_bookings', [
            'patient_name' => 'Alice',
            'mobile' => '9998887776',
            'total_amount' => 699.00,
            'payment_status' => 'paid'
        ]);
    }

    public function test_patient_can_check_diagnostic_booking_status()
    {
        $booking = \App\Models\DiagnosticBooking::create([
            'booking_no' => 'DIAG-TEST12345',
            'type' => 'diag',
            'patient_name' => 'John Doe',
            'mobile' => '9876543210',
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '10:00 AM - 12:00 PM',
            'collection_type' => 'clinic',
            'subtotal' => 200.00,
            'discount' => 0,
            'total_amount' => 200.00,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        $response = $this->post('/appointment/status', [
            'appointment_no' => 'DIAG-TEST12345'
        ]);

        $response->assertStatus(200);
        $response->assertSee('DIAG-TEST12345');
        $response->assertSee('John Doe');
    }
}
