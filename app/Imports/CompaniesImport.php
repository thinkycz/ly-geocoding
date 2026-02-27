<?php

namespace App\Imports;

use App\Models\Company;
use App\Services\MapyCzGeocodingService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class CompaniesImport implements ToModel, WithHeadingRow
{
    protected $geocodingService;


    protected $successCount = 0;

    protected $skippedCount = 0;

    protected $errors = [];

    public function __construct(MapyCzGeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Validate required fields
        if (empty($row['title']) || empty($row['company_id']) || empty($row['street']) || empty($row['city'])) {
            $this->skippedCount++;

            return null;
        }

        // Prepare company data
        $data = [
            'title' => $row['title'],
            'company_id' => $row['company_id'],
            'street' => $row['street'],
            'city' => $row['city'],
            'color' => $row['color'] ?? '#3FB1CE',
        ];

        $latitude = $this->parseLatitude($row['latitude'] ?? null);
        $longitude = $this->parseLongitude($row['longitude'] ?? null);

        if ($latitude !== null && $longitude !== null) {
            $data['latitude'] = $latitude;
            $data['longitude'] = $longitude;
        } else {
            $address = $data['street'].', '.$data['city'];
            $coordinates = $this->geocodingService->getCoordinates($address);

            if ($coordinates) {
                $data['latitude'] = $coordinates['latitude'];
                $data['longitude'] = $coordinates['longitude'];
            }
        }

        $this->successCount++;

        return Company::updateOrCreate(
            ['company_id' => $row['company_id']],
            $data
        );
    }

    /**
     * Get the results of the import.
     */
    public function getResults(): array
    {
        return [
            'success' => $this->successCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errors,
        ];
    }

    private function parseLatitude(mixed $value): ?float
    {
        return $this->parseCoordinate($value, -90, 90);
    }

    private function parseLongitude(mixed $value): ?float
    {
        return $this->parseCoordinate($value, -180, 180);
    }

    private function parseCoordinate(mixed $value, float $min, float $max): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }

            if (str_contains($value, ',') && ! str_contains($value, '.')) {
                $value = str_replace(',', '.', $value);
            }
        }

        if (! is_numeric($value)) {
            return null;
        }

        $float = (float) $value;

        if ($float < $min || $float > $max) {
            return null;
        }

        return $float;
    }
}
