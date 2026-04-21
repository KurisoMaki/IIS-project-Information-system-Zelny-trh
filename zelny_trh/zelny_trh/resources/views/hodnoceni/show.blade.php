@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail hodnocení</h1>

    <div class="card">
        <div class="card-header">
            <strong>Hodnocení ID:</strong> {{ $hodnoceni->id_hodnoceni }}
        </div>
        <div class="card-body">
            <p><strong>Nabídka:</strong> {{ $hodnoceni->nabidka->nazev }}</p>
            <p><strong>Zákazník:</strong> {{ $hodnoceni->zakaznik }}</p>
            <p><strong>Hodnocení:</strong> {{ $hodnoceni->hodnoceni }}</p>
            <p><strong>Komentář:</strong> {{ $hodnoceni->komentar }}</p>
            <p><strong>Datum hodnocení:</strong> {{ $hodnoceni->datum_hodnoceni }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
        </div>
    </div>
</div>
@endsection
