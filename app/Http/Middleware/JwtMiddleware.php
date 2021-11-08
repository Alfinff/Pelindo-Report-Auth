<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\User;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            if ($request->header('Authorization')) {
                $decode = parseJwt($request->header('Authorization'));

                $user = User::find($decode->user->id);

                if($user) {
                    // if ($decode->key == $user->key) {
                        return $next($request);
                    // }
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthorize',
                'code'    => 401,
            ]);
        } catch (\Throwable $th) {
            return writeLog($decode->original->message ?? 'Expired Token.');
        }
    }
}
