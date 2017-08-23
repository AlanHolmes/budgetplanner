@extends('layouts.app')

@section('heading')
  Create a new Budget
@endsection

@section('body')

  @include('budgets.partials.form', [
    'formAction' => '/budgets',
    'buttonText' => 'Create',
  ])

@endsection
