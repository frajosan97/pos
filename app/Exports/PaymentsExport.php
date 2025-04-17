<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\MpesaPayment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class PaymentsExport implements WithMultipleSheets {
    public function sheets(): array {
        return [
            new class implements FromArray, WithHeadings, WithEvents, ShouldAutoSize, WithTitle {
                protected $data = [];

                public function __construct() {
                    $payments = Payment::with( [ 'branch', 'mpesaPayment', 'paymentMethod' ] )->get();

                    foreach ( $payments as $payment ) {
                        $this->data[] = [
                            'Date'           => optional( $payment->payment_date )->format( 'Y-m-d H:i' ),
                            'Branch Name'    => $payment->branch->name,
                            'Invoice Number' => invoiceNumber( $payment->sale_id ),
                            'M-Pesa Txn ID'  => optional( $payment->mpesaPayment )->transaction_id ?? 'N/A',
                            'M-Pesa Phone'   => optional( $payment->mpesaPayment )->phone ?? 'N/A',
                            'Payment Method' => optional( $payment->paymentMethod )->name ?? 'N/A',
                            'Status'         => $payment->status,
                            'Amount (KES)'   => $payment->amount,
                        ];
                    }
                }

                public function array(): array {
                    return $this->data;
                }

                public function headings(): array {
                    return [ [ 'Payments Report' ], array_keys( $this->data[ 0 ] ?? [] ) ];
                }

                public function title(): string {
                    return 'Payments';
                }

                public function registerEvents(): array {
                    return [
                        AfterSheet::class => function ( AfterSheet $event ) {
                            $sheet = $event->sheet;
                            $rowCount = count( $this->data ) + 2;
                            $totalRow = $rowCount + 1;
                            $lastCol = 'H';

                            // Remove gridlines
                            $sheet->getDelegate()->setShowGridlines( false );

                            // Merge and style title
                            $sheet->mergeCells( "A1:{$lastCol}1" );
                            $sheet->getStyle( 'A1' )->applyFromArray( [
                                'font' => [ 'bold' => true, 'size' => 16, 'color' => [ 'rgb' => 'FFFFFF' ] ],
                                'alignment' => [ 'horizontal' => 'center' ],
                                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => '1F4E78' ] ],
                            ] );

                            // Style headers
                            $sheet->getStyle( "A2:{$lastCol}2" )->applyFromArray( [
                                'font' => [ 'bold' => true, 'color' => [ 'rgb' => 'FFFFFF' ] ],
                                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => '4472C4' ] ],
                                'borders' => [ 'allBorders' => [ 'borderStyle' => 'thin' ] ],
                            ] );

                            // Style data rows with zebra striping
                            for ( $i = 3; $i <= $rowCount; $i++ ) {
                                $bgColor = $i % 2 === 0 ? 'F2F2F2' : 'FFFFFF';
                                $sheet->getStyle( "A{$i}:{$lastCol}{$i}" )->applyFromArray( [
                                    'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => $bgColor ] ],
                                ] );
                            }

                            // Apply borders to data
                            $sheet->getStyle( "A3:{$lastCol}{$rowCount}" )->applyFromArray( [
                                'borders' => [ 'allBorders' => [ 'borderStyle' => 'thin' ] ],
                            ] );

                            // Add totals row
                            $sheet->setCellValue( "G{$totalRow}", 'TOTAL' );
                            $sheet->setCellValue( "H{$totalRow}", '=SUM(H3:H' . $rowCount . ')' );

                            $sheet->getStyle( "G{$totalRow}:H{$totalRow}" )->applyFromArray( [
                                'font' => [ 'bold' => true ],
                                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => 'D9E1F2' ] ],
                                'borders' => [ 'allBorders' => [ 'borderStyle' => 'thin' ] ],
                                'alignment' => [ 'horizontal' => 'right' ],
                            ] );

                            // Format Amount column as currency
                            $sheet->getStyle( "H3:H{$totalRow}" )->getNumberFormat()->setFormatCode( '#,##0.00' );

                            // Conditional formatting for Status
                            for ( $i = 3; $i <= $rowCount; $i++ ) {
                                $statusCell = "G{$i}";
                                $status = $sheet->getCell( $statusCell )->getValue();
                                if ( strtolower( $status ) === 'paid' ) {
                                    $sheet->getStyle( $statusCell )->applyFromArray( [
                                        'font' => [ 'color' => [ 'rgb' => '228B22' ], 'bold' => true ],
                                    ] );
                                } elseif ( strtolower( $status ) === 'pending' ) {
                                    $sheet->getStyle( $statusCell )->applyFromArray( [
                                        'font' => [ 'color' => [ 'rgb' => 'FF8C00' ], 'bold' => true ],
                                    ] );
                                }
                            }

                            // Freeze header
                            $sheet->freezePane( 'A3' );

                            // Add filter
                            $sheet->setAutoFilter( "A2:{$lastCol}2" );
                        }
                    ];
                }
            }
            ,

            new class implements FromArray, WithHeadings, WithEvents, ShouldAutoSize, WithTitle {
                protected $data = [];

                public function __construct() {
                    $mpesaPayments = MpesaPayment::all();

                    foreach ( $mpesaPayments as $payment ) {
                        $this->data[] = [
                            'Date'           => optional( $payment->created_at )->format( 'Y-m-d H:i' ),
                            'Transaction ID' => $payment->transaction_id,
                            'Name'           => $payment->name,
                            'Phone'          => $payment->phone,
                            'Shortcode'      => $payment->shortcode,
                            'Status'         => $payment->status,
                            'Used?'          => $payment->use_status,
                            'Amount (KES)'   => $payment->amount,
                        ];
                    }
                }

                public function array(): array {
                    return $this->data;
                }

                public function headings(): array {
                    return [ [ 'M-PESA Payments Report' ], array_keys( $this->data[ 0 ] ?? [] ) ];
                }

                public function title(): string {
                    return 'M-PESA Payments';
                }

                public function registerEvents(): array {
                    return [
                        AfterSheet::class => function ( AfterSheet $event ) {
                            $sheet = $event->sheet;
                            $rowCount = count( $this->data ) + 2;
                            $totalRow = $rowCount + 1;
                            $lastCol = 'H';

                            $sheet->getDelegate()->setShowGridlines( false );
                            $sheet->mergeCells( "A1:{$lastCol}1" );
                            $sheet->getStyle( 'A1' )->applyFromArray( [
                                'font' => [ 'bold' => true, 'size' => 16, 'color' => [ 'rgb' => 'FFFFFF' ] ],
                                'alignment' => [ 'horizontal' => 'center' ],
                                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => '1F4E78' ] ],
                            ] );

                            $sheet->getStyle( "A2:{$lastCol}2" )->applyFromArray( [
                                'font' => [ 'bold' => true, 'color' => [ 'rgb' => 'FFFFFF' ] ],
                                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => '4472C4' ] ],
                                'borders' => [ 'allBorders' => [ 'borderStyle' => 'thin' ] ],
                                'alignment' => [ 'horizontal' => 'center' ],
                            ] );

                            for ( $i = 3; $i <= $rowCount; $i++ ) {
                                $bgColor = $i % 2 === 0 ? 'F2F2F2' : 'FFFFFF';
                                $sheet->getStyle( "A{$i}:{$lastCol}{$i}" )->applyFromArray( [
                                    'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => $bgColor ] ],
                                ] );
                            }

                            $sheet->getStyle( "A3:{$lastCol}{$rowCount}" )->applyFromArray( [
                                'borders' => [ 'allBorders' => [ 'borderStyle' => 'thin' ] ],
                            ] );

                            $sheet->setCellValue( "G{$totalRow}", 'TOTAL' );
                            $sheet->setCellValue( "H{$totalRow}", '=SUM(H3:H' . $rowCount . ')' );

                            $sheet->getStyle( "G{$totalRow}:H{$totalRow}" )->applyFromArray( [
                                'font' => [ 'bold' => true ],
                                'fill' => [ 'fillType' => 'solid', 'startColor' => [ 'rgb' => 'D9E1F2' ] ],
                                'borders' => [ 'allBorders' => [ 'borderStyle' => 'thin' ] ],
                                'alignment' => [ 'horizontal' => 'center' ],
                            ] );

                            $sheet->getStyle( "H3:H{$totalRow}" )->getNumberFormat()->setFormatCode( '#,##0.00' );
                            $sheet->freezePane( 'A3' );
                            $sheet->setAutoFilter( "A2:{$lastCol}2" );
                        }
                    ];
                }
            }
        ];
    }
}
