{{-- resources/views/objednavky/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Vytvořit novou objednávku</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($vybranaNabidka->samozber === 'ANO')
        <h4>Samosběr</h4>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $vybranaNabidka->nazev }}</h5>
                <p class="card-text"><strong>Popis:</strong> {{ $vybranaNabidka->popis }}</p>
                <p class="card-text">
                    <strong>Cena:</strong> 
                    @if ($vybranaNabidka->druh_ceny == 'HMOTNOST')
                        {{ $vybranaNabidka->cena }} Kč/kg
                    @else
                        {{ $vybranaNabidka->cena }} Kč/kus
                    @endif
                </p>
                <p class="card-text"><strong>Kategorie:</strong> {{ $vybranaNabidka->kategorie }}</p>
                <p class="card-text"><strong>Lokalita:</strong> {{ $vybranaNabidka->lokalita }}</p>
                <p class="card-text">
                    <strong>Čas dostupnosti:</strong> 
                    {{ $vybranaNabidka->cas_od }} - {{ $vybranaNabidka->cas_do }}
                </p>
                <p class="card-text">
                    <strong>Atributy:</strong>
                    <ul>
                        @foreach ($vybranaNabidka->atributy as $atribut)
                            <li>{{ $atribut->nazev }}: {{ $atribut->pivot->hodnota }}</li>
                        @endforeach
                    </ul>
                </p>
            </div>
        </div>
        <form action="{{ route('objednavky.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_nabidky" value="{{ $vybranaNabidka->id_nabidky }}">
            <input type="hidden" name="stav" value="vytvorena">
            <input type="hidden" name="druh_platby" value="hotovost">
            
            <input type="hidden" name="datum_vytvoreni" value="{{ now()->toDateString() }}">
            <button type="submit" class="btn btn-primary mt-3">Přidat do seznamu událostí</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Zpět</a>
        </form>

    @else
        <h4>Standardní objednávka</h4>
        {{-- Standardní UI --}}
        <form action="{{ route('objednavky.store') }}" method="POST" id="objednavkaForm">
            @csrf
            <div class="form-group">
                <label for="celkova_cena">Celková cena</label>
                <div class="input-group">
                    <input type="number" 
                        name="celkova_cena" 
                        class="form-control" 
                        id="celkova_cena" 
                        min="0" 
                        step="1" 
                        value="{{ old('celkova_cena', 0) }}" 
                        readonly>
                    <span class="input-group-text">Kč</span>
                </div>
            </div>        

            <div class="form-group">
                <label for="stav">Stav</label>
                <select name="stav_disabled" class="form-control" id="stav" disabled>
                    <option value="vytvorena" {{ old('stav', 'vytvorena') === 'vytvorena' ? 'selected' : '' }}>Vytvořena</option>
                    <option value="zaplacena" {{ old('stav') === 'zaplacena' ? 'selected' : '' }}>Zaplacena</option>
                    <option value="zpracovana" {{ old('stav') === 'zpracovana' ? 'selected' : '' }}>Zpracována</option>
                    <option value="zaslany" {{ old('stav') === 'zaslany' ? 'selected' : '' }}>Zaslaný</option>
                    <option value="dorucena" {{ old('stav') === 'dorucena' ? 'selected' : '' }}>Doručena</option>
                    <option value="prijata" {{ old('stav') === 'prijata' ? 'selected' : '' }}>Přijata</option>
                    <option value="stornovana" {{ old('stav') === 'stornovana' ? 'selected' : '' }}>Stornována</option>
                    <option value="reklamace" {{ old('stav') === 'reklamace' ? 'selected' : '' }}>Reklamace</option>
                </select>
                <!-- Add hidden input -->
                <input type="hidden" name="stav" value="vytvorena">
            </div>        

            <div class="form-group">
                <label for="datum_vytvoreni">Datum vytvoření</label>
                <input type="date" 
                    name="datum_vytvoreni" 
                    class="form-control" 
                    id="datum_vytvoreni" 
                    value="{{ old('datum_vytvoreni', now()->toDateString()) }}" 
                    readonly>
            </div>        
            
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const dateInput = document.getElementById('datum_vytvoreni');
            
                    if (!dateInput.value) { // Only set the value if it's not already set
                        const today = new Date();
                        const formattedDate = today.toISOString().split('T')[0];
                        dateInput.value = formattedDate;
                    }
                });
            </script>
            

            <div class="form-group">
                <label for="druh_platby">Druh platby <span style="color: red;">*</span></label>
                <select name="druh_platby" class="form-control" id="druh_platby" required>
                    <option value="" disabled selected>Vyberte druh platby</option>
                    <option value="prevod">Převod</option>
                    <option value="karta">Karta</option>
                    <option value="paypal">PayPal</option>
                    <option value="hotovost">Hotovost</option>
                </select>
            </div>

            @if ($vybranaNabidka == null)
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
                                placeholder="{{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Zadejte objem' : 'Zadejte množství' }}">
                            @if ($nabidka->druh_ceny == 'HMOTNOST')
                                <span class="input-group-text">kg</span>
                            @endif

                            <input type="number" 
                                name="nabidky[{{ $nabidka->id_nabidky }}][cena]" 
                                class="form-control cena-field" 
                                data-nabidka-id="{{ $nabidka->id_nabidky }}" 
                                placeholder="{{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Zadejte cenu za kilogram' : 'Zadejte jednotkovou cenu' }}" 
                                value="{{ old('nabidky.' . $nabidka->id_nabidky . '.cena', $nabidka->cena) }}">
                            <span class="input-group-text">{{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Kč/kg' : 'Kč/kus' }}</span>
                        </div>
                    </div>
                @endforeach
            @else
            @php
                $nabidka = $vybranaNabidka;
            @endphp
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
                            placeholder="{{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Zadejte objem' : 'Zadejte množství' }}">
                        @if ($nabidka->druh_ceny == 'HMOTNOST')
                            <span class="input-group-text">kg</span>
                        @endif
                
                        <input type="number" 
                            name="nabidky[{{ $nabidka->id_nabidky }}][cena]" 
                            class="form-control cena-field" 
                            data-nabidka-id="{{ $nabidka->id_nabidky }}" 
                            value="{{ old('nabidky.' . $nabidka->id_nabidky . '.cena', $nabidka->cena) }}" 
                            readonly>
                        <span class="input-group-text">{{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Kč/kg' : 'Kč/kus' }}</span>
                    </div>
                </div>            
            @endif

            <button type="submit" class="btn btn-primary">Uložit objednávku</button>
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
    @endif
@endsection
