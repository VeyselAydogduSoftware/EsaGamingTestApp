<?php

declare(strict_types=1);

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\CurrencyValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/* Veysel Aydoğdu
 * NOT : Projenin göre catch bloğuna düşen hata bilgisi karşıya dönmeyebilir bunun yerine log tutularak zaman damgası dönülür ve log dosyasından hata bilgisi okunur.
 */

class CurrenciesController extends Controller
{
    protected $ActiveUser;

    public function __construct(){

        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);

        $this->middleware(function ($request, $next) {

            $this->ActiveUser    = Auth::user();
            return $next($request);

        });

    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
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

        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {

        try{

            $request->validate([

                'longName'      => 'required|string|max:255',
                'currencyCode'  => 'required|string|max:3|unique:currencies,currency_code',
                'symbol'        => 'required|string|max:3',

            ]);

            $currency = Currency::create([

                'long_name'     =>  $request->longName,
                'currency_code' =>  $request->currencyCode,
                'symbol'        =>  $request->symbol,
                'created_by'    =>  $this->ActiveUser->id,

            ]);

            if(!$currency){

                throw new \Exception('Yeni para birimi oluşturulamadı');

            }

            return response()->json([

                'message' => 'Yeni para birimi oluşturuldu',

            ], 200);

        }catch (\Exception $e){

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $currencyCode): JsonResponse
    {
        try{

            $currencyDetails = Currency::with('currencyValues')
                ->select(['id', 'long_name', 'currency_code', 'symbol'])
                ->where('currency_code', '=', $currencyCode)
                ->first();

            $currencyValues = $currencyDetails->currencyValues->map(static function($value){

                return [

                    'id'            => $value['id'],
                    'logged_date'   => $value['logged_at'],
                    'value'         => $value['currency_value']*100

                ];

            });

            return response()->json([

                'data' => [
                    'currency-details'  => $currencyDetails,
                    'values'            => $currencyValues
                ]

            ], 200);

        }catch (\Exception $e){

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $currencyCode): JsonResponse
    {

        try {

            $request->validate([

                'longName'      => 'required|string|max:255',
                'symbol'        => 'required|string|max:3',

            ]);

            //Güvenlik ve oluşabilecek çeşitli buglar açısından currency_code güncellenmesi engellendi.

            $currencyInfo  = Currency::where('currency_code', $currencyCode)->first();

            if(!$currencyInfo){

                throw new \Exception('Para birimi bulunamadı');

            }

            $currency = Currency::where('currency_code', $currencyCode)->update([

                'long_name'     =>  $request->longName,
                'symbol'        =>  $request->symbol,
                'updated_by'    =>  $this->ActiveUser->id,

            ]);

            if(!$currency){

                throw new \Exception('Para birimi güncellenemedi');

            }

            return response()->json([

                'message' => 'Para birimi güncellendi',

            ], 200);

        }catch (\Exception $e){

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $currencyCode): JsonResponse
    {

        try{

            $currencyInfo       = Currency::where('currency_code', '=', $currencyCode)->first();

            $currencyDestroy    = Currency::where('currency_code', '=', $currencyCode)->delete();

            if(!$currencyDestroy){

                throw new \Exception('Currency not deleted');

            }

            try {

                $values = CurrencyValue::where('currency_id', $currencyInfo->id)->delete();

            } catch (\Exception $e) {

                return response()->json([

                    'message' => 'Para birimi silindi fakat bağlantılı veriler silinemedi.'

                ], 200);
            }

            return response()->json([

                'message' => 'Para birimi ve bağlantılı değeleri silindi',

            ], 200);

            //SoftDelete yöntemide kullanılabilir.

        }catch (\Exception $e){

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

        }


    }
}
