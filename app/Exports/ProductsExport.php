<?php

namespace App\Exports;

use App\Models\Products;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductsExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize, WithTitle {
    protected $data = [];

    public function __construct() {
        $products = Products::with( [ 'branch', 'catalogue' ] )->get();

        $sno = 1;
        foreach ( $products as $product ) {
            $this->data[] = [
                'SNO' => $sno++,
                'Product Name' => $product->name,
                'Branch' => optional( $product->branch )->name ?? 'N/A',
                'Catalogue' => optional( $product->catalogue )->name ?? 'N/A',
                'SKU' => $product->sku ?? '-',
                'Barcode' => $product->barcode ?? '-',
                'Buying Price (KES)' => $product->buying_price,
                'Normal Price (KES)' => $product->normal_price,
                'Wholesale Price (KES)' => $product->whole_sale_price,
                'Agent Price (KES)' => $product->agent_price,
                'Commission (%)' => $product->commission_on_sale,
                'Quantity' => $product->quantity,
                'Unit' => $product->unit,
                'Status' => $product->status,
                'Verification' => $product->is_verified ? 'Verified' : 'Not Verified',
                'Created At' => optional( $product->created_at )->format( 'Y-m-d' ),
            ];
        }
    }

    public function array(): array {
        return $this->data;
    }

    public function headings(): array {
        return [
            [ 'Product Inventory List' ],
            array_keys( $this->data[ 0 ] ?? [] )
        ];
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function ( AfterSheet $event ) {
                $sheet = $event->sheet;
                $rowCount = count( $this->data ) + 2;
                $lastCol = 'P';

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

                // Format price columns as currency
                $priceColumns = [ 'G', 'H', 'I', 'J' ];
                // Buying Price to Agent Price
                foreach ( $priceColumns as $col ) {
                    $sheet->getStyle( "{$col}3:{$col}{$rowCount}" )->getNumberFormat()->setFormatCode( '#,##0.00' );
                }

                // // Format Commission column as percentage
                // $sheet->getStyle( "K3:K{$rowCount}" )->getNumberFormat()->setFormatCode( '0.00%' );

                // Conditional formatting for Verification status
                for ( $i = 3; $i <= $rowCount; $i++ ) {
                    $verificationCell = "O{$i}";
                    $verification = $sheet->getCell( $verificationCell )->getValue();
                    if ( $verification === 'Verified' ) {
                        $sheet->getStyle( $verificationCell )->applyFromArray( [
                            'font' => [ 'color' => [ 'rgb' => '228B22' ], 'bold' => true ],
                        ] );
                    } elseif ( $verification === 'Not Verified' ) {
                        $sheet->getStyle( $verificationCell )->applyFromArray( [
                            'font' => [ 'color' => [ 'rgb' => 'FF0000' ], 'bold' => true ],
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

    public function title(): string {
        return 'Inventory';
    }
}