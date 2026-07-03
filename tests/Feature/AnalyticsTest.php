<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guest cannot access analytics dashboard.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.analytics.index'));
        $response->assertRedirect('/login');
    }

    /**
     * Customer cannot access analytics dashboard.
     */
    public function test_customer_cannot_access_analytics(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('admin.analytics.index'));
        $response->assertRedirect('/dashboard');
    }

    /**
     * Staff cannot access analytics dashboard.
     */
    public function test_staff_cannot_access_analytics(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('admin.analytics.index'));
        $response->assertRedirect('/dashboard');
    }

    /**
     * Delivery agent cannot access analytics dashboard.
     */
    public function test_delivery_cannot_access_analytics(): void
    {
        $delivery = User::factory()->create(['role' => 'delivery']);

        $response = $this->actingAs($delivery)->get(route('admin.analytics.index'));
        $response->assertRedirect('/dashboard');
    }

    /**
     * Admin can access analytics dashboard.
     */
    public function test_admin_can_access_analytics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.analytics.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.analytics.index');
        $response->assertSee('Analytics Dashboard');
    }

    /**
     * Admin can access analytics printable preview.
     */
    public function test_admin_can_access_printable_preview(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.analytics.printable'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.analytics.print');
        $response->assertSee('LOMS Analytics Report Preview');
    }

    /**
     * Admin can export analytics CSV.
     */
    public function test_admin_can_export_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.analytics.export.csv'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * Admin can export analytics PDF.
     */
    public function test_admin_can_export_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.analytics.export.pdf'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
