<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationService;

class LocationController extends Controller
{
    public function __construct(
        protected LocationService $locationService
    ) {}

    public function countries()
    {
        return response()->json($this->locationService->getCountries());
    }

    public function cities(string $country)
    {
        $cities = $this->locationService->getCities(urldecode($country));

        return response()->json($cities);
    }
}
