@extends('layouts.app')

@section('content')
    <div class="row">
        <form class="form-horizontal col s8 push-s2" role="form" method="POST" action="{{ url('/register') }}">
            {{ csrf_field() }}
            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">child_care</i>
                    <input id="name" type="text" name="name" class="validate  {{ $errors->has('name') ? ' invalid' : '' }}" value="{{ old('name') }}" required autofocus>
                    <label for="name" data-error="{{ $errors->has('name') ? $errors->first('name') : 'wrong' }}">Username</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">email</i>
                    <input id="email" type="email" name="email" class="validate  {{ $errors->has('email') ? ' invalid' : '' }}" value="{{ old('email') }}" required>
                    <label for="email" data-error="{{ $errors->has('email') ? $errors->first('email') : 'wrong' }}">Email</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">security</i>
                    <input id="password" type="password" name="password" class="validate {{ $errors->has('password') ? ' invalid' : '' }}" required>
                    <label for="password" data-error="{{ $errors->has('password') ? $errors->first('password') : 'wrong' }}">Password</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">security</i>
                    <input id="password-confirm" type="password" name="password_confirmation" class="validate {{ $errors->has('password_confirmation') ? ' invalid' : '' }}" required>
                    <label for="password-confirm" data-error="{{ $errors->has('password_confirmation') ? $errors->first('password_confirmation') : 'wrong' }}">Confirm Password</label>
                </div>
            </div>
            <div class="col s12">
                <button class="btn waves-effect waves-light" type="submit" name="action">Register
                    <i class="material-icons right">send</i>
                </button>
            </div>
        </form>
    </div>
@endsection
