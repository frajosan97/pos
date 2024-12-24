<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleFetchService;

class DashboardController extends Controller
{
    protected $roleFetchService;

    // Inject the RoleFetchService via the constructor
    public function __construct(RoleFetchService $roleFetchService)
    {
        $this->roleFetchService = $roleFetchService;
    }

    public function index()
    {
        try {
            // Use the service to fetch the role data
            $roleData = $this->roleFetchService->getFetchData();

            // Extract title, type, and additional data for use
            $fetchType = $roleData['fetchType'];
            $fetchTypeValue = $roleData['fetchTypeValue'];

            return view('portal.analytics.index', compact(['fetchType', 'fetchTypeValue']));
        } catch (\Exception $exception) {
            Log::error(sprintf(
                'Error in %s: %s (File: %s, Line: %d)',
                __METHOD__,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
