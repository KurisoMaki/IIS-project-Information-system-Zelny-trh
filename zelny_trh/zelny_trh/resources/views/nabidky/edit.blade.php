@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editace Nabídky</h1>
    <form action="{{ route('nabidky.update', $nabidka->id_nabidky) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- General Fields -->
        <div class="form-group">
            <label for="nazev">Název:</label>
            <input type="text" name="nazev" id="nazev" class="form-control" value="{{ $nabidka->nazev }}" required>
        </div>
        <div class="form-group">
            <label for="popis">Popis:</label>
            <textarea name="popis" id="popis" class="form-control">{{ $nabidka->popis }}</textarea>
        </div>
        <div class="form-group">
            <label for="mnozstvi">Množství:</label>
            <input type="number" name="mnozstvi" id="mnozstvi" class="form-control" value="{{ $nabidka->mnozstvi }}" min="0" required>
        </div>
        <div class="form-group">
            <label for="cena">Cena:</label>
            <input type="number" name="cena" id="cena" class="form-control" value="{{ $nabidka->cena }}" min="0" required>
        </div>
        <div class="form-group">
            <label for="druh_ceny">Druh Ceny:</label>
            <select name="druh_ceny" id="druh_ceny" class="form-control" required>
                <option value="HMOTNOST" {{ $nabidka->druh_ceny == 'HMOTNOST' ? 'selected' : '' }}>Hmotnost</option>
                <option value="KUSY" {{ $nabidka->druh_ceny == 'KUSY' ? 'selected' : '' }}>Kusy</option>
            </select>
        </div>

        <div class="form-group">
            <label for="kategorie">Kategorie</label>
            <select class="form-control" id="kategorie" name="id_kategorie" required>
                <option value="">Žádná</option>
                @foreach($allCategories as $category)
                    <option value="{{ $category->id_kategorie }}" 
                        @if($category->id_kategorie == $nabidka->id_kategorie) selected @endif>
                        {{ $category->nazev }}
                    </option>
                @endforeach
            </select>
        </div>
    
        <!-- Attributes Section -->
        <h3>Atributy</h3>
        @foreach($atributy as $atribut)
        <div class="form-group">
            <label for="atribut_{{ $atribut->id_atributu }}">{{ $atribut->nazev }}:</label>
            <select name="atributy[{{ $atribut->id_atributu }}]" id="atribut_{{ $atribut->id_atributu }}" class="form-control">
                
                @if (!$nabidka->atributy->contains('id_atributu', $atribut->id_atributu))
                    <!-- If the attribute has no current value -->
                    <option value="">---</option>
                    @foreach($atribut->hodnoty as $hodnota)
                    <option value="{{ $hodnota->id_hodnoty }}" 
                        {{ optional($nabidka->atributy->firstWhere('id_atributu', $atribut->id_atributu))->id_hodnoty == $hodnota->id_hodnoty ? 'selected' : '' }}>
                        {{ $hodnota->hodnota }}
                    </option>
                    @endforeach
                @else
                    @foreach($atribut->hodnoty as $hodnota)
                    <option value="{{ $hodnota->id_hodnoty }}" 
                        {{ optional($nabidka->atributy->firstWhere('id_atributu', $atribut->id_atributu))->id_hodnoty == $hodnota->id_hodnoty ? 'selected' : '' }}>
                        {{ $hodnota->hodnota }}
                    </option>
                    @endforeach
                    <!-- Option pro prazdnou hodnotu -->
                    <option value="">---</option>
                @endif
            </select>
        </div>
        @endforeach

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Uložit změny</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>

    </form>
</div>
@endsection
