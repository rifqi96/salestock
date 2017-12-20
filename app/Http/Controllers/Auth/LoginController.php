<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Overrides validateLogin from AuthenticateUsers trait
     *
     * @param Request $request
     */
    protected function validateLogin(array $request)
    {
        return Validator::make($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Overrides sendFailedLoginResponse from AuthenticateUsers trait
     *
     * @param Request $request
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 422,
                'code' => 0,
                'data' => $errors,
            ], 422);
        }

        return response()->json([
            'status' => 400,
            'code' => 0,
            'data' => $errors,
        ], 400);
    }

    public function login(Request $request) {
        $validator = $this->validateLogin($request->all());

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'code' => 0,
                'data' => $validator->messages(),
            ], 400);
        }

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $user->generateToken();

            return response()->json([
                'status' => 200,
                'code' => 1,
                'data' => $user->toArray(),
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        if ( $user = auth()->guard('api')->user() ) {
            $user->logout();
        }

        return response()->json([
            'status' => 200,
            'code' => 1,
            'data' => 'User logged out'
        ], 200);
    }

}
