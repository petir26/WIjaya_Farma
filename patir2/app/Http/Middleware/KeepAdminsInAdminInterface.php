<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KeepAdminsInAdminInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $allowedRoutes = ['logout', 'profil', 'profil.update'];

        if ($request->user()?->role === 'admin'
            && !in_array($routeName, $allowedRoutes, true)
            && !str_starts_with((string) $routeName, 'admin.')) {
            return redirect()->route('admin.toko');
        }

        return $next($request);
    }
}
