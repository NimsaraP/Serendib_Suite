<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // A. Factory Tests
    // ==========================================

    public function test_invoice_factory_creates_valid_invoice_records(): void
    {
        $invoice = Invoice::factory()->create();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'payment_status' => 'Unpaid',
        ]);

        $this->assertNotNull($invoice->id);
        $this->assertNotNull($invoice->booking_id);
        $this->assertNotNull($invoice->client_id);
    }

    public function test_invoice_belongs_to_the_correct_booking(): void
    {
        $invoice = Invoice::factory()->create();

        $this->assertInstanceOf(Booking::class, $invoice->booking);
        $this->assertSame($invoice->booking_id, $invoice->booking->id);
    }

    public function test_invoice_belongs_to_the_correct_client(): void
    {
        $invoice = Invoice::factory()->create();

        $this->assertInstanceOf(User::class, $invoice->client);
        $this->assertSame($invoice->client_id, $invoice->client->id);
        $this->assertSame($invoice->client_id, $invoice->booking->client_id);
    }

    public function test_invoice_number_is_unique(): void
    {
        $booking1 = Booking::factory()->create();
        $booking2 = Booking::factory()->create();

        Invoice::factory()->create([
            'booking_id' => $booking1->id,
            'client_id' => $booking1->client_id,
            'invoice_number' => 'INV-UNIQUE-001',
        ]);

        $this->expectException(QueryException::class);
        Invoice::factory()->create([
            'booking_id' => $booking2->id,
            'client_id' => $booking2->client_id,
            'invoice_number' => 'INV-UNIQUE-001',
        ]);
    }

    // ==========================================
    // B. Relationship Tests
    // ==========================================

    public function test_booking_has_one_invoice_relationship(): void
    {
        $booking = Booking::factory()->create();
        $invoice = Invoice::factory()->create([
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
        ]);

        $this->assertInstanceOf(Invoice::class, $booking->invoice);
        $this->assertTrue($booking->invoice->is($invoice));
    }

    public function test_user_can_retrieve_their_client_invoices(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $booking = Booking::factory()->create(['client_id' => $client->id]);
        $invoice = Invoice::factory()->create([
            'booking_id' => $booking->id,
            'client_id' => $client->id,
        ]);

        $this->assertTrue($client->clientInvoices->contains($invoice));
    }

    // ==========================================
    // C. Financial Tests
    // ==========================================

    public function test_invoice_amounts_and_decimal_casts_work_correctly(): void
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 500.00,
            'amount_paid' => 125.50,
        ]);

        $this->assertSame('500.00', $invoice->total_amount);
        $this->assertSame('125.50', $invoice->amount_paid);
    }

    public function test_balance_due_accessor_calculates_correctly(): void
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 500.00,
            'amount_paid' => 125.50,
        ]);

        $this->assertSame('374.50', $invoice->balance_due);
    }

    public function test_amount_paid_cannot_exceed_total_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Invoice::factory()->create([
            'total_amount' => 100.00,
            'amount_paid' => 150.00,
        ]);
    }

    // ==========================================
    // D. Payment Status Tests
    // ==========================================

    public function test_payment_status_factory_states_and_scopes_work_correctly(): void
    {
        $unpaid = Invoice::factory()->unpaid()->create([
            'total_amount' => 200.00,
        ]);
        $partiallyPaid = Invoice::factory()->partiallyPaid(50.00)->create([
            'total_amount' => 200.00,
        ]);
        $paid = Invoice::factory()->paid()->create([
            'total_amount' => 200.00,
        ]);

        $this->assertSame('Unpaid', $unpaid->payment_status);
        $this->assertSame('0.00', $unpaid->amount_paid);
        $this->assertSame('200.00', $unpaid->balance_due);

        $this->assertSame('Partially Paid', $partiallyPaid->payment_status);
        $this->assertSame('50.00', $partiallyPaid->amount_paid);
        $this->assertSame('150.00', $partiallyPaid->balance_due);

        $this->assertSame('Paid', $paid->payment_status);
        $this->assertSame('200.00', $paid->amount_paid);
        $this->assertSame('0.00', $paid->balance_due);

        $unpaidResults = Invoice::unpaid()->get();
        $this->assertTrue($unpaidResults->contains($unpaid));
        $this->assertFalse($unpaidResults->contains($partiallyPaid));

        $partiallyPaidResults = Invoice::partiallyPaid()->get();
        $this->assertTrue($partiallyPaidResults->contains($partiallyPaid));
        $this->assertFalse($partiallyPaidResults->contains($paid));

        $paidResults = Invoice::paid()->get();
        $this->assertTrue($paidResults->contains($paid));
        $this->assertFalse($paidResults->contains($unpaid));
    }

    // ==========================================
    // E. Database Integrity Tests
    // ==========================================

    public function test_a_booking_cannot_have_multiple_invoices(): void
    {
        $booking = Booking::factory()->create();

        Invoice::factory()->create([
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
            'invoice_number' => 'INV-DUP-001',
        ]);

        $this->expectException(QueryException::class);
        Invoice::factory()->create([
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
            'invoice_number' => 'INV-DUP-002',
        ]);
    }

    public function test_an_invoice_cannot_reference_a_nonexistent_booking(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->expectException(QueryException::class);
        Invoice::create([
            'booking_id' => 999999,
            'client_id' => $client->id,
            'invoice_number' => 'INV-NO-BOOKING',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'total_amount' => 100.00,
            'amount_paid' => 0.00,
            'payment_status' => 'Unpaid',
        ]);
    }

    public function test_an_invoice_cannot_reference_a_nonexistent_client(): void
    {
        $booking = Booking::factory()->create();

        $this->expectException(QueryException::class);
        DB::table('invoices')->insert([
            'booking_id' => $booking->id,
            'client_id' => 999999,
            'invoice_number' => 'INV-NO-CLIENT',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'total_amount' => 100.00,
            'amount_paid' => 0.00,
            'payment_status' => 'Unpaid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_a_booking_referenced_by_an_invoice_cannot_be_deleted(): void
    {
        $invoice = Invoice::factory()->create();

        $this->expectException(QueryException::class);
        $invoice->booking->delete();
    }

    public function test_a_client_referenced_by_an_invoice_cannot_be_deleted(): void
    {
        $invoice = Invoice::factory()->create();

        $this->expectException(QueryException::class);
        $invoice->client->delete();
    }

    public function test_invoice_client_must_match_booking_client(): void
    {
        $booking = Booking::factory()->create();
        $otherClient = User::factory()->create(['role' => 'client']);

        $this->expectException(InvalidArgumentException::class);
        Invoice::factory()->create([
            'booking_id' => $booking->id,
            'client_id' => $otherClient->id,
        ]);
    }

    // ==========================================
    // F. Historical Integrity Tests
    // ==========================================

    public function test_changing_package_price_does_not_change_existing_booking_or_invoice_amount(): void
    {
        $package = Package::factory()->create(['price' => 200.00]);
        $booking = Booking::factory()->create([
            'package_id' => $package->id,
            'package_price' => $package->price,
        ]);
        $invoice = Invoice::factory()->create([
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
            'total_amount' => $booking->package_price,
        ]);

        // Photographer modifies catalog package price
        $package->update(['price' => 500.00]);

        $booking->refresh();
        $invoice->refresh();

        $this->assertSame('200.00', $booking->package_price);
        $this->assertSame('200.00', $invoice->total_amount);
        $this->assertSame('500.00', $package->fresh()->price);
    }

    public function test_changing_booking_package_price_does_not_silently_change_existing_invoice_total_amount(): void
    {
        $booking = Booking::factory()->create(['package_price' => 200.00]);
        $invoice = Invoice::factory()->create([
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
            'total_amount' => $booking->package_price,
        ]);

        $booking->update(['package_price' => 300.00]);

        $invoice->refresh();
        $this->assertSame('200.00', $invoice->total_amount);
        $this->assertSame('300.00', $booking->fresh()->package_price);
    }

    // ==========================================
    // G. Seeder Tests
    // ==========================================

    public function test_database_seeder_creates_expected_invoice_and_is_idempotent(): void
    {
        // First run
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, Invoice::count());
        $invoice = Invoice::first();

        $this->assertSame('INV-2026-0001', $invoice->invoice_number);
        $this->assertSame('Partially Paid', $invoice->payment_status);
        $this->assertSame('50.00', $invoice->amount_paid);
        $this->assertSame('150.00', $invoice->total_amount);
        $this->assertSame('100.00', $invoice->balance_due);
        $this->assertSame($invoice->booking_id, Booking::first()->id);
        $this->assertSame($invoice->client_id, User::where('email', 'test@example.com')->first()->id);

        // Second run must remain completely idempotent
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(1, Invoice::count());
    }
}
