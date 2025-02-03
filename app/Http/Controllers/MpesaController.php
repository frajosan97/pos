<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MpesaPayment;
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

            $response = json_decode($response, true);

            if (isset($response['ResponseCode'])) {
                return response()->json(['success' => 'URLs registered successfully'], 200);
            } else {
                return response()->json(['error' => $response['errorMessage']], 500);
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

    public function validation(Request $request)
    {
        try {
            // Retrieve the raw JSON input
            $rawInput = file_get_contents('php://input');
            // Decode the JSON response
            $decodedResponse = json_decode($rawInput, true);

            // Store raw input data in public/mpesa_logs
            storeLog('validation', $decodedResponse);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function confirmation(Request $request)
    {
        try {
            // Retrieve the raw JSON input
            $rawInput = file_get_contents('php://input');
            // Decode the JSON response
            $decodedResponse = json_decode($rawInput, true);

            // Check if the response is valid
            if (!isset($decodedResponse['TransID']) || !isset($decodedResponse['TransAmount'])) {
                Log::error('Invalid confirmation data.', ['raw_input' => $rawInput]);
                return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
            }

            // Extract data from the decoded response
            $transactionId = $decodedResponse['TransID'];
            $name = $decodedResponse['LastName'];
            $amount = $decodedResponse['TransAmount'];
            $phoneNumber = '0700000000';
            $shortCode = $decodedResponse['BusinessShortCode'];
            $status = 'success';

            // Store successful payment response
            MpesaPayment::create([
                'transaction_id' => $transactionId,
                'name' => $name,
                'amount' => $amount,
                'phone' => $phoneNumber,
                'shortcode' => $shortCode,
                'status' => $status,
                'response_payload' => $decodedResponse,
            ]);

            // Store raw input data in public/mpesa_logs
            storeLog('confirmation', $decodedResponse);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            // Retrieve the raw JSON input
            $rawInput = file_get_contents('php://input');
            
            // Decode the JSON response
            $decodedResponse = json_decode($rawInput, true);

            // Store raw input data in public/mpesa_logs for debugging
            storeLog('callback', $decodedResponse);

            // Ensure the response structure is valid
            if (!isset($decodedResponse['Body']['stkCallback'])) {
                Log::error('Invalid callback data.', ['raw_input' => $rawInput]);
                return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
            }

            // Extract callback details
            $callbackData = $decodedResponse['Body']['stkCallback'];

            $resultCode = $callbackData['ResultCode'];
            $resultDesc = $callbackData['ResultDesc'];

            // Initialize transaction details
            $transactionId = null;
            $name = null;
            $amount = null;
            $phoneNumber = null;
            $shortCode = null;
            $status = null;

            // Process successful transaction
            if ($resultCode === 0 && isset($callbackData['CallbackMetadata']['Item'])) {
                $metadata = collect($callbackData['CallbackMetadata']['Item'])->pluck('Value', 'Name');

                $transactionId = $metadata['MpesaReceiptNumber'] ?? null;
                $name = $metadata['LastName'] ?? 'Unknown';
                $amount = $metadata['Amount'] ?? null;
                $phoneNumber = $metadata['PhoneNumber'] ?? null;
                $shortCode = '650';
                $status = 'success';
            }

            // Store successful payment response
            MpesaPayment::create([
                'transaction_id' => $transactionId,
                'name' => $name,
                'amount' => $amount,
                'phone' => $phoneNumber,
                'shortcode' => $shortCode,
                'status' => $status,
                'response_payload' => $decodedResponse,
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
