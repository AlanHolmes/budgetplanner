@extends('layouts.app')

@section('heading')
  Edit Budget
@endsection

@section('body')

  <form action="/budgets/{{ $budget->id }}" method="POST">
    {{ csrf_field() }}
    {{ method_field('PATCH') }}

    @if ($errors->any())
      <div class="container mb-4">
        <div class="alert alert-danger">
          <strong>
            There {{ $errors->count() == 1 ? 'is' : 'are' }} {{ $errors->count() }} {{ str_plural('error', $errors->count() )}} with this budget:
          </strong>
          <ul class="text-danger">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif


    <div class="container mb-4">
      <div class="row">
        <div class="col col-4">
          <h2>Budget Details</h2>

          <p class="text-muted"></p>
        </div>

        <div class="col col-8">
          <div class="card">
            <div class="card-body">

              <div class="form-group">
                <label for="name">Name</label>
                <input class="form-control{{ $errors->first('name', ' is-invalid') }}" id="name" name="name" value="{{ old('name', $budget->name) }}" placeholder="eg Household Budget">
              </div>

              <div class="form-group">
                <label for="description">Description</label>
                <input class="form-control{{ $errors->first('description', ' is-invalid') }}" id="description" name="description" value="{{ old('description', $budget->description) }}" placeholder="Budget description (optional)">
              </div>

              <div class="form-group">
                <label for="budget">Amount</label>
                <div class="input-group">
                  <span class="input-group-addon">&pound;</span>
                  <input class="form-control{{ $errors->first('budget', ' is-invalid') }}" id="budget" name="budget" value="{{ old('budget', $budget->budget_as_float) }}" placeholder="1000.00">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container mb-4">
      <div class="row mb-4">
        <div class="col col-4">
          <h2>Frequency</h2>
        </div>

        <div class="col col-8">
          <div class="card">
            <div class="card-body">

              <div class="form-group row mb-0">

                <div class="col col-sm-6">
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-light {{ old('frequency', $budget->frequency) == 'monthly' ? 'active' : '' }}">
                      <input type="radio" name="frequency" id="monthly" value="monthly" autocomplete="off" {{ old('frequency', $budget->frequency) == 'monthly' ? 'checked' : '' }}> Monthly
                    </label>
                    <label class="btn btn-light  {{ old('frequency', $budget->frequency) == 'weekly' ? 'active' : '' }}">
                      <input type="radio" name="frequency" id="weekly" value="weekly" autocomplete="off" {{ old('frequency', $budget->frequency) == 'weekly' ? 'checked' : '' }}> Weekly
                    </label>
                  </div>
                </div>

                <label for="start_on" class="col-sm-auto col-form-label">Starting on</label>
                <div class="col-sm-3">
                  <select name="start_on" id="start_on" class="form-control{{ $errors->first('start_on', ' is-invalid') }}">
                    <option value="">Choose Day</option>
                    @foreach (range(1, 31) as $day)
                      <option value="{{ $day }}" {{ old('start_on', $budget->start_on) == $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="container">
      <div class="form-group text-right">
        <button class="btn btn-primary">Update</button>
      </div>
    </div>

  </form>
@endsection
