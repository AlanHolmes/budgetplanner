@extends('layouts.app')

@section('heading')
  My Budgets
@endsection

@section('body')

  <div class="container">


    @foreach ($budgets as $budget)

      <div class="card mb-4">
        <div class="card-body">

          <div class="float-right">
            <a href="/budgets/{{ $budget->id }}/edit" role="button" class="btn btn-outline-dark btn-sm">Edit</a>
          </div>

          <h4 class="card-title">{{ $budget->name }}</h4>
          <h6 class="card-subtitle mb-2 text-muted">{{ $budget->description }}</h6>


          <div class="row  align-items-center">
            <div class="col col-2">
              &pound;0
            </div>

            <div class="col">
              <div class="progress">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: 10%" aria-valuenow="10"
                     aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>
            </div>

            <div class="col col-2 text-right">
             &pound;{{ $budget->budget_as_float }}
            </div>
          </div>



        </div>
      </div>

    @endforeach

  </div>

@endsection
