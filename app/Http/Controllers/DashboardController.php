<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Define roles with additional data for each role
            $roles = [
                '4' => [
                    'title' => 'Brand Analytics',
                    'type' => 'brand',
                    'value' => 'brand-id',
                ],
                '3' => [
                    'title' => 'Company Analytics',
                    'type' => 'company',
                    'value' => '',
                ],
                '2' => [
                    'title' => 'Branch Analytics',
                    'type' => 'branch',
                    'value' => Auth::user()->branch?->id,
                ],
            ];

            // Retrieve the role data based on the authenticated user's role
            $roleData = $roles[Auth::user()->role?->role] ?? [
                'title' => 'Employee Analytics',
                'type' => 'employee',
                'value' => Auth::user()->id,
            ];

            // Extract title, type, and additional data for use
            $title = $roleData['title'];
            $fetchType = $roleData['type'];
            $fetchTypeValue = $roleData['value'];

            return view('portal.analytics.index', compact(['title', 'fetchType', 'fetchTypeValue']));
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
