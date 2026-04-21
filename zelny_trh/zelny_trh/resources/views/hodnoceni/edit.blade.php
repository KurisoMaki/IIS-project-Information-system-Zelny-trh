@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Upravit hodnocení</h1>

    <form action="{{ route('hodnoceni.update', $hodnoceni->id_hodnoceni) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="id_nabidky" class="form-label">Nabídka</label>
            <select name="id_nabidky" id="id_nabidky" class="form-control" required>
                @foreach($nabidky as $nabidka)
                    <option value="{{ $nabidka->id_nabidky }}" 
                        {{ $hodnoceni->id_nabidky == $nabidka->id_nabidky ? 'selected' : '' }}>
                        {{ $nabidka->nazev }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="zakaznik" class="form-label">Zákazník</label>
            <input type="text" name="zakaznik" id="zakaznik" class="form-control" 
                   value="{{ $hodnoceni->zakaznik }}" required>
        </div>
        <div class="form-group">
            <label for="hodnoceni">Hodnocení <span style="color: red;">*</span></label>
            <select name="hodnoceni" id="hodnoceni" class="form-control" required>
                <option value="1">1 - Špatné</option>
                <option value="2">2 - Podprůměrné</option>
                <option value="3">3 - Průměrné</option>
                <option value="4">4 - Dobré</option>
                <option value="5">5 - Výborné</option>
            </select>
        </div>
        <div>
            <label for="komentar" class="form-label">Komentář</label>
            <textarea name="komentar" id="komentar" class="form-control" rows="4">{{ $hodnoceni->komentar }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Uložit</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
    </form>
</div>
@endsection
