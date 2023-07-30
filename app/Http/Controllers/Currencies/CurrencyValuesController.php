<?php

namespace App\Http\Controllers\Currencies;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\CurrencyValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyValuesController extends Controller
{

    protected $ActiveUser;

    public function __construct(){

        $this->middleware(function ($request, $next) {

            $this->ActiveUser    = Auth::user();
            return $next($request);

        });

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {

        try{

            $request->validate([

                'currencyCode'      =>  'required|exists:currencies,currency_code',
                'value'             =>  'required',

            ]);

            $currencyInfo   =   Currency::where('currency_code', '=', $request->currencyCode)->first();

            if(!$currencyInfo){

                throw new \Exception('Para birimi bulunamadı');

            }

            $currencyValue = CurrencyValue::create([

                'currency_id'       =>  $currencyInfo->id,
                'currency_value'    =>  $request->value,
                'logged_at'         =>  now(),
                'created_by'        =>  $this->ActiveUser->id

            ]);

            if(!$currencyValue){

                throw new \Exception('Kayıt eklenemedi');

            }

            return response()->json([

                'message' => 'Kayıt eklendi'

            ], 200);


        }catch (\Exception $e) {

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

        }


    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try{

            $request->validate([

                'value'     =>  'required',

            ]);

            $value = CurrencyValue::where('id', $id)->update([

                'currency_value' =>  $request->value,
                'updated_by'     =>  $this->ActiveUser->id,

            ]);

            if(!$value){

                throw new \Exception('Kayıt güncellenemedi');

            }

            return response()->json([

                'message' => 'Kayıt güncellendi'

            ], 200);


        }catch (\Exception $e) {

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

            try {

                $value = CurrencyValue::where('id', $id)->delete();

                if (!$value) {

                    throw new \Exception('Kayıt silinemedi');

                }

                return response()->json([

                    'message' => 'Kayıt silindi'

                ], 200);

            } catch (\Exception $e) {

                return response()->json([

                    'error' => $e->getMessage()

                ], 500);

            }
    }
}
