<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RoleOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastiin user sudah login
        if (!Auth::check()) {
            abort(403, 'Akses Ditolak: Silakan login terlebih dahulu.');
        }

        // Cek role secara real-time ke tabel Supabase (public.users)
        // Ini memastikan jika Owner menurunkan jabatan seseorang, saat itu juga aksesnya terputus.
        $userRole = DB::table('users')->where('id', Auth::id())->value('role');

        if ($userRole !== 'owner') {
            abort(403, 'Akses Ditolak: Hanya Owner yang diizinkan mengakses halaman ini.');
        }

        return $next($request);
    }
}