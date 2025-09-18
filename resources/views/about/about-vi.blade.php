@extends('layouts.master')

@section('content')

<iframe 
  src="{{ asset('about-vi.html') }}"
  style="width: 100%; height: 100vh; padding-bottom: 80px; border:none; overflow:hidden;"
  allowfullscreen
  loading="lazy">
</iframe>

@endsection
