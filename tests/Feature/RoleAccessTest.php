<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_lpu_and_kepala_stasiun_can_view_pks_index_only(): void
    {
        $this->actingAs($this->userWithRole('lpu'))->get('/pks')->assertOk();
        $this->actingAs($this->userWithRole('kepala_stasiun'))->get('/pks')->assertOk();

        $this->actingAs($this->userWithRole('penyetor'))->get('/pks')->assertForbidden();
        $this->actingAs($this->userWithRole('admin'))->get('/pks')->assertForbidden();
    }

    public function test_dashboard_renders_for_each_role(): void
    {
        foreach (['admin', 'lpu', 'penyetor', 'kepala_stasiun'] as $role) {
            $this->actingAs($this->userWithRole($role))
                ->get('/dashboard')
                ->assertOk();
        }
    }

    public function test_only_lpu_can_print_pks(): void
    {
        $this->actingAs($this->userWithRole('lpu'))->get('/pks/999/cetak')->assertNotFound();

        $this->actingAs($this->userWithRole('kepala_stasiun'))->get('/pks/999/cetak')->assertForbidden();
        $this->actingAs($this->userWithRole('penyetor'))->get('/pks/999/cetak')->assertForbidden();
    }

    public function test_penyetor_and_kepala_stasiun_can_view_invoice_and_payment_lists(): void
    {
        $this->actingAs($this->userWithRole('penyetor'))->get('/invoice')->assertOk();
        $this->actingAs($this->userWithRole('kepala_stasiun'))->get('/invoice')->assertOk();
        $this->actingAs($this->userWithRole('lpu'))->get('/invoice')->assertForbidden();

        $this->actingAs($this->userWithRole('penyetor'))->get('/payment')->assertOk();
        $this->actingAs($this->userWithRole('kepala_stasiun'))->get('/payment')->assertOk();
        $this->actingAs($this->userWithRole('lpu'))->get('/payment')->assertForbidden();
    }

    public function test_only_penyetor_can_print_invoice_and_kwitansi(): void
    {
        $this->actingAs($this->userWithRole('penyetor'))->get('/invoice/999/cetak')->assertNotFound();
        $this->actingAs($this->userWithRole('kepala_stasiun'))->get('/invoice/999/cetak')->assertForbidden();
        $this->actingAs($this->userWithRole('lpu'))->get('/invoice/999/cetak')->assertForbidden();

        $this->actingAs($this->userWithRole('penyetor'))->get('/payment/999/kwitansi')->assertNotFound();
        $this->actingAs($this->userWithRole('kepala_stasiun'))->get('/payment/999/kwitansi')->assertForbidden();
        $this->actingAs($this->userWithRole('lpu'))->get('/payment/999/kwitansi')->assertForbidden();
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }
}
