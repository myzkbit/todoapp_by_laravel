<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class RedirectToHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isSSL() && app()->isProduction()) {
            return redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        }

        return $next($request);
    }

    //Webサーバー毎にキーと値で判別
    public function isSSL()
    {
        if (isset($_SERVER['HTTPS'])) { // Apache
            return ($_SERVER['HTTPS'] === 'on' or $_SERVER['HTTPS'] === '1');
        }
        elseif (isset($_SERVER['SSL'])) { // IIS
            return ($_SERVER['SSL'] === 'on');
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) { // Reverse proxy
            return (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_PORT'])) { // Reverse proxy
            return ($_SERVER['HTTP_X_FORWARDED_PORT'] === '443');
        }
        elseif (isset($_SERVER['SERVER_PORT'])) {
            return ($_SERVER['SERVER_PORT'] === '443');
        }
        return false;
    }
}
