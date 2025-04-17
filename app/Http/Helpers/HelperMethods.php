<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

/**
 * Check if the current user agent is a desktop device.
 *
 * @return bool True if desktop, false otherwise.
 */
if (!function_exists('isDesktop')) {
    function isDesktop()
    {
        // Get the User-Agent header from the request
        $userAgent = Request::header('User-Agent');

        // Check if 'Mobi' is not present in the User-Agent string
        return $userAgent ? strpos($userAgent, 'Mobi') === false : true;
    }
}

/**
 * Convert a given string into a URL-friendly slug.
 *
 * @param string $string The string to be converted.
 * @return string The slugified version of the string.
 */
if (!function_exists('smart_key')) {
    function smart_key($string = "")
    {
        if (empty($string)) {
            return Str::uuid();
        } else {
            return preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($string)));
        }
    }
}

/**
 * Convert a given string into a URL-friendly slug.
 *
 * @param string $string The string to be converted.
 * @return string The slugified version of the string.
 */
if (!function_exists('my_slug')) {
    function my_slug($string)
    {
        $slug = Str::slug($string);
        $slug = str_replace('-', '_', $slug);
        return Str::lower($slug);
    }
}

/**
 * Get the URL of an image from storage, or a default image if not found.
 *
 * @param string $imagePath
 * @param string $default
 * @return string
 */
if (!function_exists('getImage')) {
    function getImage(string $imagePath, string $default = 'default.png'): string
    {
        // Define the full path to the image in the public folder
        $imageFullPath = public_path($imagePath);

        // Check if the file exists in the public directory
        if (file_exists($imageFullPath)) {
            return asset($imagePath);  // Return the image URL
        }

        // Return default image URL if the file does not exist
        return asset("assets/images/defaults/$default");
    }
}

/**
 * Check if a view exists in the application.
 *
 * @param string $view The name of the view to check.
 * @return bool True if the view exists, false otherwise.
 */
if (!function_exists('viewExists')) {
    function viewExists($view)
    {
        // Use the View facade to check for the existence of the view
        return View::exists($view);
    }
}

/**
 * Get a list of user roles with their corresponding descriptions.
 *
 * @return array An associative array of user roles and descriptions.
 */
if (!function_exists('userRoles')) {
    function userRoles()
    {
        return [
            '1' => 'Marketing officer',
            '2' => 'Branch Manager',
            '3' => 'Administrator'
        ];
    }
}

/**
 * Get the description of a user role based on the role ID.
 *
 * @param string $userRole The user role ID.
 * @return string The description of the user role.
 */
if (!function_exists('userRoleBind')) {
    function userRoleBind($userRole = "")
    {
        if (!empty($userRole)) {
            foreach (userRoles() as $key => $value) {
                if ($userRole === $key) {
                    return $value;
                }
            }
        } else {
            return 'undefined';
        }
    }
}

/**
 * Obfuscate email address for display.
 *
 * @param string $email
 * @return string
 */
if (!function_exists('obfuscateEmail')) {
    function obfuscateEmail(string $email): string
    {
        return preg_replace('/(?<=.).(?=.*@)/', '*', $email);
    }
}

/**
 * Obfuscate phone number for display.
 *
 * @param string $phone
 * @return string
 */
if (!function_exists('obfuscatePhone')) {
    function obfuscatePhone(string $phone): string
    {
        return substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
    }
}

/**
 * Format a phone number to start with '254' if it begins with '0'.
 *
 * @param string $phoneNumber
 * @return string
 * @throws InvalidArgumentException
 */
if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        if (substr($phoneNumber, 0, 1) == '0') {
            $phoneNumber = '254' . substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }
}

/**
 * Format an amount to two decimal places.
 *
 * @param mixed $amount
 * @return string
 * @throws InvalidArgumentException
 */
if (!function_exists('formatAmount')) {
    function formatAmount($amount): string
    {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException("Invalid amount provided.");
        }
        return number_format((float)$amount, 2, '.', '');
    }
}

/**
 * Store logs in a JSON file, creating or updating as necessary.
 *
 * @param string $type The type of log.
 * @param mixed $data The data to store.
 */
if (!function_exists('storeLog')) {
    function storeLog(string $type, $data): void
    {
        $filePath = "$type.json";
        $existingData = Storage::disk('public')->exists($filePath)
            ? json_decode(Storage::disk('public')->get($filePath), true)
            : [];
        $existingData[] = $data;

        Storage::disk('public')->put($filePath, json_encode($existingData, JSON_PRETTY_PRINT));
    }
}

if (!function_exists('genPassword')) {
    function genPassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        return substr(str_shuffle($chars), 0, $length);
    }
}

/**
 * Get a list of default customer and guarantor photo types and their default images.
 *
 * @return array An associative array of photo types and their default images.
 */
if (!function_exists('kyc_docs')) {
    function kyc_docs()
    {
        return [
            'passport_photo' => 'Passport Photo',
            'id_photo' => 'ID Copy',
            'kra' => 'KRA PIN Certificate',
            'police_clearance' => 'PCC (Police Clearance Certificate)',
        ];
    }
}

/**
 * Format a given date with an ordinal suffix (e.g., 20TH March, 2025).
 *
 * @param \Carbon\Carbon|null $date The date to format. If null, the current date is used.
 * @param string $get Specifies whether to return the full date, or just the day, month, or year.
 * @return string
 */
if (!function_exists('formatDateWithOrdinal')) {
    function formatDateWithOrdinal($date = null, string $get = 'full'): string
    {
        // Use the current date if no date is provided
        if (empty($date)) {
            $date = Carbon::now();
        } else {
            $date = Carbon::parse($date);
        }

        // Get the day of the month
        $day = $date->day;

        // Determine the ordinal suffix
        $ordinal = 'th';
        if ($day % 10 == 1 && $day != 11) {
            $ordinal = 'st';
        } elseif ($day % 10 == 2 && $day != 12) {
            $ordinal = 'nd';
        } elseif ($day % 10 == 3 && $day != 13) {
            $ordinal = 'rd';
        }

        // Format the date based on the $get parameter
        switch ($get) {
            case 'day':
                return $day . '<sup>' . $ordinal . '</sup>';
            case 'month':
                return $date->format('F');
            case 'year':
                return $date->format('Y');
            case 'full':
            default:
                return $day . '<sup>' . $ordinal . '</sup> ' . $date->format('F, Y');
        }
    }
}

if (!function_exists('getProtectedRouteNames')) {
    function getProtectedRouteNames()
    {
        // Get all routes
        $routes = Route::getRoutes();

        // Filter routes by the 'auth' and 'permission' middleware
        $protectedRoutes = collect($routes)->filter(function ($route) {
            $middleware = $route->middleware();
            return in_array('auth', $middleware) && in_array('permission', $middleware);
        });

        // Extract route names
        $routeNames = $protectedRoutes->map(function ($route) {
            return $route->getName();
        })->filter();

        return $routeNames->all();
    }
}

if (!function_exists('permissionSlug')) {
    function permissionSlug($permissionSlug) {

        if($permissionSlug==='/') {
            $permissionSlug = 'dashboard';
        }
        
        return str_replace( '.', '_', $permissionSlug );
    }
}

if (!function_exists('invoiceNumber')) {
    function invoiceNumber($invoiceId)
    {
        return str_pad($invoiceId, 6, '0', STR_PAD_LEFT);
    }
}

