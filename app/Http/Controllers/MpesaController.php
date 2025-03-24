<?php

namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MpesaService;
use App\Services\SmsService;
use Illuminate\Support\Facades\DB;

class MpesaController extends Controller
{
    protected $mpesaService;
    protected $smsService;

    /**
     * MpesaController constructor.
     *
     * @param MpesaService $mpesaService
     * @param SmsService $smsService
     */
    public function __construct(MpesaService $mpesaService, SmsService $smsService)
    {
        $this->mpesaService = $mpesaService;
        $this->smsService = $smsService;
    }

    /**
     * Register URLs for C2B transactions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerUrl()
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

    public function stkPush(Request $request)
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
                return response()->json(['success' => 'Payment initiated successfully, kindly check your phone and enter MPESA pin to complete the transaction']);
            }
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Handle validation request from M-Pesa.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validation(Request $request)
    {
        try {
            // Retrieve the raw JSON input
            $rawInput = file_get_contents('php://input');
            // Decode the JSON response
            $decodedResponse = json_decode($rawInput, true);

            // Store raw input data in public/mpesa_logs
            storeLog('mpesa_logs/validation', $decodedResponse);

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error'], 500);
        }
    }

    /**
     * Handle callback request from M-Pesa.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request)
    {
        try {
            // Retrieve the raw JSON input
            $rawInput = file_get_contents('php://input');

            // Decode the JSON response
            $decodedResponse = json_decode($rawInput, true);

            // Store raw input data in public/mpesa_logs for debugging
            storeLog('mpesa_logs/callback', $decodedResponse);

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
            $firstName = null;
            $amount = null;
            $phoneNumber = null;

            // Process successful transaction
            if ($resultCode === 0 && isset($callbackData['CallbackMetadata']['Item'])) {
                $metadata = collect($callbackData['CallbackMetadata']['Item'])->pluck('Value', 'Name');

                $transactionId = $metadata['MpesaReceiptNumber'] ?? null;
                $firstName = $metadata['LastName'] ?? 'Unknown';
                $amount = $metadata['Amount'] ?? null;
                $phoneNumber = $metadata['PhoneNumber'] ?? null;
            }

            // Store The Response to Database
            $this->storeTransaction([
                'transaction_id' => $transactionId,
                'name' => $firstName,
                'amount' => $amount,
                'phone' => $phoneNumber,
                'status' => 'success',
                'transaction_type' => 'payment'
            ]);

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Handle confirmation request from M-Pesa.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmation(Request $request)
    {
        try {
            // Retrieve the raw JSON input
            $rawInput = file_get_contents('php://input');
            // Decode the JSON response
            $decodedResponse = json_decode($rawInput, true);

            // Store raw input data in public/mpesa_logs
            storeLog('mpesa_logs/confirmation', $decodedResponse);

            // Check if the response is valid
            if (!isset($decodedResponse['TransID']) || !isset($decodedResponse['TransAmount']) || !isset($decodedResponse['MSISDN']) || !isset($decodedResponse['FirstName'])) {
                Log::error('Invalid confirmation data.', ['raw_input' => $rawInput]);
                return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
            }

            // Extract necessary fields
            $transactionId = $decodedResponse['TransID'];
            $amount = $decodedResponse['TransAmount'];
            $phoneNumber = $decodedResponse['MSISDN'];
            $firstName = $decodedResponse['FirstName'];

            // Store The Response to Database
            $this->storeTransaction([
                'transaction_id' => $transactionId,
                'name' => $firstName,
                'amount' => $amount,
                'phone' => $phoneNumber,
                'status' => 'success',
                'transaction_type' => 'payment'
            ]);

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        } catch (\Exception $exception) {
            Log::error('Error in ' . __METHOD__ . ' - File: ' . $exception->getFile() . ', Line: ' . $exception->getLine() . ', Message: ' . $exception->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error'], 500);
        }
    }

    /**
     * Store transaction details in the database.
     *
     * @param array $data
     * @return void
     */
    protected function storeTransaction(array $data)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Store successful payment response
            $mpesaPayment = MpesaPayment::create([
                'transaction_id' => $data['transaction_id'],
                'name' => $data['name'],
                'amount' => $data['amount'],
                'phone' => $data['phone'],
                'shortcode' => '6570575',
                'status' => $data['status'],
                'response_payload' => json_encode($data),
            ]);

            if ($mpesaPayment) {
                // Send notification
            }

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            Log::error('Error storing transaction: ' . $e->getMessage());
        }
    }
}
