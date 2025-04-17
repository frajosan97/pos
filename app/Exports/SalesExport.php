<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class SalesExport implements WithMultipleSheets {
    public function sheets(): array {
        return [
            new class implements FromArray, WithHeadings, WithEvents, ShouldAutoSize, WithTitle {
                protected $data = [];
                protected $totals = [
                    'items' => 0,
                    'total_amount' => 0,
                    'amount_paid' => 0
                ];

                public function __construct() {
                    $sales = Sale::with(['user', 'branch', 'customer', 'saleItems', 'payments'])->get();

                    foreach ($sales as $sale) {
                        $totalItems = $sale->saleItems->count();
                        $amountPaid = $sale->payments->sum('amount');
                        $totalBilled = $sale->total_amount;

                        $this->totals['items'] += $totalItems;
                        $this->totals['total_amount'] += $totalBilled;
                        $this->totals['amount_paid'] += $amountPaid;

                        $this->data[] = [
                            'Date' => optional($sale->created_at)->format('Y-m-d H:i'),
                            'Invoice No' => invoiceNumber($sale->id),
                            'Customer' => $sale->customer->name ?? 'Walk-in',
                            'Sale Type' => ucfirst($sale->sale_type),
                            'Sales Person' => $sale->user->name ?? 'N/A',
                            'Branch' => $sale->branch->name ?? 'N/A',
                            'Items Sold' => $totalItems,
                            'Total Amount (KES)' => $totalBilled,
                            'Amount Paid (KES)' => $amountPaid,
                        ];
                    }
                }

                public function array(): array {
                    return $this->data;
                }

                public function headings(): array {
                    return [ [ 'Sales Report' ], array_keys($this->data[0] ?? []) ];
                }

                public function title(): string {
                    return 'Sales';
                }

                public function registerEvents(): array {
                    return [
                        AfterSheet::class => function (AfterSheet $event) {
                            $sheet = $event->sheet;
                            $rowCount = count($this->data) + 2;
                            $totalRow = $rowCount + 1;
                            $lastCol = 'I';

                            // Remove gridlines
                            $sheet->getDelegate()->setShowGridlines(false);

                            // Merge and style title
                            $sheet->mergeCells("A1:{$lastCol}1");
                            $sheet->getStyle('A1')->applyFromArray([
                                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                                'alignment' => ['horizontal' => 'center'],
                                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F4E78']],
                            ]);

                            // Style headers
                            $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
                                'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                            ]);

                            // Style data rows with zebra striping
                            for ($i = 3; $i <= $rowCount; $i++) {
                                $bgColor = $i % 2 === 0 ? 'F2F2F2' : 'FFFFFF';
                                $sheet->getStyle("A{$i}:{$lastCol}{$i}")->applyFromArray([
                                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $bgColor]],
                                ]);
                            }

                            // Apply borders to data
                            $sheet->getStyle("A3:{$lastCol}{$rowCount}")->applyFromArray([
                                'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                            ]);

                            // Add totals row
                            $sheet->setCellValue("G{$totalRow}", 'TOTAL');
                            $sheet->setCellValue("H{$totalRow}", '=SUM(H3:H' . $rowCount . ')');
                            $sheet->setCellValue("I{$totalRow}", '=SUM(I3:I' . $rowCount . ')');

                            $sheet->getStyle("G{$totalRow}:I{$totalRow}")->applyFromArray([
                                'font' => ['bold' => true],
                                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E1F2']],
                                'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                                'alignment' => ['horizontal' => 'right'],
                            ]);

                            // Format Amount columns as currency
                            $sheet->getStyle("H3:I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                            // Freeze header
                            $sheet->freezePane('A3');

                            // Add filter
                            $sheet->setAutoFilter("A2:{$lastCol}2");
                        }
                    ];
                }
            },

            new class implements FromArray, WithHeadings, WithEvents, ShouldAutoSize, WithTitle {
                protected $data = [];

                public function __construct() {
                    $sales = Sale::with(['saleItems.product'])->get();

                    foreach ($sales as $sale) {
                        foreach ($sale->saleItems as $item) {
                            $this->data[] = [
                                'Invoice No' => invoiceNumber($sale->id),
                                'Product' => $item->product->name ?? 'Unknown',
                                'Quantity' => $item->quantity,
                                'Unit Price (KES)' => $item->unit_price,
                                'Total (KES)' => $item->quantity * $item->unit_price,
                            ];
                        }
                    }
                }

                public function array(): array {
                    return $this->data;
                }

                public function headings(): array {
                    return [ [ 'Sale Items Report' ], array_keys($this->data[0] ?? []) ];
                }

                public function title(): string {
                    return 'Sale Items';
                }

                public function registerEvents(): array {
                    return [
                        AfterSheet::class => function (AfterSheet $event) {
                            $sheet = $event->sheet;
                            $rowCount = count($this->data) + 2;
                            $lastCol = 'E';

                            // Remove gridlines
                            $sheet->getDelegate()->setShowGridlines(false);

                            // Merge and style title
                            $sheet->mergeCells("A1:{$lastCol}1");
                            $sheet->getStyle('A1')->applyFromArray([
                                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                                'alignment' => ['horizontal' => 'center'],
                                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F4E78']],
                            ]);

                            // Style headers
                            $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
                                'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                            ]);

                            // Style data rows with zebra striping
                            for ($i = 3; $i <= $rowCount; $i++) {
                                $bgColor = $i % 2 === 0 ? 'F2F2F2' : 'FFFFFF';
                                $sheet->getStyle("A{$i}:{$lastCol}{$i}")->applyFromArray([
                                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $bgColor]],
                                ]);
                            }

                            // Apply borders to data
                            $sheet->getStyle("A3:{$lastCol}{$rowCount}")->applyFromArray([
                                'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                            ]);

                            // Format Amount columns as currency
                            $sheet->getStyle("D3:E{$rowCount}")->getNumberFormat()->setFormatCode('#,##0.00');

                            // Freeze header
                            $sheet->freezePane('A3');

                            // Add filter
                            $sheet->setAutoFilter("A2:{$lastCol}2");
                        }
                    ];
                }
            }
        ];
    }
}