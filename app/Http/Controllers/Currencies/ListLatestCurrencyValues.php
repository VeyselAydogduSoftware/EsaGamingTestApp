<?php
declare(strict_types=1);
namespace App\Http\Controllers\Currencies;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\CurrencyValue;
use Illuminate\Support\Collection;
class ListLatestCurrencyValues extends Controller
{
    public function __invoke(Request $request, string $currencyCode): JsonResponse
    {
        try{

            $currencyDetails = Currency::select(['id', 'long_name', 'currencyCode', 'symbol'])->where('currencyCode', $currencyCode)->first();
            $values = CurrencyValue::select(['logged_at', 'currency_value'])->where('currency_id', $currencyDetails->id)->get();

            $currencyValues = $values->map(static function($value){

                return [

                    'logged_date' => $value['logged_at'],
                    'value' => $value['currency_value']*100

                ];

            });

            return response()->json([

                'data' => [
                    'currency-details' => $currencyDetails,
                    'values' => $currencyValues
                ]

            ], 200);

        }catch (\Exception $e){

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

            //Projeye göre hata bilgisi karşıya dönmeyebilir bunun yerine log tutularak zaman damgası dönülür ve log dosyasından hata bilgisi okunur.

        }

    }
}
