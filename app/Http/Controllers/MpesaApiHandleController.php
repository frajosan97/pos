<?php

namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaApiHandleController extends Controller
{
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
            $name = $decodedResponse['lastName'];
            $amount = $decodedResponse['TransAmount'];
            $phoneNumber = '0700000000';
            $shortCode = $decodedResponse['shortCode'];
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

            // Store raw input data in public/mpesa_logs
            storeLog('callback', $decodedResponse);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function result(Request $request)
    {
        try {
            // Retrieve the raw JSON input
            $rawInput = file_get_contents('php://input');
            // Decode the JSON response
            $decodedResponse = json_decode($rawInput, true);

            // Store raw input data in public/mpesa_logs
            storeLog('result', $decodedResponse);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function timeout(Request $request)
    {
        try {
            // Retrieve the raw JSON input
            $rawInput = file_get_contents('php://input');
            // Decode the JSON response
            $decodedResponse = json_decode($rawInput, true);

            // Store raw input data in public/mpesa_logs
            storeLog('timeout', $decodedResponse);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
