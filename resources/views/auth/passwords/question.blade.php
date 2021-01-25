@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">

                    @include('layouts.flash-messages')
                    
                    <form method="POST" action="{{ route('password.question.reset') }}" aria-label="{{ __('Reset Password') }}">
                        @csrf

                        <input type="hidden" name="email" value="{{$email}}">

                        <div class="form-group row">
                            <label for="security-question" class="col-md-4 control-label text-md-right">Security Question</label>

                            <div class="col-md-6">
                                {!! Form::select('security-question',$questions,null,['class'=>'form-control','name'=>'security_question_id','required']) !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="security-answer" class="col-md-4 control-label text-md-right">Security Answer</label>

                            <div class="col-md-6">
                                <input id="security-answer" class="form-control" name="security_answer" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
