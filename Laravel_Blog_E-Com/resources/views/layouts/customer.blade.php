@extends('lay_app')

@section('sidebar')
  @include('partials.sidebar.customer', ['categories' => $categories ?? []])
@endsection
