<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    private const TVA_RATE = 0.20;

    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $products = $this->getProductSales($start, $end);

        $totals = [
            'quantity' => $products->sum('total_quantity'),
            'total_ttc' => $products->sum('total_ttc'),
            'total_ht' => $products->sum('total_ht'),
        ];

        return view('admin.exports.index', compact('products', 'month', 'totals'));
    }

    public function excel(Request $request): StreamedResponse
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $products = $this->getProductSales($start, $end);
        $monthLabel = $start->translatedFormat('F Y');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ventes');

        // En-têtes
        $headers = ['Produit', 'SKU', 'Quantité', 'Prix unit. TTC', 'Total TTC', 'Total HT'];
        $sheet->fromArray($headers, null, 'A1');

        // Style en-têtes
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '276E44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Données
        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue("A{$row}", $product->product_name);
            $sheet->setCellValue("B{$row}", $product->sku ?? '');
            $sheet->setCellValue("C{$row}", (int) $product->total_quantity);
            $sheet->setCellValue("D{$row}", (float) $product->unit_price);
            $sheet->setCellValue("E{$row}", (float) $product->total_ttc);
            $sheet->setCellValue("F{$row}", (float) $product->total_ht);
            $row++;
        }

        // Ligne totaux
        if ($products->isNotEmpty()) {
            $sheet->setCellValue("A{$row}", 'TOTAL');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->setCellValue("C{$row}", "=SUM(C2:C".($row - 1).')');
            $sheet->setCellValue("E{$row}", "=SUM(E2:E".($row - 1).')');
            $sheet->setCellValue("F{$row}", "=SUM(F2:F".($row - 1).')');

            $totalStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
            ];
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($totalStyle);
        }

        // Format monétaire sur les colonnes prix
        $lastDataRow = $row;
        $sheet->getStyle("D2:F{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0.00 €');
        $sheet->getStyle("C2:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-largeur colonnes
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "ventes-{$month}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function daily(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $days = $this->getDailySales($start, $end);

        $totals = [
            'orders' => $days->sum('orders_count'),
            'total_ttc' => $days->sum('total_ttc'),
            'total_ht' => $days->sum('total_ht'),
        ];

        return view('admin.exports.daily', compact('days', 'month', 'totals'));
    }

    public function dailyExcel(Request $request): StreamedResponse
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $days = $this->getDailySales($start, $end);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CA journalier');

        $headers = ['Date', 'Commandes', 'CA TTC', 'CA HT'];
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '276E44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($days as $day) {
            $date = Carbon::parse($day->date);
            $sheet->setCellValue("A{$row}", $date->format('d/m/Y'));
            $sheet->setCellValue("B{$row}", (int) $day->orders_count);
            $sheet->setCellValue("C{$row}", (float) $day->total_ttc);
            $sheet->setCellValue("D{$row}", (float) $day->total_ht);
            $row++;
        }

        if ($days->isNotEmpty()) {
            $sheet->setCellValue("A{$row}", 'TOTAL');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->setCellValue("B{$row}", "=SUM(B2:B".($row - 1).')');
            $sheet->setCellValue("C{$row}", "=SUM(C2:C".($row - 1).')');
            $sheet->setCellValue("D{$row}", "=SUM(D2:D".($row - 1).')');

            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        $lastDataRow = $row;
        $sheet->getStyle("C2:D{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0.00 €');
        $sheet->getStyle("B2:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, "ca-journalier-{$month}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function getDailySales(Carbon $start, Carbon $end)
    {
        return Order::query()
            ->whereIn('status', ['processing', 'shipped', 'completed'])
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('
                DATE(paid_at) as date,
                COUNT(*) as orders_count,
                SUM(total) as total_ttc,
                ROUND(SUM(total) / ?, 2) as total_ht
            ', [1 + self::TVA_RATE])
            ->groupByRaw('DATE(paid_at)')
            ->orderBy('date')
            ->get();
    }

    private function getProductSales(Carbon $start, Carbon $end)
    {
        return OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['processing', 'shipped', 'completed'])
            ->whereBetween('orders.paid_at', [$start, $end])
            ->selectRaw('
                order_items.product_name,
                order_items.sku,
                order_items.unit_price,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total) as total_ttc,
                ROUND(SUM(order_items.total) / ?, 2) as total_ht
            ', [1 + self::TVA_RATE])
            ->groupBy('order_items.product_name', 'order_items.sku', 'order_items.unit_price')
            ->orderByDesc('total_quantity')
            ->get();
    }
}
