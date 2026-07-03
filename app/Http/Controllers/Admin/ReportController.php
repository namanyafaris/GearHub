<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
	public function index(Request $request)
	{
		$startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
		$endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

		$start = Carbon::parse($startDate)->startOfDay();
		$end = Carbon::parse($endDate)->endOfDay();

		$transactions = Order::with('user')
			->whereBetween('created_at', [$start, $end])
			->orderBy('created_at', 'desc')
			->get();

		$total = $transactions->whereNotIn('status', ['pending', 'cancelled'])->sum('total_price');

		return view('admin.reports.index', compact('transactions', 'total', 'startDate', 'endDate'));
	}

	public function exportPdf(Request $request)
	{
		$startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
		$endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

		$start = Carbon::parse($startDate)->startOfDay();
		$end = Carbon::parse($endDate)->endOfDay();

		$transactions = Order::with('user')
			->whereBetween('created_at', [$start, $end])
			->orderBy('created_at', 'desc')
			->get();

		$total = $transactions->whereNotIn('status', ['pending', 'cancelled'])->sum('total_price');

		$pdf = Pdf::loadView('admin.reports.pdf', compact('transactions', 'total', 'startDate', 'endDate'));

		$filename = "sales_report_{$startDate}_to_{$endDate}.pdf";

		return $pdf->download($filename);
	}
}
