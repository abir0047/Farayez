@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 max-w-4xl text-center">
        <h2 class="text-lg sm:text-xl font-bold text-blue-900">
            মুসলিম উত্তরাধিকারের আইন অনুযায়ী সম্পত্তি বন্টন
        </h2>

        <div class="my-4">
            <button class="px-4 py-2 sm:px-6 sm:py-2 bg-red-600 text-white rounded-lg w-full sm:w-auto">
                ফলাফল
            </button>
        </div>

        <h3 class="text-lg font-semibold text-red-700">
            @if (!empty($deceasedInfo['name']))
                {{ $deceasedInfo['name'] }}-এর সম্পত্তির বন্টন
            @else
                মৃত ব্যক্তির সম্পত্তির বন্টন
            @endif
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6 p-4 border rounded-lg bg-gray-100 text-sm sm:text-base">
            @foreach ($assets as $asset)
                <div>{{ $asset['name'] }}: {{ number_format($asset['value']) }} {{ $asset['unit'] }}</div>
            @endforeach
        </div>

        <!-- Pie Chart Section -->
        <div class="my-8">
            <h3 class="text-md sm:text-lg font-semibold mb-4">শেয়ার অনুযায়ী সম্পত্তির বন্টন চার্ট</h3>
            <div class="chart-container relative h-[250px] sm:h-[400px] w-full">
                <canvas id="sharePieChart"></canvas>
            </div>
        </div>

        <!-- Responsive Table Wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 my-6 text-sm sm:text-base">
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
        </div>

        <!-- Contact Info -->
        <div class="text-start my-6 text-sm text-gray-600">
            <p class="mb-4">
                সার্বিক সহযোগিতায়<br>
                <span class="font-semibold">অ্যাডভোকেট চৌধুরী তানবীর আহমেদ ছিদ্দিক</span><br>
                মোবাইলঃ 01882-689299 | ই-মেইলঃ tanbiradvocate@gmail.com
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-2 my-6">
            <a href="{{ route('calculator', ['edit' => 1]) }}"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg w-full sm:w-auto">
                পূর্বের পাতা
            </a>
            <a href="" class="px-4 py-2 bg-blue-500 text-white rounded-lg w-full sm:w-auto">
                ডাউনলোড
            </a>
            <a href="" class="px-4 py-2 bg-red-500 text-white rounded-lg w-full sm:w-auto">
                প্রিন্ট করুন
            </a>
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

            // Prepare chart data with fallback to relation if name is null
            const chartData = {
                labels: shares.map(share => {
                    const displayName = share.name || share.relation;
                    return `${displayName} (${(share.share_fraction * 100).toFixed(2)}%)`;
                }),
                datasets: [{
                    data: shares.map(share => share.share_fraction),
                    backgroundColor: generateColors(shares.length),
                    borderWidth: 2,
                    hoverOffset: 15, // This makes slices "pop out" on hover
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#ffffff'
                }]
            };

            // Create pie chart
            const ctx = document.getElementById('sharePieChart').getContext('2d');
            const pieChart = new Chart(ctx, {
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
                                },
                                // Show relation if name is null in legend
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => {
                                        const share = shares[i];
                                        const displayName = share.name || share.relation;
                                        return {
                                            text: displayName,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const share = shares[context.dataIndex];
                                    const displayName = share.name || share.relation;
                                    const percentage = (context.parsed * 100).toFixed(2) + '%';
                                    const amount = (totalEstate * context.parsed).toLocaleString(
                                        'en-BD') + ' টাকা';
                                    return [
                                        displayName,
                                        `শেয়ার: ${percentage}`,
                                        // `পরিমাণ: ${amount}`
                                    ];
                                }
                            }
                        },
                        title: {
                            display: true,
                            // text: `মোট সম্পত্তির পরিমাণ: ${totalEstate.toLocaleString()} টাকা`,
                            font: {
                                size: 16
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    // Enhanced hover effects
                    onHover: (event, chartElement) => {
                        if (chartElement.length) {
                            const index = chartElement[0].index;
                            pieChart.setActiveElements([{
                                datasetIndex: 0,
                                index
                            }]);
                            pieChart.update();
                        }
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
