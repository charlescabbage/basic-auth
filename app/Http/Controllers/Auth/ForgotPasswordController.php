<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use App\SecurityQuestion;
use Session;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showQuestionForm(Request $request)
    {
        if (empty(Session::get('email'))) {
            if (empty(Session::get('status'))) {
                return Redirect::route('password.request');
            }
            return Redirect::route('password.request')->with(['status' => Session::get('status')]);
        }

        $q = SecurityQuestion::all();
        $questions[''] = 'Select a question';
        foreach($q as $question){
          $questions[$question->id] = $question->question;
        }

        return view('auth.passwords.question',compact('questions'))->with(
            ['email' => Session::get('email')]
        );
    }

    public function sendSecuredResetLinkEmail(Request $request)
    {
        $input = Input::all();

        $rules = [
            'email' => User::$auth_rules['email'],
            'g-recaptcha-response' => 'required|captcha'
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $account = User::where('email',$request->email)->first();
        if ($account) {
            if (!empty($account->security_question_id)) {
                return Redirect::route('password.question.request')->with(
                    ['email' => $request->email]
                );
            }
        }

        return $this->sendResetLinkEmail($request);
    }

    public function verifySecurityQuestion(Request $request)
    {
        $account = User::where('email',$request->email)->first();
        if ($account) {
            $question = SecurityQuestion::find($request->security_question_id);
            if ($question) {
                if ($account->security_question_id == $question->id && $account->security_answer == $request->security_answer) {
                    return $this->sendResetLinkEmail($request);
                } else {
                    return back()->with(
                        ['error' => 'Invalid answer!', 'email' => $request->email]
                    );
                }
            } else {
                return back()->with(
                    ['error' => 'Error', 'email' => $request->email]
                );
            }
        }

        return Redirect::route('password.request');
    }
}
