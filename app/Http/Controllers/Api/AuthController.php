<?php

namespace App\Http\Controllers\Api;

use App\User;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'hotel_id' => 'required',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = 0;
        $user->password = bcrypt($request->password);
        $user->hotel_id = $request->hotel_id;
        $user->save();

//        $validatedData['password'] = bcrypt($request->password);
//        $validatedData['status'] = 0;
//        $validatedData['hotel_id'] = $request->hotel_id;

//        return $validatedData;

//        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken]);

        /*        $http = new GuzzleClient;
                $response = $http->post(url('oauth/token'), [
                    'form_params' => [
                        'grant_type' => 'password',
                        'client_id' => env('OAUTH_GRANT_SECRET_ID'),
                        'client_secret' => env('OAUTH_GRANT_SECRET_KEY'),
                        'username' => $request->email,
                        'password' => $request->password,
                        'scope' => '',
                    ],
                ]);
                return response(['auth' => json_decode((string)$response->getBody(), true)]);*/
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        /*$user = User::where('email', $request->email)->first();

        if (!$user) {
            return response(['status' => 'error', 'message' => 'User not found']);
        }*/

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response(['user' => auth()->user(), 'access_token' => $accessToken]);

        /*if (Hash::check($request->password, $user->password)) {
            $http = new GuzzleClient;
            $response = $http->post(url('oauth/token'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('OAUTH_GRANT_SECRET_ID'),
                    'client_secret' => env('OAUTH_GRANT_SECRET_KEY'),
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ],
            ]);
            return response(['auth' => json_decode((string)$response->getBody(), true)]);
        }*/
    }


    /*public function refreshToken()
    {
        $http = new GuzzleClient;
        $response = $http->post(url('oauth/token'), [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => request('refresh_token'),
                'client_id' => env('OAUTH_GRANT_SECRET_ID'),
                'client_secret' => env('OAUTH_GRANT_SECRET_KEY'),
                'scope' => '',
            ],
        ]);
        return json_decode((string)$response->getBody(), true);
    }*/
}