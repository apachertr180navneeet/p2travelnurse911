<?php

namespace App\Http\Middleware;
use DB;
use Closure;

class ApiAuthenticate
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        // Check if the token is valid (you'll implement this logic)
        if ($this->isValidToken($token)) {
            return $next($request);
        }
        
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    private function isValidToken($token)
    {
        
        return true;
        
        ## Convert the token
        $object = json_decode(base64_decode(base64_decode($token)));
        
        
        if(!isset($object->id)) {
            return false;
        }

        $user_id = $object->id;
        $login_token = $object->token;

        # Check the token in DB
        $token_row = DB::table('login_tokens')->select('*')->where(['user_id' => $user_id, 'token'=> $login_token])->first();

        if(empty($token_row)){
            return false;
        }
        
        
        # Check user status and delete status
        $row = DB::table('users')->select('status', 'deleted_at')->where(['id' => $user_id])->first();
        if(!empty($row)){
            if(!is_null($row->deleted_at))
                return false;
            else if($row->status == 2)
                return false;
        }
        else
            return false;
    
        
        return true;
    }
}
