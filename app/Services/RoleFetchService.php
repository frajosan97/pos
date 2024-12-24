<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleFetchService
{
    /**
     * Get the fetch type and its value based on the user's role.
     *
     * @return array
     */
    public function getFetchData()
    {
        try {
            // Define roles with additional data for each role
            $roles = [
                '4' => [
                    'type' => 'brand',
                    'value' => 'brand-id',
                ],
                '3' => [
                    'type' => 'company',
                    'value' => '',
                ],
                '2' => [
                    'type' => 'branch',
                    'value' => Auth::user()->branch?->id,
                ],
            ];

            // Retrieve the role data based on the authenticated user's role
            $roleData = $roles[Auth::user()->role?->role] ?? [
                'type' => 'employee',
                'value' => Auth::user()->id,
            ];

            return [
                'fetchType' => $roleData['type'],
                'fetchTypeValue' => $roleData['value']
            ];
        } catch (\Exception $exception) {
            Log::error(sprintf(
                'Error in %s: %s (File: %s, Line: %d)',
                __METHOD__,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));
            throw new \Exception('Error fetching role data');
        }
    }
}
