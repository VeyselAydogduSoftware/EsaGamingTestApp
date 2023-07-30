<?php

namespace App\Http\Controllers\Calculator;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\CurrencyValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{

    public function index(Request $request): JsonResponse
    {

        try {

            $request->validate([

                'amount'                =>  'required',
                'sourceCurrencyCode'    =>  'required|exists:currencies,currency_code',
                'targetCurrencyCode'    =>  'required|exists:currencies,currency_code',

            ]);

            $sourceCurrencyId   =   Currency::where('currency_code', '=', $request->sourceCurrencyCode)->first()->id;
            $targetCurrencyId   =   Currency::where('currency_code', '=', $request->targetCurrencyCode)->first()->id;

            $source = CurrencyValue::where('currency_id', '=', $sourceCurrencyId)->orderBy('logged_at', 'desc')->limit(1)->first()->currency_value;

            $target = CurrencyValue::where('currency_id', '=', $targetCurrencyId)->orderBy('logged_at', 'desc')->limit(1)->first()->currency_value;

            if (!$source || !$target) {

                throw new \Exception('En az bir para birimine ait değer bulunamadı');

            }

            return response()->json([

                $request->amount * ($target / $source)

            ], 200);

        }catch (\Exception $e) {

            return response()->json([

                'message' => $e->getMessage(),

            ], 500);

        }

    }

}
