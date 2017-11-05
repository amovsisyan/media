<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AdminLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => 'logout']);
    }

    // Need to be overwritten, cause in my app redirect from login goes to '/', not to '/home'
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        $redirectTo = property_exists($this, 'redirectTo') ? $this->redirectTo : '/';

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($redirectTo);
    }

    public function login(Request $request)
    {
        // Validate
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // Loging in
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            return redirect(route('adminDashboard', ['locale' => \App::getLocale()]));
        }

        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function showLoginForm()
    {
        return view('auth.admin-login');
    }
}
