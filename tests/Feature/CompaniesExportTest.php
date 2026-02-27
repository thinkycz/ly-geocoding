<?php

namespace Tests\Feature;

use App\Exports\CompaniesExport;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompaniesExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_headings_match_import_format(): void
    {
        $export = new CompaniesExport();

        $this->assertSame([
            'title',
            'company_id',
            'street',
            'city',
            'latitude',
            'longitude',
            'color',
        ], $export->headings());
    }

    public function test_export_mapping_matches_import_format(): void
    {
        $company = Company::create([
            'title' => 'ACME',
            'company_id' => 'acme-1',
            'street' => 'Main 1',
            'city' => 'Prague',
            'latitude' => 50.087,
            'longitude' => 14.421,
            'color' => null,
        ]);

        $export = new CompaniesExport();

        $this->assertSame([
            'ACME',
            'acme-1',
            'Main 1',
            'Prague',
            50.087,
            14.421,
            '#3FB1CE',
        ], $export->map($company));
    }
}

