@extends('layouts.login')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-6 d-none d-lg-block p-0">
                        <img src="{{ asset('assets/img/login-img.webp') }}" alt="Shabat Print Image"
                            class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Portal Karyawan Login</h1>
                            </div>
                            @if ($errors->has('login'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('login') }}
                                </div>
                            @endif
                            <form class="user" method="POST" action="/login">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user"
                                        name="username" value="{{ old('username') }}" required autofocus
                                        placeholder="Enter Username">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" name="password" placeholder="Password">
                                </div>
                                <button class="btn btn-primary btn-user btn-block" type="submit">
                                    Login
                                </button>
                                <hr>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection