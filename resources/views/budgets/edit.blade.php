@extends('layouts.app')

@section('heading')
  Edit Budget
@endsection

@section('body')

  @include('budgets.partials.form', [
    'formAction' => "/budgets/{$budget->id}",
    'buttonText' => 'Update',
    'methodField' => 'PATCH',
  ])

@endsection
