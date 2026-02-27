<?php

namespace App\Http\Middleware;

use App\Support\AdminUnlock;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminUnlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if (AdminUnlock::isUnlocked()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403);
        }

        return redirect()->guest(route('admin.unlock'));
    }
}
