@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ohodnotit produkt</h1>
    <form action="{{ route('hodnoceni.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_nabidky" value="{{ $nabidka->id_nabidky }}">
        
        <h1 class="h3">Nabídka: {{ $nabidka->nazev }}</h1>

        <div class="form-group">
            <label for="hodnoceni">Hodnocení (1-5) <span style="color: red;">*</span></label>
            <input type="number" name="hodnoceni" id="hodnoceni" class="form-control" min="1" max="5" required>
        </div>

        <div class="form-group">
            <label for="komentar">Komentář</label>
            <textarea name="komentar" id="komentar" class="form-control" rows="4"></textarea>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Odeslat hodnocení</button> 
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
        </div>
    </form>
</div>
@endsection
