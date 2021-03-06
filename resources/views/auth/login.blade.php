@extends('layouts.master')

@section('content')
  <div class="container">
    <div class="row full-height align-items-center justify-content-center">
      <div class="col col-md-8 col-lg-6">
        <form action="/login" method="POST" class="card">
          {{ csrf_field() }}
          <h1 class="card-header text-center h6">
            Login to your account
          </h1>

          <div class="card-body">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">@icon('envelope', 'zondicon-size-1')</span>
                <input name="email" type="email" class="form-control" placeholder="Email Address">
              </div>
            </div>

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">@icon('lock-closed', 'zondicon-size-1')</span>
                <input name="password" type="password" class="form-control" placeholder="Password">
              </div>
            </div>

            <div class="form-group">
              <button class="btn btn-primary btn-block">Log In</button>
            </div>
            @if($errors->any())
              <div class="alert alert-danger">
                These credentials do not match our records.
              </div>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
