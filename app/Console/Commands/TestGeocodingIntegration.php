<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\MapyCzGeocodingService;
use Illuminate\Console\Command;

class TestGeocodingIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-geocoding-integration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the integration of Mapy.cz Geocoding Service with Company model';

    /**
     * Execute the console command.
     */
    public function handle(MapyCzGeocodingService $geocodingService)
    {
        $this->info('Testing Geocoding Integration...');

        $testData = [
            'title' => 'Test Company ' . time(),
            'company_id' => 'TEST-' . time(),
            'street' => 'Václavské náměstí 1',
            'city' => 'Praha',
        ];

        $this->info("Creating company with address: {$testData['street']}, {$testData['city']}");

        // Simulate the logic in CompanyController::store
        $address = $testData['street'] . ', ' . $testData['city'];
        $coordinates = $geocodingService->getCoordinates($address);

        if ($coordinates) {
            $this->info("Coordinates fetched successfully: Lat: {$coordinates['latitude']}, Lon: {$coordinates['longitude']}");
            $testData['latitude'] = $coordinates['latitude'];
            $testData['longitude'] = $coordinates['longitude'];
        } else {
            $this->error('Failed to fetch coordinates from Mapy.cz API');
            return 1;
        }

        $company = Company::create($testData);

        if ($company->latitude && $company->longitude) {
            $this->info('Company created successfully with coordinates!');
            $this->table(
                ['ID', 'Title', 'Street', 'City', 'Latitude', 'Longitude'],
                [[
                    $company->id,
                    $company->title,
                    $company->street,
                    $company->city,
                    $company->latitude,
                    $company->longitude
                ]]
            );
        } else {
            $this->error('Company created but coordinates are missing!');
        }

        // Clean up
        $company->delete();
        $this->info('Test company deleted.');

        return 0;
    }
}
