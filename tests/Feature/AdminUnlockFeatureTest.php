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

    private function unlockAccess(): void
    {
        $this->post(route('admin.unlock.store'), [
            'password' => 'secret',
        ])->assertRedirect(route('companies.index'));
    }

    public function test_locked_can_view_companies_index_and_show(): void
    {
        $this->configureAdminPassword();
        $company = $this->makeCompany();

        $this->get(route('companies.index'))
            ->assertOk()
            ->assertSee('Company data is locked')
            ->assertSee('Enter Password')
            ->assertSee('const mapMarkers =')
            ->assertDontSee('ACME')
            ->assertDontSee('acme-1')
            ->assertDontSee('Main 1')
            ->assertDontSee('View Details');

        $this->get(route('companies.show', $company))->assertRedirect(route('admin.unlock'));
        $this->get(route('admin.unlock'))->assertOk();
    }

    public function test_locked_is_redirected_to_unlock_for_protected_routes(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $this->configureAdminPassword();
        $company = $this->makeCompany();

        $this->get(route('companies.create'))->assertRedirect(route('admin.unlock'));
        $this->get(route('companies.edit', $company))->assertRedirect(route('admin.unlock'));
        $this->get(route('companies.show', $company))->assertRedirect(route('admin.unlock'));
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

        $this->get(route('companies.index'))->assertOk()->assertSee('ACME');
        $this->get(route('companies.show', $company))->assertOk()->assertSee('Company Details');
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

    public function test_unlock_redirects_back_to_requested_company_detail_page(): void
    {
        $this->configureAdminPassword();
        $company = $this->makeCompany();

        $this->get(route('companies.show', $company))->assertRedirect(route('admin.unlock'));

        $this->post(route('admin.unlock.store'), [
            'password' => 'secret',
        ])->assertRedirect(route('companies.show', $company));
    }

    public function test_unlocked_index_can_sort_by_requested_columns(): void
    {
        $this->configureAdminPassword();

        Company::create([
            'title' => 'Beta',
            'company_id' => 'beta-2',
            'street' => 'Second 2',
            'city' => 'Brno',
            'latitude' => 49.195,
            'longitude' => 16.606,
            'color' => '#222222',
        ]);

        Company::create([
            'title' => 'Alpha',
            'company_id' => 'alpha-1',
            'street' => 'First 1',
            'city' => 'Prague',
            'latitude' => 50.087,
            'longitude' => 14.421,
            'color' => '#111111',
        ]);

        Company::create([
            'title' => 'Gamma',
            'company_id' => 'gamma-3',
            'street' => 'Third 3',
            'city' => 'Ostrava',
            'latitude' => 49.820,
            'longitude' => 18.262,
            'color' => '#333333',
        ]);

        $this->unlockAccess();

        $this->get(route('companies.index', ['sort' => 'title', 'direction' => 'asc']))
            ->assertOk()
            ->assertSeeInOrder(['Alpha', 'Beta', 'Gamma']);

        $this->get(route('companies.index', ['sort' => 'company_id', 'direction' => 'desc']))
            ->assertOk()
            ->assertSeeInOrder(['gamma-3', 'beta-2', 'alpha-1']);

        $this->get(route('companies.index', ['sort' => 'color', 'direction' => 'asc']))
            ->assertOk()
            ->assertSeeInOrder(['#111111', '#222222', '#333333']);
    }
}
