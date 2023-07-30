<?php
declare(strict_types=1);
namespace App\Http\Controllers\Currencies;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Currency;
class ListCurrencies extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {

        try{

            $currencyList = Currency::all()->toArray();

            return response()->json([

                'data' => !count($currencyList) ? 'Kayıtlı data bulunamadı' : $currencyList

            ], 200);

        }catch (\Exception $e){

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

            //Projeye göre hata bilgisi karşıya dönmeyebilir bunun yerine log tutularak zaman damgası dönülür ve log dosyasından hata bilgisi okunur.

        }

    }
}
