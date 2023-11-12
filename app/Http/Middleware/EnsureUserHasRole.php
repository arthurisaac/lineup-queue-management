<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param string $role
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->user()->roles()->where('name', $role)->exists()) return $next($request);

        abort(403);
    }
}