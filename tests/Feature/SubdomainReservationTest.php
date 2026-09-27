<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SubdomainReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_requests_reserve_one_slug_and_the_other_company_can_choose_another(): void
    {
        Mail::fake();
        $this->submitApplication('primera@ejemplo.test', 'empresa-sol');
        $this->submitApplication('segunda@ejemplo.test', 'empresa-sol');

        $first = DB::table('company_applications')->where('email', 'primera@ejemplo.test')->first();
        $second = DB::table('company_applications')->where('email', 'segunda@ejemplo.test')->first();
        $this->assertNull($first->reserved_slug);
        $this->assertNull($second->reserved_slug);

        $this->get($this->verificationUrl($first->uuid))->assertOk()->assertSee('quedó reservado');
        $this->assertDatabaseHas('company_applications', [
            'id' => $first->id,
            'status' => 'pending_review',
            'reserved_slug' => 'empresa-sol',
        ]);
        $this->get(route('evaluation.slug', ['slug' => 'empresa-sol']))
            ->assertOk()->assertJson(['available' => false]);

        $this->get($this->verificationUrl($second->uuid))->assertOk()->assertSee('Elige otra dirección');
        $this->assertDatabaseHas('company_applications', [
            'id' => $second->id,
            'status' => 'slug_conflict',
            'reserved_slug' => null,
        ]);

        $changeUrl = URL::temporarySignedRoute('evaluation.slug.change', now()->addDays(2), ['uuid' => $second->uuid]);
        $this->post($changeUrl, ['requested_slug' => 'empresa-sol-2'])
            ->assertRedirect(route('evaluation.confirmed'));
        $this->get(route('evaluation.confirmed'))->assertOk()->assertSee('quedó reservado');
        $this->assertDatabaseHas('company_applications', [
            'id' => $second->id,
            'requested_slug' => 'empresa-sol-2',
            'reserved_slug' => 'empresa-sol-2',
            'status' => 'pending_review',
        ]);
    }

    public function test_approval_keeps_the_reservation_and_rejection_releases_it(): void
    {
        Mail::fake();
        $this->submitApplication('aprobada@ejemplo.test', 'empresa-uno');
        $this->submitApplication('rechazada@ejemplo.test', 'empresa-dos');

        $approved = DB::table('company_applications')->where('email', 'aprobada@ejemplo.test')->first();
        $rejected = DB::table('company_applications')->where('email', 'rechazada@ejemplo.test')->first();
        $this->get($this->verificationUrl($approved->uuid))->assertOk();
        $this->get($this->verificationUrl($rejected->uuid))->assertOk();

        $operator = User::factory()->create(['is_minka_operator' => true]);
        $this->actingAs($operator)->post(route('minka.application.approve', $approved->id), [
            'evaluation_ends_at' => now()->addMonth()->toDateString(),
            'enabled_modules' => ['ventas'],
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('company_applications', [
            'id' => $approved->id,
            'approved_slug' => 'empresa-uno',
            'reserved_slug' => 'empresa-uno',
            'status' => 'approved',
        ]);

        $this->post(route('minka.application.reject', $rejected->id), ['reason' => 'No continuará.'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('company_applications', [
            'id' => $rejected->id,
            'reserved_slug' => null,
            'status' => 'rejected',
        ]);
        $this->get(route('evaluation.slug', ['slug' => 'empresa-dos']))
            ->assertOk()->assertJson(['available' => true]);
        $this->get(route('evaluation.slug', ['slug' => 'empresa-uno']))
            ->assertOk()->assertJson(['available' => false]);
    }

    private function submitApplication(string $email, string $slug): void
    {
        $this->post(route('evaluation.store'), [
            'company_name' => 'Empresa de prueba',
            'first_name' => 'Ana',
            'last_name' => 'Ejemplo',
            'email' => $email,
            'phone' => '999999999',
            'requested_slug' => $slug,
            'plan_interest' => 'inicio',
            'privacy_accept' => '1',
        ])->assertSessionHasNoErrors();
    }

    private function verificationUrl(string $uuid): string
    {
        return URL::temporarySignedRoute('evaluation.verify', now()->addDay(), ['uuid' => $uuid]);
    }
}
