<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\User;
use App\Models\Sale;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

class PdfController extends Controller
{
    /**
     * Generate PDF and return the response.
     *
     * @param string $html
     * @param string $fileName
     * @param string $viewType
     * @return \Illuminate\Http\Response
     */
    protected function generatePDF(string $html, string $fileName, string $viewType, string $orientation)
    {
        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => $orientation,
            ]);

            // Set the Footer
            $mpdf->SetHTMLFooter('
                <div style="text-align: center; color: gray;">
                    Page {PAGENO} of {nbpg}
                </div>
            ');

            $mpdf->SetBasePath(public_path()); // Set base path for assets
            $mpdf->WriteHTML($html); // Write HTML content to the PDF

            $pdfContent = $mpdf->Output('', $viewType); // Generate PDF as string

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header("Content-Disposition", "inline; filename=\"{$fileName}.pdf\"");
        } catch (\Throwable $th) {
            // Log the error message
            Log::error('PDF Generation Error', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);

            return response()->json(['error' => 'Failed to generate PDF. Please try again later.'], 500);
        }
    }

    /**
     * Generate an sales PDF.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sales(Request $request)
    {
        // Fetch sales data and render it to HTML
        $sales = Sale::with(['payments.paymentMethod'])->get();
        $html = View::make('portal.pdf.sales', compact('sales'))->render();
        $fileName = 'sales';

        // Generate and return the PDF
        return $this->generatePDF($html, $fileName, 'S', 'L');
    }

    /**
     * Generate an inventory PDF.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function inventory(Request $request)
    {
        // Fetch inventory data and render it to HTML
        $inventory = Products::with(['catalogue'])->get();
        $html = View::make('portal.pdf.inventory', compact('inventory'))->render();
        $fileName = 'inventory';

        // Generate and return the PDF
        return $this->generatePDF($html, $fileName, 'S', 'L');
    }

    /**
     * Generate an employee PDF.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function employee(Request $request)
    {
        // Fetch employee data and render it to HTML
        $employee = User::with(['branch'])->get();
        $html = View::make('portal.pdf.employee', compact('employee'))->render();
        $fileName = 'employee';

        // Generate and return the PDF
        return $this->generatePDF($html, $fileName, 'S', 'P');
    }

    /**
     * Generate an branch PDF.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function branch(Request $request)
    {
        // Fetch branch data and render it to HTML
        $branch = Branch::with(['county', 'constituency', 'ward'])->get();
        $html = View::make('portal.pdf.branch', compact('branch'))->render();
        $fileName = 'branch';

        // Generate and return the PDF
        return $this->generatePDF($html, $fileName, 'S', 'P');
    }

    /**
     * Generate an invoice PDF.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function invoice(Request $request, string $id)
    {
        // Fetch invoice data and render it to HTML
        $invoice = Sale::with(['saleItems.product', 'payments.paymentMethod', 'customer'])->findOrFail($id);
        $html = View::make('portal.pdf.invoice', compact('invoice'))->render();
        $fileName = 'invoice';

        // Generate and return the PDF
        return $this->generatePDF($html, $fileName, 'S', 'P');
    }

    /**
     * Generate an company PDF.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function company(Request $request)
    {
        // Fetch company data and render it to HTML
        $company = Company::first();
        $html = View::make('portal.pdf.company', compact('company'))->render();
        $fileName = 'company';

        // Generate and return the PDF
        return $this->generatePDF($html, $fileName, 'S', 'P');
    }

    public function contractLetter(Request $request, string $id)
    {
        $employee = User::findOrFail($id);
        $html = View::make('portal.pdf.contract_letter', compact('employee'))->render();
        $fileName = 'contract_letter';

        // Generate and return the PDF
        return $this->generatePDF($html, $fileName, 'S', 'P');
    }
}
