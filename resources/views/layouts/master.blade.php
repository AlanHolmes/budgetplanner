<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Budget Planner') }}</title>

  <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
</head>
<body>
<div id="app">
  @yield('content')
</div>

<script src="{{ asset(mix('js/app.js')) }}" type="text/javascript"></script>
{{ svg_spritesheet() }}
</body>
</html>
