<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class UsersExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize, WithTitle {
    protected $data = [];

    public function __construct() {
        $users = User::with( 'branch' )->get();

        $sno = 1;
        foreach ( $users as $user ) {
            $this->data[] = [
                'SNO' => $sno++,
                'Name' => ucwords( strtolower( $user->name ) ),
                'Gender' => ucfirst( $user->gender ?? 'N/A' ),
                'Phone' => $user->phone ?? 'N/A',
                'Email' => strtolower( $user->email ?? 'N/A' ),
                'ID Number' => $user->id_number ?? 'N/A',
                'Status' => $user->status === 1 ? 'Active' : 'Inactive',
                'Commission Rate' => $user->commission_rate ?? 0,
                'Branch' => $user->branch->name ?? 'N/A',
                'Registered On' => optional( $user->created_at )->format( 'Y-m-d' ),
            ];
        }
    }

    public function array(): array {
        return $this->data;
    }

    public function headings(): array {
        return [
            [ 'User Export - Staff Overview' ],
            array_keys( $this->data[ 0 ] ?? [] )
        ];
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function ( AfterSheet $event ) {
                $sheet = $event->sheet;
                $rowCount = count( $this->data ) + 2;
                $lastCol = 'J';

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

                // // Format Commission Rate as percentage
                // $sheet->getStyle( "H3:H{$rowCount}" )->getNumberFormat()->setFormatCode( '0.00%' );

                // Conditional formatting for Status
                for ( $i = 3; $i <= $rowCount; $i++ ) {
                    $statusCell = "G{$i}";
                    $status = $sheet->getCell( $statusCell )->getValue();
                    if ( $status === 'Active' ) {
                        $sheet->getStyle( $statusCell )->applyFromArray( [
                            'font' => [ 'color' => [ 'rgb' => '228B22' ], 'bold' => true ],
                        ] );
                    } elseif ( $status === 'Inactive' ) {
                        $sheet->getStyle( $statusCell )->applyFromArray( [
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
        return 'Employees';
    }
}