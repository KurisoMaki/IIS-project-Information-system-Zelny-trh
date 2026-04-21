{{-- resources/views/objednavky/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Upravit objednávku #{{ $objednavka->id_objednavky }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('objednavky.update', $objednavka->id_objednavky) }}" method="POST" id="objednavkaForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="celkova_cena">Celková cena</label>
            <div class="input-group">
                <input type="number" name="celkova_cena" class="form-control" id="celkova_cena" min="0" readonly
                       value="{{ $objednavka->celkova_cena }}" required>
                <span class="input-group-text">Kč</span>
            </div>
        </div>

        <div class="form-group">
            <label for="stav">Stav</label>
            <select name="stav" class="form-control" id="stav" 
                {{ Auth::user()->urole === 'Zakaznik' || Auth::user()->urole === 'Farmar' ? 'disabled' : '' }} required>
                <option value="" disabled>Vyberte stav <span style="color: red;">*</span></option>
                <option value="vytvorena" {{ $objednavka->stav === 'vytvorena' ? 'selected' : '' }}>Vytvořena</option>
                <option value="zaplacena" {{ $objednavka->stav === 'zaplacena' ? 'selected' : '' }}>Zaplacena</option>
                <option value="zpracovana" {{ $objednavka->stav === 'zpracovana' ? 'selected' : '' }}>Zpracována</option>
                <option value="zaslany" {{ $objednavka->stav === 'zaslany' ? 'selected' : '' }}>Zaslaný</option>
                <option value="dorucena" {{ $objednavka->stav === 'dorucena' ? 'selected' : '' }}>Doručena</option>
                <option value="prijata" {{ $objednavka->stav === 'prijata' ? 'selected' : '' }}>Přijata</option>
                <option value="stornovana" {{ $objednavka->stav === 'stornovana' ? 'selected' : '' }}>Stornována</option>
                <option value="reklamace" {{ $objednavka->stav === 'reklamace' ? 'selected' : '' }}>Reklamace</option>
            </select>
        </div>

        @if(Auth::user()->urole === 'Zakaznik' || Auth::user()->urole === 'Farmar')
            <input type="hidden" name="stav" value="{{ $objednavka->stav }}">
        @endif

        <div class="form-group">
            <label for="datum_vytvoreni">Datum vytvoření</label>
            <input type="date" name="datum_vytvoreni" class="form-control" id="datum_vytvoreni" 
                   value="{{ $objednavka->datum_vytvoreni }}" required>
        </div>

        <div class="form-group">
            <label for="druh_platby">Druh platby <span style="color: red;">*</span></label>
            <select name="druh_platby" class="form-control" id="druh_platby" required>
                <option value="" disabled>Vyberte druh platby</option>
                <option value="prevod" {{ $objednavka->druh_platby === 'prevod' ? 'selected' : '' }}>Převod</option>
                <option value="karta" {{ $objednavka->druh_platby === 'karta' ? 'selected' : '' }}>Karta</option>
                <option value="paypal" {{ $objednavka->druh_platby === 'paypal' ? 'selected' : '' }}>PayPal</option>
                <option value="hotovost" {{ $objednavka->druh_platby === 'hotovost' ? 'selected' : '' }}>Hotovost</option>
            </select>
        </div>

        
        <h4>Nabídky</h4>

        @foreach ($nabidky as $nabidka)
            <div class="form-group">
                <label for="nabidky[{{ $nabidka->id_nabidky }}][objem]">
                    {{ $nabidka->nazev }}
                </label>
                <div class="input-group">
                    <input type="hidden" name="nabidky[{{ $nabidka->id_nabidky }}][id_nabidky]" 
                           value="{{ $nabidka->id_nabidky }}">

                    <input type="number" name="nabidky[{{ $nabidka->id_nabidky }}][objem]" 
                           class="form-control objem-field" 
                           data-nabidka-id="{{ $nabidka->id_nabidky }}" 
                           placeholder="{{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Zadejte objem' : 'Zadejte množství' }}" 
                           value="{{ old('nabidky.' . $nabidka->id_nabidky . '.objem', $objednavka->nabidky->where('id_nabidky', $nabidka->id_nabidky)->first()->pivot->objem ?? '') }}">
                    @if ($nabidka->druh_ceny == 'HMOTNOST')
                        <span class="input-group-text">kg</span>
                    @endif

                    <input type="number" name="nabidky[{{ $nabidka->id_nabidky }}][cena]" 
                           class="form-control cena-field" 
                           data-nabidka-id="{{ $nabidka->id_nabidky }}" 
                           placeholder="{{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Zadejte cenu za kilogram' : 'Zadejte jednotkovou cenu' }}" 
                           value="{{ old('nabidky.' . $nabidka->id_nabidky . '.cena', $objednavka->nabidky->where('id_nabidky', $nabidka->id_nabidky)->first()->pivot->cena ?? $nabidka->cena) }}">
                    <span class="input-group-text">{{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Kč/kg' : 'Kč/kus' }}</span>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Uložit změny</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const celkovaCenaInput = document.getElementById('celkova_cena');

        // Funkce pro výpočet celkové ceny
        function calculateTotalPrice() {
            let totalPrice = 0;

            document.querySelectorAll('.objem-field').forEach(function (objemInput) {
                const nabidkaId = objemInput.getAttribute('data-nabidka-id');
                const cenaInput = document.querySelector(`.cena-field[data-nabidka-id="${nabidkaId}"]`);
                
                const objem = parseFloat(objemInput.value) || 0;
                const cena = parseFloat(cenaInput.value) || 0;

                totalPrice += objem * cena;
            });

            celkovaCenaInput.value = Math.round(totalPrice);
        }

        // Přidání listenerů na změnu polí objem a cena
        document.querySelectorAll('.objem-field, .cena-field').forEach(function (input) {
            input.addEventListener('input', calculateTotalPrice);
        });
    });
</script>
@endsection
