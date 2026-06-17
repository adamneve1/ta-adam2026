<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Pks;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $today = Carbon::today();

        $yearOptions = Pks::query()
            ->whereNotNull('tanggal')
            ->pluck('tanggal')
            ->map(fn ($date) => Carbon::parse($date)->year)
            ->push($today->year)
            ->unique()
            ->sortDesc()
            ->values();

        $selectedYear = (int) $request->query('year', $today->year);
        if (! $yearOptions->contains($selectedYear)) {
            $selectedYear = $today->year;
        }

        $period = in_array($request->query('period'), ['month', 'quarter', 'year', 'custom'], true)
            ? $request->query('period')
            : 'year';

        $periodBaseDate = $today->copy()->year($selectedYear);
        $quarter = (int) ceil($periodBaseDate->month / 3);
        $customStartDate = $this->parseDate($request->query('start_date'));
        $customEndDate = $this->parseDate($request->query('end_date'));
        $hasValidCustomRange = $customStartDate && $customEndDate && $customStartDate->lte($customEndDate);

        [$startDate, $endDate, $periodLabel] = match ($period) {
            'custom' => $hasValidCustomRange
                ? [
                    $customStartDate->copy()->startOfDay(),
                    $customEndDate->copy()->endOfDay(),
                    $customStartDate->copy()->locale('id')->translatedFormat('d M Y') . ' - ' . $customEndDate->copy()->locale('id')->translatedFormat('d M Y'),
                ]
                : [
                    Carbon::create($selectedYear, 1, 1)->startOfDay(),
                    Carbon::create($selectedYear, 12, 31)->endOfDay(),
                    'Tahun ' . $selectedYear,
                ],
            'month' => [
                $periodBaseDate->copy()->startOfMonth(),
                $periodBaseDate->copy()->endOfMonth(),
                $periodBaseDate->copy()->locale('id')->translatedFormat('F Y'),
            ],
            'quarter' => [
                $periodBaseDate->copy()->startOfQuarter(),
                $periodBaseDate->copy()->endOfQuarter(),
                'Triwulan ' . $quarter . ' ' . $selectedYear,
            ],
            default => [
                Carbon::create($selectedYear, 1, 1)->startOfDay(),
                Carbon::create($selectedYear, 12, 31)->endOfDay(),
                'Tahun ' . $selectedYear,
            ],
        };

        if ($period === 'custom' && ! $hasValidCustomRange) {
            $period = 'year';
        }

        $startDateInput = $startDate->toDateString();
        $endDateInput = $endDate->toDateString();

        $applyPeriod = function ($query, string $column) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween($column, [$startDate->toDateString(), $endDate->toDateString()]);
            }

            return $query;
        };

        $pksQuery = $applyPeriod(Pks::query(), 'tanggal');
        $paymentQuery = $applyPeriod(Payment::query(), 'tanggal_pembayaran');

        $stats = [
            'total_pks' => (clone $pksQuery)->count(),
            'nilai_kontrak' => (clone $pksQuery)->sum('total'),
            'invoice_menunggu_bayar' => $applyPeriod(Invoice::where('status', '!=', Invoice::STATUS_PAID)
                ->whereNotNull('kode_billing')
                ->where('kode_billing', '!=', ''), 'tanggal_invoice')
                ->count(),
            'invoice_jatuh_tempo' => $applyPeriod(Invoice::where('status', '!=', Invoice::STATUS_PAID)
                ->whereDate('tanggal_jatuh_tempo', '<', $today), 'tanggal_jatuh_tempo')
                ->count(),
            'pks_belum_invoice' => $applyPeriod(Pks::doesntHave('invoices'), 'tanggal')->count(),
            'penerimaan_periode' => (clone $paymentQuery)->sum('jumlah_pembayaran'),
        ];

        $stats['realisasi_penerimaan'] = $stats['nilai_kontrak'] > 0
            ? min(100, round(($stats['penerimaan_periode'] / $stats['nilai_kontrak']) * 100, 1))
            : 0;
        $stats['belum_terealisasi'] = max(0, $stats['nilai_kontrak'] - $stats['penerimaan_periode']);

        $recentInvoices = $applyPeriod(Invoice::with('pks.client'), 'tanggal_invoice')
            ->orderByDesc('tanggal_invoice')
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard', compact('stats', 'recentInvoices', 'period', 'periodLabel', 'selectedYear', 'yearOptions', 'startDateInput', 'endDateInput'));
    }

    private function parseDate(?string $date): ?Carbon
    {
        if (! $date) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Throwable) {
            return null;
        }
    }
}
