<?php

namespace App\Http\Controllers;

use App\Models\Constituency;
use App\Models\Ward;
use App\Models\Location;
use App\Models\MpesaPayment;
use App\Models\PaymentMethod;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function constituency(Request $request, string $county_id)
    {
        try {
            $data = Constituency::where('county_id', $county_id)->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function ward(Request $request, string $constituency_id)
    {
        try {
            $data = Ward::where('constituency_id', $constituency_id)->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function location(Request $request, string $ward_id)
    {
        try {
            $data = Location::where('ward_id', $ward_id)->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function product(Request $request, string $barcode)
    {
        try {
            $data = Products::where('barcode', $barcode)->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function paymentMethods(Request $request)
    {
        try {
            $data = PaymentMethod::all();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }

    public function mpesaPaymants(Request $request)
    {
        try {
            $data = MpesaPayment::whereNot('use_status', 'used')->get();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request. Please try again.'], 500);
        }
    }
}
