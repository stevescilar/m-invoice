<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->compileReport($request);
        return view('reports.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->compileReport($request);
        $pdf  = Pdf::loadView('reports.pdf', $data)->setPaper('a4', 'landscape');

        $filename = 'financial-tax-report-' . $data['startDate']->format('Y-m-d') . '-to-' . $data['endDate']->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $data     = $this->compileReport($request);
        $filename = 'financial-tax-report-' . $data['startDate']->format('Y-m-d') . '-to-' . $data['endDate']->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [$data['company']->name . ' — Financial, Profit & KRA VAT Report']);
            fputcsv($out, ['Period', $data['startDate']->format('d M Y') . ' to ' . $data['endDate']->format('d M Y')]);
            fputcsv($out, []);

            // Summary section
            fputcsv($out, ['SUMMARY METRIC', 'AMOUNT (KSH)']);
            fputcsv($out, ['Total Invoiced (Gross)', number_format($data['totalBilled'], 2, '.', '')]);
            fputcsv($out, ['Total Cash Collected (Paid + Deposits)', number_format($data['totalCollected'], 2, '.', '')]);
            fputcsv($out, ['Outstanding Receivables', number_format($data['totalOutstanding'], 2, '.', '')]);
            fputcsv($out, ['Material Buying Cost (COGS)', number_format($data['totalBuyingCost'], 2, '.', '')]);
            fputcsv($out, ['Gross Profit on Invoices', number_format($data['grossInvoiceProfit'], 2, '.', '')]);
            fputcsv($out, ['Operating Expenses', number_format($data['totalExpenses'], 2, '.', '')]);
            fputcsv($out, ['Net Profit (After Expenses)', number_format($data['netProfit'], 2, '.', '')]);
            fputcsv($out, ['KRA ETR VAT Billed (16%)', number_format($data['vatBilled'], 2, '.', '')]);
            fputcsv($out, ['KRA ETR VAT Collected (16%)', number_format($data['vatCollected'], 2, '.', '')]);
            fputcsv($out, []);

            // Invoices breakdown
            fputcsv($out, ['INVOICES BREAKDOWN']);
            fputcsv($out, [
                'Invoice #', 'Issue Date', 'Client', 'Status', 'ETR',
                'Material Selling', 'Labour', 'Discount', 'VAT (16%)',
                'Grand Total', 'Amount Paid', 'Balance Due', 'Material Buying Cost', 'Invoice Profit',
            ]);

            foreach ($data['invoices'] as $inv) {
                fputcsv($out, [
                    $inv->invoice_number,
                    $inv->issue_date->format('Y-m-d'),
                    $inv->client->name ?? '—',
                    strtoupper($inv->status),
                    $inv->etr_enabled ? 'YES' : 'NO',
                    number_format((float) $inv->material_cost, 2, '.', ''),
                    number_format((float) $inv->labour_cost, 2, '.', ''),
                    number_format((float) $inv->discount_amount, 2, '.', ''),
                    number_format((float) $inv->vat_amount, 2, '.', ''),
                    number_format((float) $inv->grand_total, 2, '.', ''),
                    number_format($inv->totalPaidAmount(), 2, '.', ''),
                    number_format($inv->balanceDue(), 2, '.', ''),
                    number_format((float) $inv->total_cost, 2, '.', ''),
                    number_format((float) $inv->total_profit, 2, '.', ''),
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ['EXPENSES BREAKDOWN']);
            fputcsv($out, ['Date', 'Category', 'Description', 'Amount (Ksh)']);

            foreach ($data['expenses'] as $exp) {
                fputcsv($out, [
                    Carbon::parse($exp->expense_date)->format('Y-m-d'),
                    $exp->category,
                    $exp->description,
                    number_format((float) $exp->amount, 2, '.', ''),
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function compileReport(Request $request): array
    {
        $company = Auth::user()->company;
        $period  = $request->get('period', 'this_month');

        [$startDate, $endDate] = match ($period) {
            'last_month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_quarter' => [now()->firstOfQuarter(), now()->lastOfQuarter()->endOfDay()],
            'this_year'    => [now()->startOfYear(), now()->endOfYear()],
            'all_time'     => [Carbon::parse('2020-01-01')->startOfDay(), now()->endOfDay()],
            'custom'       => [
                $request->filled('from') ? Carbon::parse($request->get('from'))->startOfDay() : now()->startOfMonth(),
                $request->filled('to') ? Carbon::parse($request->get('to'))->endOfDay() : now()->endOfDay(),
            ],
            default        => [now()->startOfMonth(), now()->endOfMonth()],
        };

        $invoices = Invoice::with('client')
            ->where('company_id', $company->id)
            ->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('issue_date')
            ->get();

        $expenses = Expense::where('company_id', $company->id)
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('expense_date')
            ->get();

        $totalBilled        = (float) $invoices->sum('grand_total');
        $totalCollected     = (float) $invoices->sum(fn ($inv) => $inv->totalPaidAmount());
        $totalOutstanding   = max(0, $totalBilled - $totalCollected);
        $totalMaterialSales = (float) $invoices->sum('material_cost');
        $totalLabourSales   = (float) $invoices->sum('labour_cost');
        $totalBuyingCost    = (float) $invoices->sum('total_cost');
        $grossInvoiceProfit = (float) $invoices->sum('total_profit');
        $totalExpenses      = (float) $expenses->sum('amount');
        $netProfit          = $grossInvoiceProfit - $totalExpenses;
        $netMargin          = $totalBilled > 0 ? round(($netProfit / $totalBilled) * 100, 1) : 0;

        $etrInvoices  = $invoices->where('etr_enabled', true);
        $vatBilled    = (float) $etrInvoices->sum('vat_amount');
        $vatCollected = (float) $etrInvoices->where('status', 'paid')->sum('vat_amount');
        $taxableSales = max(0, (float) $etrInvoices->sum('grand_total') - $vatBilled);

        return compact(
            'company',
            'period',
            'startDate',
            'endDate',
            'invoices',
            'expenses',
            'totalBilled',
            'totalCollected',
            'totalOutstanding',
            'totalMaterialSales',
            'totalLabourSales',
            'totalBuyingCost',
            'grossInvoiceProfit',
            'totalExpenses',
            'netProfit',
            'netMargin',
            'etrInvoices',
            'vatBilled',
            'vatCollected',
            'taxableSales'
        );
    }
}
