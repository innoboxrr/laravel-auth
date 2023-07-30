<?php

namespace Innoboxrr\LaravelAuth\Http\Requests\Socialite;

use Illuminate\Foundation\Http\FormRequest;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class CallbackRequest extends FormRequest
{

    public static $customLoginCallback;

    public static $customRegisterCallback;

    public function authorize()
    {

        return true;

    }

    public function rules()
    {
    
        return [];
    
    }

    public function handle()
    {

        try {
     
            $providerUser = Socialite::driver($this->provider)->stateless()->user();

            $userModel = app(config('laravel-auth.user-class'));
        
            $finduser = $userModel::where('email', $providerUser->getEmail())->first(); 
      
            if($finduser){

                if (static::$customLoginCallback) {
        
                    return call_user_func(static::$customLoginCallback, $finduser);
        
                }

                Auth::login($finduser);

                return $this->wantsJson()
                    ? response()->json(['success' => true])
                    : redirect(config('laravel-auth.routes.redirects.socialite-callback'));

            }else{

                if (static::$customRegisterCallback) {
        
                    return call_user_func(static::$customRegisterCallback, $providerUser);
        
                }

                $newUser = $userModel::create([
                    'name' => $providerUser->name,
                    'email' => $providerUser->email,
                    'password' => bcrypt(substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 8)), 0, 8))
                ]);
     
                Auth::login($newUser);

                return $this->wantsJson()
                    ? response()->json(['success' => true])
                    : redirect(config('laravel-auth.routes.redirects.socialite-callback'));
                    
            }
     
        } catch (\Exception $e) {
            
            abort(500);

        }

    }   
    
}
