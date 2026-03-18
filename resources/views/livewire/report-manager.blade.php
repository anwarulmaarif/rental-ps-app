<div class="p-4 md:p-6 max-w-6xl mx-auto" x-data="reportCharts()">
    
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-gray-800 mb-4">📈 Laporan Bisnis</h2>
        
        <div class="flex bg-gray-200 p-1 rounded-2xl">
            <button wire:click="setSegment('bulanan')" class="flex-1 py-3 text-sm font-bold rounded-xl transition-all {{ $segment === 'bulanan' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500' }}">
                Bulan Ini
            </button>
            <button wire:click="setSegment('tahunan')" class="flex-1 py-3 text-sm font-bold rounded-xl transition-all {{ $segment === 'tahunan' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500' }}">
                Tahun Ini
            </button>
        </div>
    </div>

    <div class="flex gap-4 mb-6">
        @if($segment === 'bulanan')
        <div class="flex-1">
            <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">Pilih Bulan</label>
            <select wire:model.live="selectedMonth" class="w-full bg-white p-3 rounded-2xl border border-gray-100 font-bold shadow-sm outline-none">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ sprintf('%02d', $i) }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>
        @endif
        <div class="flex-1">
            <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">Pilih Tahun</label>
            <select wire:model.live="selectedYear" class="w-full bg-white p-3 rounded-2xl border border-gray-100 font-bold shadow-sm outline-none">
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-5 rounded-3xl text-white shadow-lg shadow-blue-200">
            <p class="text-[10px] uppercase font-bold opacity-80 mb-1">Total Pendapatan</p>
            <h3 class="text-xl md:text-2xl font-black">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-5 rounded-3xl text-white shadow-lg shadow-purple-200">
            <p class="text-[10px] uppercase font-bold opacity-80 mb-1">Total Waktu (Jam)</p>
            <h3 class="text-xl md:text-2xl font-black">{{ $totalJam }} Jam</h3>
            <p class="text-[10px] mt-1 opacity-90">Terlaris: <b>{{ $unitTerlaris }}</b></p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-3xl shadow-md border border-gray-50 mb-6">
        <h4 class="font-bold text-gray-700 mb-4">Tren Pendapatan {{ $segment == 'bulanan' ? 'Harian' : 'Bulanan' }}</h4>
        <div id="trendChart" class="w-full h-64 mb-4" wire:ignore></div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-100">
                        <th class="py-2">{{ $segment == 'bulanan' ? 'Tanggal' : 'Bulan' }}</th>
                        <th class="py-2 text-center">Jam</th>
                        <th class="py-2 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trendData as $data)
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="py-3 font-semibold text-gray-700">
                            {{ $segment == 'bulanan' ? \Carbon\Carbon::parse($data->label)->format('d M') : \Carbon\Carbon::create()->month($data->label)->translatedFormat('M') }}
                        </td>
                        <td class="py-3 text-center text-gray-500">{{ $data->total_jam }}j</td>
                        <td class="py-3 text-right font-bold text-emerald-600">Rp {{ number_format($data->pendapatan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white p-5 rounded-3xl shadow-md border border-gray-50 mb-6">
        <h4 class="font-bold text-gray-700 mb-4">Performa Unit TV</h4>
        <div id="unitChart" class="w-full h-64 mb-4 flex justify-center" wire:ignore></div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-100">
                        <th class="py-2">Unit</th>
                        <th class="py-2 text-center">Jam</th>
                        <th class="py-2 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unitData as $data)
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="py-3 font-black text-gray-800">{{ $data->nama_unit }}</td>
                        <td class="py-3 text-center text-gray-500">{{ $data->total_jam }}j</td>
                        <td class="py-3 text-right font-bold text-emerald-600">Rp {{ number_format($data->pendapatan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportCharts', () => ({
                trendChartInstance: null,
                unitChartInstance: null,
                
                init() {
                    this.renderCharts();
                    
                    // Dengarkan event dari Livewire jika data berubah (ganti bulan/tahun/segment)
                    window.addEventListener('refresh-charts', () => {
                        setTimeout(() => this.renderCharts(), 100); 
                    });
                    
                    // Listener bawaan Livewire 3 untuk update DOM
                    document.addEventListener('livewire:navigated', () => {
                        this.renderCharts();
                    });
                },

                renderCharts() {
                    // Ambil data JSON dari backend yang dirender ke HTML
                    let rawTrend = @json($trendData);
                    let rawUnit = @json($unitData);

                    let trendLabels = rawTrend.map(i => i.label);
                    let trendSeries = rawTrend.map(i => i.pendapatan);

                    let unitLabels = rawUnit.map(i => i.nama_unit);
                    let unitSeries = rawUnit.map(i => parseInt(i.pendapatan)); // Donut chart butuh integer

                    // Destroy chart lama sebelum render ulang
                    if(this.trendChartInstance) this.trendChartInstance.destroy();
                    if(this.unitChartInstance) this.unitChartInstance.destroy();

                    // 1. Chart Trend (Area/Bar)
                    let trendOptions = {
                        series: [{ name: 'Pendapatan', data: trendSeries }],
                        chart: { type: 'area', height: 250, toolbar: { show: false }, zoom: {enabled: false} },
                        colors: ['#3b82f6'],
                        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2, stops: [0, 90, 100] } },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 3 },
                        xaxis: { categories: trendLabels, labels: { style: { colors: '#9ca3af', fontSize: '10px' } } },
                        yaxis: { labels: { formatter: (value) => "Rp " + (value/1000) + "K", style: { colors: '#9ca3af', fontSize: '10px' } } }
                    };
                    this.trendChartInstance = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
                    this.trendChartInstance.render();

                    // 2. Chart Unit (Donut)
                    let unitOptions = {
                        series: unitSeries,
                        chart: { type: 'donut', height: 250 },
                        labels: unitLabels,
                        colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                        dataLabels: { enabled: false },
                        plotOptions: { pie: { donut: { size: '70%' } } },
                        legend: { position: 'bottom', fontSize: '12px', markers: { radius: 12 } }
                    };
                    this.unitChartInstance = new ApexCharts(document.querySelector("#unitChart"), unitOptions);
                    this.unitChartInstance.render();
                }
            }));
        });
    </script>
</div>