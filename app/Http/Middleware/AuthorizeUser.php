<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AuthorizeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $roles  Daftar role yang diizinkan (dipisahkan oleh koma)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        $user_role = $request->user()->getRole();
            $allowed_roles = array_map('trim', explode(',', $roles));
            if (in_array($user_role, $allowed_roles)) {
                return $next($request);
            }
        abort(403, "Forbidden. Kamu tidak punya akses ke halaman ini");
    }
}
