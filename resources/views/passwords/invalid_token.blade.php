@extends('layout')
@section('title', ' | Reset Password Failed')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Reset Password') }}</div>

                    <div class="card-body">
                        <h5 class="card-title">Reset Password Failed!</h5>
                        <p class="card-text text-danger">{{ $errors->first('token') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
