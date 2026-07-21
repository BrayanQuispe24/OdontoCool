<?php

namespace App\Http\Controllers;

use App\Services\PublicLandingService;
use Inertia\Inertia;
use Inertia\Response;

class PublicLandingController extends Controller
{
    public function index(PublicLandingService $publicLandingService): Response
    {
        return Inertia::render('HomePage', $publicLandingService->landingData());
    }
}
