<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    public function registerUrl(Request $request)
    {
        try {
            $response = $this->mpesaService->register();

            if ($response['status'] === 'success') {
                return response()->json(['success' => 'URLs registered successfully']);
            }
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function stkpush(Request $request)
    {
        try {
            $request->validate([
                'phoneNumber' => 'required|string',
                'amount' => 'required|numeric|min:1',
            ]);

            $response = $this->mpesaService->stkPush(
                $request->input('phoneNumber'),
                $request->input('amount'),
                $request->input('phoneNumber'),
                'Payment initiated successfully'
            );

            $response = json_decode($response, true);

            if ($response['ResponseCode'] === '0') {
                return response()->json(['success' => 'Payment initiated successfully, kindly advice the customer to check their phone and enter MPESA pin to complete the transaction']);
            }
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
