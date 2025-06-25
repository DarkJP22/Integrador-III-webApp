<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\ResetCode;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LoginUserController extends Controller
{
    use ValidatesRequests;

    public function login()
    {
        return view('auth.users.login');
    }

    public function validateRequest(Request $request)
    {
         $this->validate($request, [
            'email' => 'required'
        ]);

        return $this;

    }

    public function postLogin(Request $request)
    {
      $v = $this->validateRequest($request);
        $emailOrIde = 'email';

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',

        ]);

        if ($validator->fails()) {
            $emailOrIde = 'ide';

            $validatorIde = Validator::make($request->all(), [
                'email' => 'required|exists:users,ide',

            ]);

            if ($validatorIde->fails()) {
                throw ValidationException::withMessages([
                    'email' => ['Usuario no existe'],
                ]);
            }
        } else {

            $validatorEmail = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',

            ]);
            if ($validatorEmail->fails()) {
                throw ValidationException::withMessages([
                    'email' => ['Usuario no existe'],
                ]);
            }
        }

        if (Auth::attempt([ $emailOrIde => $request->email, 'password' => $request->password, 'active' => 1] ,$request->has('remember'))) {
            
            //$user = \Auth::user();

           // if ($user->hasRole('paciente')) {
                // Authentication passed...

                return redirect()->intended('/');
           // }

           /* throw ValidationException::withMessages([
                'phone' => [trans('auth.failed')],
            ]);*/
            
           // \Auth::logout();
            
           
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);

        
       
        return back();


    }

    public function resetPassword()
    {
        return view('auth.passwords.phone');

    }

    public function sendResetCodePhone(Request $request)
    {
        $this->validatePhone($request);

        $user = User::byPhone($request->phone_number, $request->phone_country_code);

        $code = ResetCode::generateFor($user);

        $code->send();

        flash('Se ha enviado un codigo al teléfono para poder utilizarlo en el cambio de contraseña!','success');

        return redirect('/user/password/reset/code');

    }

    public function resetCode()
    {
        

        return view('auth.passwords.code');

    }

    public function newPassword(Request $request)
    {
         $this->validate($request, [
             'phone_country_code' => 'required',
             'phone_number' => 'required|digits_between:8,15|exists:users',
             'password' =>'required|confirmed',
             'code' => 'required|exists:reset_codes'

        
             ]);

          $code = ResetCode::where('code',$request->code)->where('created_at','>', Carbon::now()->subHours(2))->first();

          if (!$code) {

              throw ValidationException::withMessages([
                'code' => ['Codigo no existe o ha expirado'],
            ]);

            return back();

          }
          
          $user = $code->user;

          $user->password = bcrypt($request->password);
          $user->save();


          Auth::login($user);

          \DB::table('reset_codes')
                ->where('phone', $user->fullPhone)
                ->delete();

         //$code->delete();

         



        return redirect('/');


    }

     protected function validatePhone(Request $request)
    {
        $this->validate($request, ['phone_country_code' => 'required','phone_number' => 'required|digits_between:8,15|exists:users']);
    }
}
