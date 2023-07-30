<?php
declare(strict_types=1);
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    public function register(Request $request): JsonResponse
    {

        try{

            $request->validate([
                'name'      => 'required|string',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|string|min:6|max:20|confirmed',

            ]);

            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'email_verified_at' => now(),

            ]);

            if(!$user){

                throw new \Exception('User not created');

            }

            return response()->json([

                'message' => 'User created successfully',
                //ihtiyaca göre token oluşturularak döndürülebilir,
                //token oluşturulması için laravel sanctum kullanılabilir.
                //email onay kısmı bilerek atlandı.

            ], 200);


        }catch (\Exception $e){

            return response()->json([

                'error' => $e->getMessage()

            ], 500);

        }

    }


}
