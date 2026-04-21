@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail atributu: {{ $atribut->nazev }}</h1>

    <h3>Hodnoty atributu</h3>
    <ul>
        @foreach ($atribut->hodnoty as $hodnota)
            <li>{{ $hodnota->hodnota }}</li>
        @endforeach
    </ul>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
</div>
@endsection
