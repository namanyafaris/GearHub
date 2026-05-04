<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facades\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
	public function index(Request $request)
	{
		$month = $request->input('month', Carbon::now()->month);
		$year = $request->input('year', Carbon::now()->year);

		$start = Carbon::create($year, $month, 1)->startOfMonth();
		$end = $start->copy()->endOfMonth();

		$transactions = Order::with('user')
			->whereBetween('created_at', [$start, $end])
			->orderBy('created_at', 'desc')
			->get();

		$total = $transactions->whereNotIn('status', ['pending', 'cancelled'])->sum('total_price');

		return view('admin.reports.index', compact('transactions', 'total', 'month', 'year'));
	}

	public function exportPdf(Request $request)
	{
		$month = $request->input('month', Carbon::now()->month);
		$year = $request->input('year', Carbon::now()->year);

		$start = Carbon::create($year, $month, 1)->startOfMonth();
		$end = $start->copy()->endOfMonth();

		$transactions = Order::with('user')
			->whereBetween('created_at', [$start, $end])
			->orderBy('created_at', 'desc')
			->get();

		$total = $transactions->whereNotIn('status', ['pending', 'cancelled'])->sum('total_price');

		$pdf = Pdf::loadView('admin.reports.pdf', compact('transactions', 'total', 'month', 'year'));

		$filename = "sales_report_{$year}_{$month}.pdf";

		return $pdf->download($filename);
	}
}
