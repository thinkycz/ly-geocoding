<?php

namespace App\Imports;

use App\Models\Company;
use App\Services\MapyCzGeocodingService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;

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
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Validate required fields
        if (empty($row['title']) || empty($row['company_id']) || empty($row['street']) || empty($row['city'])) {
            $this->skippedCount++;
            return null;
        }

        // Check if company_id already exists
        if (Company::where('company_id', $row['company_id'])->exists()) {
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

        // Auto-compute coordinates using geocoding service
        $address = $data['street'] . ', ' . $data['city'];
        $coordinates = $this->geocodingService->getCoordinates($address);

        if ($coordinates) {
            $data['latitude'] = $coordinates['latitude'];
            $data['longitude'] = $coordinates['longitude'];
        }

        $this->successCount++;

        return new Company($data);
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
}

