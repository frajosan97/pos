<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Role;

class RoleFetchService
{
    /**
     * Get the fetch type and its value based on the user's role.
     *
     * @return array
     * @throws \Exception
     */
    public function getFetchData(): array
    {
        try {
            // Retrieve the authenticated user
            $user = Auth::user();

            if (!$user || !$user->roles->isNotEmpty()) {
                // If the user or their roles are not found, use default values
                return $this->getDefaultFetchData();
            }

            // Fetch the first role of the user
            $userRole = $user->roles->first();

            if (!$userRole) {
                // If no roles are associated with the user, use default values
                return $this->getDefaultFetchData();
            }

            // Retrieve role details from the 'roles' table
            $role = Role::find($userRole->id);

            if (!$role) {
                // If the role does not exist in the database, use default values
                return $this->getDefaultFetchData();
            }

            // Prepare the fetchType and fetchTypeValue based on the role data
            return [
                'fetchType' => $role->type ?? 'employee',  // Default to 'employee' if type is null
                'fetchTypeValue' => $role->value ?? $user->id,  // Default to the user ID if value is null
            ];
        } catch (\Exception $exception) {
            // Log the exception details
            Log::error(sprintf(
                'Error in %s: %s (File: %s, Line: %d)',
                __METHOD__,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));

            // Re-throw a generic exception to avoid leaking sensitive details
            throw new \Exception('Error fetching role data');
        }
    }

    /**
     * Get default fetch data.
     *
     * @return array
     */
    private function getDefaultFetchData(): array
    {
        $userId = Auth::id(); // Get the authenticated user's ID

        return [
            'fetchType' => 'employee', // Default type
            'fetchTypeValue' => $userId, // Default to the authenticated user's ID
        ];
    }
}
