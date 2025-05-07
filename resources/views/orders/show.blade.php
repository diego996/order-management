@extends('layouts.app')

@section('content')
    @livewire('order-details', ['order' => $order])
@endsection
