<?php

namespace App\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index() {
        return view('admin.login');
    }

    public function authentificate(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'

        ]);



        if ($validator->passes()) {
            //dd(Auth::guard('admin'));
            //dd($request->email);
           // dd($request->password);
            //dd(Auth::guard('admin')->attempt(['email' => $request->email,
           // 'password' => $request->password],$request->get('remember')));

            if(Auth::guard('admin')->attempt(['email' => $request->email,
            'password' => $request->password],$request->get('remember'))){
                //dd($request->email);

             $admin = Auth::guard('admin')->user();




             if ($admin->role == 2) {
              //  dd('Authentification réussie en tant qu\'administrateur de niveau 2');

                return redirect()->route('admin.dashboard');
             } else {
                Auth::guard('admin')->logout();
                //dd('test');

                return redirect()->route('admin.login')->with('error','Vous n êtes pas autorisé');

            }

        } else {
            //dd('echouer');

            return redirect()->route('admin.login')->with('error','id ou mo incorrect');

        }


        } else {
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));

        }


    }

    }







