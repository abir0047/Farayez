<?php

namespace App\Http\Controllers;

use App\Services\InheritanceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalculateDistributionController extends Controller
{
    public function calculate(Request $request)
    {
        // Ensure request is interpreted as JSON
        $data = $request->json()->all();

        if (!isset($data['deceasedInfo'])) {
            return response()->json(['error' => 'Missing deceasedInfo in request'], 400);
        }

        $calculator = new InheritanceCalculator($data);
        $results = $calculator->calculate();

        // Store results in session
        session([
            'results' => $results['shares'],
            'totalEstate' => $results['total_estate'],
            'assets' => $results['assets'],
            'calculator_data' => $data
        ]);

        return response()->json([
            'redirect_url' => route('inheritance.results')
        ]);
    }


    private function performCalculation($data)
    {
        // Add your calculation logic here
        return [
            'shares' => [],
            'distribution' => []
        ];
    }
}
