<?php

namespace Tests\Feature;

use App\Imports\CompaniesImport;
use App\Models\Company;
use App\Services\MapyCzGeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CompaniesImportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_import_uses_provided_coordinates_when_present(): void
    {
        $geocodingService = Mockery::mock(MapyCzGeocodingService::class);
        $geocodingService->shouldNotReceive('getCoordinates');

        $import = new CompaniesImport($geocodingService);

        $company = $import->model([
            'title' => 'ACME',
            'company_id' => 'acme-1',
            'street' => 'Main 1',
            'city' => 'Prague',
            'latitude' => '50.0870',
            'longitude' => '14.4210',
        ]);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals(50.0870, $company->latitude, '', 0.000001);
        $this->assertEquals(14.4210, $company->longitude, '', 0.000001);
    }

    public function test_import_geocodes_when_coordinates_are_missing(): void
    {
        $geocodingService = Mockery::mock(MapyCzGeocodingService::class);
        $geocodingService
            ->shouldReceive('getCoordinates')
            ->once()
            ->with('Main 1, Prague')
            ->andReturn(['latitude' => 1.23, 'longitude' => 4.56]);

        $import = new CompaniesImport($geocodingService);

        $company = $import->model([
            'title' => 'ACME',
            'company_id' => 'acme-1',
            'street' => 'Main 1',
            'city' => 'Prague',
        ]);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals(1.23, $company->latitude, '', 0.000001);
        $this->assertEquals(4.56, $company->longitude, '', 0.000001);
    }

    public function test_import_geocodes_when_coordinates_are_invalid(): void
    {
        $geocodingService = Mockery::mock(MapyCzGeocodingService::class);
        $geocodingService
            ->shouldReceive('getCoordinates')
            ->once()
            ->with('Main 1, Prague')
            ->andReturn(['latitude' => 1.23, 'longitude' => 4.56]);

        $import = new CompaniesImport($geocodingService);

        $company = $import->model([
            'title' => 'ACME',
            'company_id' => 'acme-1',
            'street' => 'Main 1',
            'city' => 'Prague',
            'latitude' => '200',
            'longitude' => '14.4210',
        ]);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals(1.23, $company->latitude, '', 0.000001);
        $this->assertEquals(4.56, $company->longitude, '', 0.000001);
    }

    public function test_import_upserts_by_company_id(): void
    {
        Company::create([
            'title' => 'Old Name',
            'company_id' => 'acme-1',
            'street' => 'Old Street',
            'city' => 'Old City',
            'latitude' => 10.1,
            'longitude' => 20.2,
            'color' => '#111111',
        ]);

        $geocodingService = Mockery::mock(MapyCzGeocodingService::class);
        $geocodingService->shouldNotReceive('getCoordinates');

        $import = new CompaniesImport($geocodingService);

        $company = $import->model([
            'title' => 'New Name',
            'company_id' => 'acme-1',
            'street' => 'New Street',
            'city' => 'New City',
            'latitude' => '50.0870',
            'longitude' => '14.4210',
            'color' => '#222222',
        ]);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals(1, Company::query()->count());
        $this->assertSame('New Name', Company::query()->first()->title);
        $this->assertSame('New Street', Company::query()->first()->street);
        $this->assertSame('New City', Company::query()->first()->city);
        $this->assertSame('#222222', Company::query()->first()->color);
    }
}
