<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapyCzGeocodingService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.mapy.cz/v1/geocode';

    public function __construct()
    {
        $this->apiKey = config('services.mapy_cz.api_key', '');
    }

    /**
     * Get coordinates for a given address.
     *
     * @param string $address
     * @return array|null Returns ['latitude' => float, 'longitude' => float] or null if not found.
     */
    public function getCoordinates(string $address): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Mapy.cz API Key is missing.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'X-Mapy-Api-Key' => $this->apiKey,
            ])->get($this->baseUrl, [
                'query' => $address,
                'lang' => 'en',
                'limit' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Log the response for debugging if needed
                // Log::info('Mapy.cz Geocoding Response:', $data);

                if (!empty($data['items']) && isset($data['items'][0]['position'])) {
                    $position = $data['items'][0]['position'];
                    return [
                        'latitude' => $position['lat'],
                        'longitude' => $position['lon'],
                    ];
                }
            } else {
                Log::error('Mapy.cz Geocoding API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Mapy.cz Geocoding Exception: ' . $e->getMessage());
        }

        return null;
    }
}
