<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // If user is not active, logout and redirect to verification
            if (!$user->is_active) {
                $identifier = $user->email ?? $user->phone;
                
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('verify.show', ['identifier' => $identifier])
                    ->with('warning', 'حسابك غير مفعل. يرجى تفعيل حسابك أولاً. / Your account is not activated. Please activate your account first.');
            }
        }
        
        return $next($request);
    }
}
