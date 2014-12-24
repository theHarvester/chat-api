<?php

class AuthController extends BaseController
{

    protected $layout = "layout";

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    public function getRegister()
    {
        return View::make('auth.register');
    }

    public function getLogin()
    {
        return View::make('auth.login');
    }

    public function doRegister()
    {

        $validator = Validator::make(Input::all(), User::$rules);

        if ($validator->passes()) {
            $user = new User;
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            $user->save();

            return Redirect::to('account/login')->with('message', 'Thanks for registering!');
        } else {
            return Redirect::to('account/register')->with('message', 'The following errors occurred')->withErrors($validator)->withInput();
        }
    }

    public function doLogin()
    {
        if (Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')))) {
            return Redirect::route('home')->with('message', 'You successfully signed in');
        } else {
            return Redirect::to('account/login')
                ->with('message', 'Your email/password combination was incorrect')
                ->withInput();
        }
    }

    public function doLogout()
    {
        Auth::logout();
        return Redirect::to('/')->with('message', 'You have signed out');
    }

}
