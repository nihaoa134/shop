<?php
namespace App\Http\Middleware;


use Closure;

class CheckLogin
{
    public function handle($request,Closure $next){
        if (!$request->session()->get('u_token')){
            header("Refresh:3;url=/login");
            echo '请先登录';
            exit;
        }
        return $next($request);
    }
}