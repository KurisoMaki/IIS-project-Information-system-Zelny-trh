@extends('layouts.app')

@section('content')
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1>Vytvořit {{ $typ === 'samosber' ? 'Nový Samosběr' : 'Novou Nabídku' }}</h1>
    <form action="{{ route('nabidky.store') }}" method="POST">
        @csrf

        <!-- Společné položky -->
        <div class="form-group">
            <label for="nazev">Název  <span style="color: red;">*</span></label>
            <input type="text" name="nazev" id="nazev" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="popis">Popis</label>
            <input type="text" name="popis" id="popis" class="form-control" value="{{ old('popis', '') }}">
        </div>
        @if($typ === 'normalni')
            <div class="form-group">
                <label for="mnozstvi">Množství  <span style="color: red;">*</span></label>
                <input type="number" name="mnozstvi" id="mnozstvi" class="form-control" min="0" required>
            </div>
        @endif
        <div class="form-group">
            <label for="cena">Cena  <span style="color: red;">*</span></label>
            <input type="number" name="cena" id="cena" class="form-control" min="0" required>
        </div>
        <div class="form-group">
            <label for="druh_ceny">Druh Ceny <span style="color: red;">*</span></label>
            <select name="druh_ceny" id="druh_ceny" class="form-control" required>
                <option value="HMOTNOST">Hmotnost</option>
                <option value="KUSY">Kusy</option>
            </select>
        </div>
        <div class="form-group">
            <label for="kategorie">Kategorie</label>
            <select class="form-control" id="kategorie" name="id_kategorie">
                <option value="">Žádná</option>
                @foreach($parentCategories as $parentCategory)
                    <option value="{{ $parentCategory->id_kategorie }}">{{ $parentCategory->nazev }}</option>
                @endforeach
            </select>
        </div>

        @if($typ === 'normalni')
            <input type="hidden" name="samozber" value="NE">
            <div class="form-group">
                <label for="misto_puvodu">Místo Původu</label>
                <input type="text" name="misto_puvodu" id="misto_puvodu" class="form-control">
            </div>
            <div class="form-group">
                <label for="trvanlivost">Trvanlivost</label>
                <input type="date" name="trvanlivost" id="trvanlivost" class="form-control">
            </div>
        @elseif($typ === 'samosber')
            <input type="hidden" name="mnozstvi" value="0">
            <input type="hidden" name="samozber" value="ANO">
            <div class="form-group">
                <label for="lokalita">Lokalita  <span style="color: red;">*</span></label>
                <input type="text" name="lokalita" id="lokalita" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="cas_od">Čas Od  <span style="color: red;">*</span></label>
                <input type="date" name="cas_od" id="cas_od" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="cas_do">Čas Do  <span style="color: red;">*</span></label>
                <input type="date" name="cas_do" id="cas_do" class="form-control" required>
            </div>
        @endif

        <h3>Atributy</h3>
        @foreach($atributy as $atribut)
        <div class="form-group">
            <label for="atribut_{{ $atribut->id_atributu }}">{{ $atribut->nazev }}</label>
            <select name="atributy[{{ $atribut->id_atributu }}]" id="atribut_{{ $atribut->id_atributu }}" class="form-control">
                <option value="">-- Vyberte hodnotu --</option>
                @foreach($atribut->hodnoty as $hodnota)
                <option value="{{ $hodnota->id_hodnoty }}">{{ $hodnota->hodnota }}</option>
                @endforeach
            </select>
        </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Uložit</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
    </form>
</div>
@endsection
