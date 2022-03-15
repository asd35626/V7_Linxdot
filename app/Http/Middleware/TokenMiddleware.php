<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\UserProcessTicket;
use Response;
use Cookie;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tokenId = $request->header('Authorization') ? $request->header('Authorization') : '';

        if($tokenId == '') {
            $tokenId = Cookie::get('authToken');
        }

        if($tokenId == '') {
            $responseBody['message'] = 'missing tokenID';
            return Response::json($responseBody, 401);

        }

        $count = UserProcessTicket::where('ProcessTicketId', $tokenId)
          ->where('IfSuccess', 1)
          ->where('IfLogout', 0)
			     ->where('ExpireDate', '>=', Carbon::now())
            ->count();

        if ($count != 1) {
            $responseBody['message'] = 'Token is not corrent.';

            return Response::json($responseBody, 401);
        }

        return $next($request);
    }
}
