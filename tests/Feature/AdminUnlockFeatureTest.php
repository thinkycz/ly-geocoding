<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUnlockFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function configureAdminPassword(string $password = 'secret'): void
    {
        config(['admin.password_hash' => Hash::make($password)]);
        config(['admin.unlock_timeout_seconds' => 3600]);
        config(['admin.session_key' => 'admin_unlocked_at']);
    }

    private function makeCompany(): Company
    {
        return Company::create([
            'title' => 'ACME',
            'company_id' => 'acme-1',
            'street' => 'Main 1',
            'city' => 'Prague',
            'latitude' => 50.087,
            'longitude' => 14.421,
            'color' => null,
        ]);
    }

    public function test_locked_can_view_companies_index_and_show(): void
    {
        $this->configureAdminPassword();
        $company = $this->makeCompany();

        $this->get(route('companies.index'))->assertOk();
        $this->get(route('companies.show', $company))->assertOk();
        $this->get(route('admin.unlock'))->assertOk();
    }

    public function test_locked_is_redirected_to_unlock_for_protected_routes(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $this->configureAdminPassword();
        $company = $this->makeCompany();

        $this->get(route('companies.create'))->assertRedirect(route('admin.unlock'));
        $this->get(route('companies.edit', $company))->assertRedirect(route('admin.unlock'));
        $this->get(route('companies.import'))->assertRedirect(route('admin.unlock'));
        $this->get(route('companies.export'))->assertRedirect(route('admin.unlock'));

        $this->put(route('companies.update', $company), [
            'title' => 'ACME',
            'company_id' => 'acme-1',
            'street' => 'Main 1',
            'city' => 'Prague',
            'latitude' => 50.087,
            'longitude' => 14.421,
            'color' => null,
        ])->assertRedirect(route('admin.unlock'));

        $this->delete(route('companies.destroy', $company))->assertRedirect(route('admin.unlock'));

        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }

    public function test_unlock_allows_admin_actions_until_locked(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $this->configureAdminPassword();
        $company = $this->makeCompany();

        $this->get(route('companies.export'))->assertRedirect(route('admin.unlock'));

        $this->post(route('admin.unlock.store'), [
            'password' => 'secret',
        ])->assertRedirect(route('companies.export'));

        $this->get(route('companies.create'))->assertOk();

        $this->post(route('companies.store'), [
            'title' => 'BETA',
            'company_id' => 'beta-1',
            'street' => 'Second 2',
            'city' => 'Brno',
            'latitude' => 49.195,
            'longitude' => 16.606,
            'color' => '#3FB1CE',
        ])->assertRedirect(route('companies.index'));

        $this->put(route('companies.update', $company), [
            'title' => 'ACME Updated',
            'company_id' => 'acme-1',
            'street' => 'Main 1',
            'city' => 'Prague',
            'latitude' => 50.087,
            'longitude' => 14.421,
            'color' => '#3FB1CE',
        ])->assertRedirect(route('companies.index'));

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'title' => 'ACME Updated']);

        $this->delete(route('companies.destroy', $company))->assertRedirect(route('companies.index'));
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);

        $this->post(route('admin.lock'))->assertRedirect(route('companies.index'));
        $this->get(route('companies.create'))->assertRedirect(route('admin.unlock'));
    }
}

