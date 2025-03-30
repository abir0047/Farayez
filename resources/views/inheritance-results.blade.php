@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 max-w-4xl text-center">
        <h2 class="text-xl font-bold text-blue-900">মুসলিম উত্তরাধিকারের আইন অনুযায়ী সম্পত্তি বন্টন</h2>

        <div class="my-4">
            <button class="px-6 py-2 bg-red-600 text-white rounded-lg">ফলাফল</button>
        </div>

        <h3 class="text-lg font-semibold text-red-700">মৃত এর সম্পত্তির বন্টন</h3>

        <div class="grid grid-cols-2 gap-4 my-6 p-4 border rounded-lg bg-gray-100">
            @foreach ($assets as $asset)
                <div>{{ $asset['name'] }}: {{ number_format($asset['value']) }} {{ $asset['unit'] }}</div>
            @endforeach
        </div>

        <!-- Add Pie Chart Section -->
        <div class="my-8">
            <h3 class="text-lg font-semibold mb-4">শেয়ার অনুযায়ী সম্পত্তির বন্টন চার্ট</h3>
            <div class="chart-container" style="position: relative; height:400px; width:100%">
                <canvas id="sharePieChart"></canvas>
            </div>
        </div>

        <table class="w-full border-collapse border border-gray-300 my-6">
            <thead class="bg-blue-900 text-white">
                <tr>
                    <th class="p-2 border">সম্পর্ক</th>
                    <th class="p-2 border">নাম</th>
                    <th class="p-2 border">শেয়ারের পরিমাণ</th>
                    @foreach ($assets as $asset)
                        <th class="p-2 border relative group">
                            {{ $asset['name'] }}
                            <span
                                class="absolute hidden group-hover:block bg-gray-800 text-white text-xs rounded p-1 -bottom-8 left-1/2 transform -translate-x-1/2 whitespace-nowrap tooltip-arrow">
                                {{ $asset['unit'] }}
                            </span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($shares as $index => $share)
                    <tr class="text-center border">
                        <td class="p-2 border">{{ $share['relation'] }}</td>
                        <td class="p-2 border">{{ $share['name'] ?? 'N/A' }}</td>
                        <td class="p-2 border">{{ number_format($share['share_fraction'] * 100, 2) }}</td>
                        @foreach ($assets as $assetKey => $asset)
                            <td class="p-2 border">
                                {{ number_format($asset['shares'][$index]['amount'], 2) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Add contact information section -->
        <div class="text-start my-6">
            <p class="text-sm text-gray-600 mb-4">
                সার্বিক সহযোগিতায়<br>
                <span class="font-semibold">অ্যাডভোকেট চৌধুরী তানবীর আহমেদ ছিদ্দিক</span><br>
                মোবাইলঃ 01882-689299 | ই-মেইলঃ tanbiradvocate@gmail.com
            </p>
        </div>

        <div class="text-center my-6">
            <a href="{{ route('calculator', ['edit' => 1]) }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg">পূর্বের
                পাতা</a>
            <a href="" class="px-6 py-2 bg-blue-500 text-white rounded-lg">ডাউনলোড</a>
            <a href="" class="px-6 py-2 bg-red-500 text-white rounded-lg">প্রিন্ট করুন</a>
        </div>
    </div>

    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shares = @json($shares);
            const assets = @json($assets);
            const totalEstate = @json($totalEstate);

            // Generate dynamic colors
            const generateColors = (count) => {
                const colors = [];
                const hueStep = 360 / count;
                for (let i = 0; i < count; i++) {
                    colors.push(`hsl(${hueStep * i}, 70%, 50%)`);
                }
                return colors;
            };

            // Prepare chart data
            const chartData = {
                labels: shares.map(share => `${share.relation} (${(share.share_fraction * 100).toFixed(2)}%)`),
                datasets: [{
                    data: shares.map(share => share.share_fraction),
                    backgroundColor: generateColors(shares.length),
                    borderWidth: 2
                }]
            };

            // Create pie chart
            const ctx = document.getElementById('sharePieChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 20,
                                padding: 15,
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const percentage = (value * 100).toFixed(2) + '%';
                                    const amount = (totalEstate * value).toLocaleString('en-BD', {
                                        style: 'currency',
                                        currency: 'BDT',
                                        maximumFractionDigits: 2
                                    });
                                    return `${label}: ${percentage} (${amount})`;
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: `মোট সম্পত্তির পরিমাণ: ${totalEstate.toLocaleString()} টাকা`,
                            font: {
                                size: 16
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });
    </script>
    <style>
        .tooltip-arrow::after {
            content: " ";
            position: absolute;
            bottom: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent #1F2937 transparent;
        }
    </style>
@endsection
