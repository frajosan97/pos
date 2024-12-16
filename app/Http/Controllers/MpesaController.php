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
                return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Register URL request successful!']);
            } else {
                return redirect()->back()->with('message', ['type' => 'error', 'text' => 'Register URL request failed!']);
            }
        } catch (\Exception $e) {
            Log::error('Error registering URL: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('message', ['type' => 'error', 'text' => 'An error occurred while registering the URL.']);
        }
    }

    public function simulate(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'msisdn' => 'required|string',
                'billRefNumber' => 'required|string',
            ]);

            $response = $this->mpesaService->simulateC2B(
                $request->input('msisdn'),
                $request->input('amount'),
                $request->input('billRefNumber')
            );

            if ($response['status'] === 'success') {
                return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Simulation successful!']);
            } else {
                return redirect()->back()->with('message', ['type' => 'error', 'text' => 'Simulation failed!']);
            }
        } catch (\Exception $e) {
            Log::error('Error during C2B simulation: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('message', ['type' => 'error', 'text' => 'An error occurred during the simulation.']);
        }
    }

    public function simulateB2c(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'msisdn' => 'required|string',
                'billRefNumber' => 'required|string',
            ]);

            $response = $this->mpesaService->b2c(
                formatAmount($request->input('amount')),
                $request->input('msisdn'),
                'BusinessPayment',
                'LoanDisbursement',
                'Loan disbursed successfully',
                'CUSTOMER-' . time()
            );

            if ($response['status'] === 'success') {
                return redirect()->back()->with('message', ['type' => 'success', 'text' => 'B2C simulation successful!']);
            } else {
                return redirect()->back()->with('message', ['type' => 'error', 'text' => 'B2C simulation failed.']);
            }
        } catch (\Exception $e) {
            Log::error('Error during B2C simulation: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('message', ['type' => 'error', 'text' => 'An error occurred during the B2C simulation.']);
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
                return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Payment request successful!']);
            } else {
                return redirect()->back()->with('message', ['type' => 'error', 'text' => 'Payment request failed. ' . $response['ResponseDescription']]);
            }
        } catch (\Exception $e) {
            Log::error('Error during STK Push: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('message', ['type' => 'error', 'text' => 'An error occurred during the STK Push request.']);
        }
    }

    public function reverse(Request $request)
    {
        try {
            $request->validate([
                'transactionId' => 'required|string',
                'amount' => 'required|numeric|min:1',
            ]);

            $response = $this->mpesaService->reverse(
                $request->input('transactionId'),
                $request->input('amount')
            );

            if ($response['status'] === 'success') {
                return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Reverse request successful!']);
            } else {
                return redirect()->back()->with('message', ['type' => 'error', 'text' => 'Reverse request failed.']);
            }
        } catch (\Exception $e) {
            Log::error('Error during transaction reversal: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('message', ['type' => 'error', 'text' => 'An error occurred while reversing the transaction.']);
        }
    }

    public function accountBalance(Request $request)
    {
        try {
            $response = $this->mpesaService->accountBalance();

            if ($response['status'] === 'success') {
                return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Account Balance request successful!']);
            } else {
                return redirect()->back()->with('message', ['type' => 'error', 'text' => 'Account Balance request failed.']);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching account balance: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('message', ['type' => 'error', 'text' => 'An error occurred while fetching the account balance.']);
        }
    }

    public function getTransactionStatus(Request $request)
    {
        try {
            $request->validate([
                'transactionId' => 'required|string',
                'OriginatorConversationID' => 'required|string',
            ]);

            $response = $this->mpesaService->getTransactionStatus(
                $request->input('transactionId'),
                $request->input('OriginatorConversationID')
            );

            if ($response['status'] === 'success') {
                return redirect()->back()->with('message', ['type' => 'success', 'text' => 'Transaction Status request successful!']);
            } else {
                return redirect()->back()->with('message', ['type' => 'error', 'text' => 'Transaction Status request failed.']);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching transaction status: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('message', ['type' => 'error', 'text' => 'An error occurred while fetching the transaction status.']);
        }
    }

    /**
     * =======================================================================================================
     * URLS LISTING AND DATA REPORTING
     * =======================================================================================================
     */
    public function validation(Request $request)
    {
        // Retrieve the raw JSON input
        $rawInput = file_get_contents('php://input');
        // Store raw input data in public/mpesa_logs
        storeLog('Validation', $rawInput);
    }

    public function timeout(Request $request)
    {
        // Retrieve the raw JSON input
        $rawInput = file_get_contents('php://input');
        // Store raw input data in public/mpesa_logs
        storeLog('timeout', $rawInput);
    }

    public function callback(Request $request)
    {
        // Retrieve the raw JSON input
        $rawInput = file_get_contents('php://input');
        // Store raw input data in public/mpesa_logs
        storeLog('stk_push_callback', $rawInput);
    }

    public function confirmation(Request $request)
    {
        // Retrieve the raw JSON input
        $rawInput = file_get_contents('php://input');
        // Decode the JSON response
        $decodedResponse = json_decode($rawInput, true);

        // Store raw input data in public/mpesa_logs
        storeLog('confirmation', $decodedResponse);
    }

    public function result(Request $request)
    {
        // Retrieve the raw JSON input
        $rawInput = file_get_contents('php://input');

        // Store raw input data in public/mpesa_logs
        storeLog('result', $rawInput);
    }
}
