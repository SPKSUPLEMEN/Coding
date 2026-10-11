@extends('layouts.app')

@section('content')
<div class="container">
    @if(Auth::user()->role == 'admin')
        @include('pages.home.admin')
    @elseif(Auth::user()->role == 'mhs')
        @include('pages.home.mhs')
    @endif
</div>
@endsection
