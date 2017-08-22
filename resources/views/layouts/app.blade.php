@extends('layouts.master')

@section('content')
  @include('layouts.partials.nav')


  <div class="container">
    <h1>@yield('heading')</h1>
  </div>


  @yield('body')

@endsection
