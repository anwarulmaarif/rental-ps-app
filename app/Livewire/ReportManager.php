<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportManager extends Component
{
    public $segment = 'bulanan'; // Default tab
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = date('m');
        $this->selectedYear = date('Y');
    }

    public function setSegment($tipe)
    {
        $this->segment = $tipe;
        // Pancing browser untuk merender ulang chart setelah ganti tab
        $this->dispatch('refresh-charts'); 
    }

    public function render()
    {
        $query = Rental::query();
        $query->whereYear('created_at', $this->selectedYear);

        if ($this->segment === 'bulanan') {
            $query->whereMonth('created_at', $this->selectedMonth);
            
            // Data Harian (Untuk List & Chart Garis)
            $trendData = (clone $query)
                ->select(DB::raw('DATE(created_at) as label'), DB::raw('SUM(total_biaya) as pendapatan'), DB::raw('SUM(durasi) as total_jam'))
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        } else {
            // Data Bulanan (Untuk List & Chart Garis Tahun Terpilih)
            $trendData = (clone $query)
                ->select(DB::raw('MONTH(created_at) as label'), DB::raw('SUM(total_biaya) as pendapatan'), DB::raw('SUM(durasi) as total_jam'))
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        }

        // Data Per Unit (Sama untuk bulanan & tahunan, hanya terpengaruh filter query di atas)
        $unitData = (clone $query)
            ->select('nama_unit', DB::raw('SUM(total_biaya) as pendapatan'), DB::raw('SUM(durasi) as total_jam'))
            ->groupBy('nama_unit')
            ->orderBy('pendapatan', 'desc')
            ->get();

        // Total Ringkasan Atas
        $totalPendapatan = $unitData->sum('pendapatan');
        $totalJam = $unitData->sum('total_jam');
        $unitTerlaris = $unitData->first()->nama_unit ?? '-';

        return view('livewire.report-manager', [
            'trendData' => $trendData,
            'unitData' => $unitData,
            'totalPendapatan' => $totalPendapatan,
            'totalJam' => $totalJam,
            'unitTerlaris' => $unitTerlaris,
        ])->layout('layouts.app');
    }
}