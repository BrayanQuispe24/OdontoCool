<?php

namespace App\Http\Controllers;

use App\Services\PublicLandingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BusquedaNegocioController extends Controller
{
    public function index(Request $request, PublicLandingService $publicLandingService): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        return response()->json($publicLandingService->search($query));
    }
}
