<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
        Fortify::registerView(function () {
            return view('auth.register');
        });
        Fortify::loginView(function () {
            return view('auth.login');
        });

        $this->app->singleton(LoginRequest::class, function ($app) {
            $request = $app->make(Request::class);
            if ($request->routeIs('login') && $request->isMethod('post')) {
                $request->validate([
                    'email' => 'required',
                    'password' => 'required',
                ], [
                    'email.required' => 'メールアドレスを入力してください',
                    'password.required' => 'パスワードを入力してください',
                ]);
            }

            return LoginRequest::createFrom($request);
        });

        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse
        {
            public function toResponse($request)
            {
                return redirect('/admin');
            }
        });

        $this->app->instance(LoginResponse::class, new class implements LoginResponse
        {
            public function toResponse($request)
            {
                return redirect('/admin');
            }
        });
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません'],
            ]);
        });

    }
}
