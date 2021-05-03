<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\EmailService;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->input('email'))->first();

            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                return redirect(route('login'))->withErrors([
                    'login' => 'Email or password is incorrect!'
                ])->withInput();
            }

            if ($user->status !== '1') {
                return $this->returnError('user.unactivated');
            }

            Auth::login($user);

            return redirect('/dashboard');
        }

        return view('auth/login');
    }

    public function register(Request $request)
    {

        if ($request->isMethod('post')) {
            try {
                $rules = [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => 'required'
                ];

                $validator = Validator::make($request->all(), $rules);

                if (!$validator->passes()) {
                    return $this->returnBadRequest('Please fill all required fields');
                }

                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->status = strtoupper(Str::random(6));
                $user->role_id = Role::ROLE_USER;

                $user->save();


                $emailService = new EmailService();

                if ($emailService->sendVerifyAccount($user)) ;
                $user->save();

                return view('/verify');
            } catch (\Exception $e) {
                return $this->returnError($e->getMessage());
            }

        }

        return view('auth/register');
    }


    /**
     * Verify account
     * @param Request $request
     * @param User $userModel
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, User $userModel)
    {
        if ($request->isMethod('post')) {
            try {
                $rules = [
                    'code' => 'required|min:6|max:6'
                ];

                $messages = [
                    'code.required' => 'code',
                    'code.min' => 'code.min',
                    'code.max' => 'code.max'
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if (!$validator->passes())
                    return $this->returnError($validator->errors()->first());


                $user = $userModel::where('status', $request->code)->get()->first();

                if (!$user)
                    return $this->returnNotFound('user.404');

                if ($user->status == User::STATUS_ACTIVE)
                    return $this->returnError('user.activated');

                if ($user->status != $request->code)
                    return $this->returnError('code.min');

                $user->status = '1';

                $user->save();

                return view('auth/login');

            } catch (\Exception $e) {
                return $this->returnError($e->getMessage());
            }
        }
        return view('auth/verify');
    }

    /**
     * Forgot password
     * @param Request $request
     * @param User $userModel
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request, User $userModel)
    {
        if ($request->isMethod('post')) {
            try {
                $rules = [
                    'email' => 'required|email|exists:users'
                ];

                $messages = [
                    'email.required' => 'email',
                    'email.email' => 'email.email',
                    'email.exists' => 'email.exists',
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if (!$validator->passes()) {
                    return $this->returnError($validator->errors()->first());
                }

                $user = $userModel::where('email', $request->email)->first();

                $user->forgot_code = strtoupper(Str::random(6));
                $user->save();

                $emailService = new EmailService();
                $emailService->sendForgotPassword($user);

                return view('auth.changePassword');
            } catch (\Exception $e) {
                return $this->returnError($e->getMessage());
            }
        } else

            return view('auth/forgot');
    }

    /**
     * Change user password
     * @param Request $request
     * @param User $userModel
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request, User $userModel)
    {
        if ($request->isMethod('post')) {
            try {
                $rules = [
                    'code' => 'bail|required',
                    'password' => 'bail|required|min:6',
                ];


                $messages = [
                    'code.required' => 'code',
                    'password.required' => 'password',
                    'password.min' => 'password.min',
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if (!$validator->passes()) {
                    return $this->returnError($validator->errors()->first());
                }

                $user = $userModel::where('forgot_code', $request->code)->first();

                if (!$user) {
                    return $this->returnNotFound('user.404');
                }

                if ($user->forgot_code !== $request->code) {
                    return $this->returnError('code.min');
                }

                $user->password = Hash::make($request->password);
                $user->forgot_code = '';

                $user->save();

                return $this->returnSuccess();
            } catch (\Exception $e) {
                return $this->returnError($e->getMessage());
            }
        }

        return view('auth/changePassword');
    }

}
