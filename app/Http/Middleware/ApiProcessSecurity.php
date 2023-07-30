<?php

declare(strict_types=1);
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiProcessSecurity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $apiSecurityKeyHeader   = $request->header('apiSecurityKey');

        $expectedApiSecurityKey = env('API_SECURITY_KEY');

        if ($apiSecurityKeyHeader === $expectedApiSecurityKey) {

            return $next($request);

        }

        return response()->json(['message' => 'Geçersiz API Güvenlik Anahtarı'], 401);

    }
}
