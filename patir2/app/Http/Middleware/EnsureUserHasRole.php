<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $actualRole = $request->user()?->role;
        $allowedRoles = $role === 'doctor' ? ['doctor', 'dokter'] : [$role];

        abort_unless(in_array($actualRole, $allowedRoles, true), 403);

        return $next($request);
    }
}
