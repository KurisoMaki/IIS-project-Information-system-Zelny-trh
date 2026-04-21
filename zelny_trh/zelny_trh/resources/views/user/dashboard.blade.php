@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Vítejte, {{ $user->jmeno }}</h2>

    <!-- Detaily profilu -->
    <div class="card mb-4">
        <div class="card-header">Detaily profilu</div>
        <div class="card-body">
            <p><strong>Přihlašovací jméno:</strong> {{ $user->prihlasovaci_jmeno }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Datum narození:</strong> {{ $user->datum_narozeni }}</p>
            <p><strong>Adresa:</strong> {{ $user->adresa }}</p>
            <p><strong>Další osobní údaje:</strong> {{ $user->dalsi_osobni_udaje }}</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Upravit profil</a>
        </div>
    </div>

    <!-- Nabídky uživatele -->
    <div class="card mb-4">
        <div class="card-header">Vaše nabídky</div>
        <div class="card-body">
            @if($nabidky->count())
                <ul>
                    @foreach($nabidky as $nabidka)
                        <li>
                            <h5>{{ $nabidka->nazev }}</h5>
                            @if($nabidka->samozber === 'ANO')
                                <h5><span class="text-success">(Samosběr!)</span></h5>
                            @endif
                            <strong>Popis:</strong> {{ $nabidka->popis }}<br>
                            <strong>Cena:</strong> {{ $nabidka->cena }} @if ($nabidka->druh_ceny === 'HMOTNOST')
                                    (Kč/kg)
                                @else
                                    (Kč/kus)
                                @endif
                            <br>
                            <strong>Množství:</strong> {{ $nabidka->mnozstvi }}<br>
                            @if($nabidka->samozber === 'ANO')
                                <strong>Samosběr</strong><br>
                            @endif
                            <a href="{{ route('nabidky.show', $nabidka->id_nabidky) }}" class="btn btn-info">Zobrazit detail</a>
                            <a href="{{ route('nabidky.edit', $nabidka->id_nabidky) }}" class="btn btn-warning">Upravit</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Nemáte žádné nabídky.</p>
            @endif
        </div>
    </div>

    <!-- Objednávky uživatele -->
    <div class="card mb-4">
        <div class="card-header">Vaše objednávky</div>
        <div class="card-body">
            @if($objednavky->count())
                <ul>
                    @foreach($objednavky as $objednavka)
                        <li class="list-group-item">
                            <h5>Objednávka ID: {{ $objednavka->id_objednavky }}</h5>
                            <strong>Stav:</strong> {{ ucfirst($objednavka->stav) }} <br>
                            <strong>Celková cena:</strong> {{ $objednavka->celkova_cena }} Kč <br>
                            <strong>Vytvořeno:</strong> {{ \Carbon\Carbon::parse($objednavka->datum_vytvoreni)->format('d.m.Y H:i') }} <br>
                            @if($objednavka->datum_prebrani)
                                <strong>Převzato:</strong> {{ \Carbon\Carbon::parse($objednavka->datum_prebrani)->format('d.m.Y H:i') }} <br>
                            @endif

                            <h6>Nabídky:</h6>
                            <ul>
                                @foreach($objednavka->nabidky as $nabidka)
                                    <li>
                                        <strong>Název:</strong> 
                                            {{ $nabidka->nazev }} <br>
                                        <strong>Obsahuje nabídku:</strong>
                                        <li>{{ $nabidka->nazev }}</li>
                                        @if($nabidka->samozber === 'ANO')
                                            <span class="text-success">(Samosběr!)</span>
                                        @endif
                                        <br>
                                        <strong>Popis:</strong> {{ $nabidka->popis ?? 'Neposkytnut' }} <br>
                                        <strong>Cena:</strong> {{ $nabidka->cena }}
                                            @if ($nabidka->druh_ceny === 'HMOTNOST')
                                                (Kč/kg)
                                            @else
                                                (Kč/kus)
                                            @endif
                                        <br>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('objednavky.show', $objednavka->id_objednavky) }}" class="btn btn-info mt-3">Zobrazit detail</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Nemáte žádné objednávky.</p>
            @endif
        </div>
    </div>

    <!-- Objednávky vašich nabídek -->
    <div class="card mb-4">
        <div class="card-header">Objednávky vašich nabídek</div>
        <div class="card-body">
            @if($objednavkyKZpracovani->count())
                <ul>
                    @foreach($objednavkyKZpracovani as $objednavka)
                        <li>
                            <strong>ID objednávky:</strong> {{ $objednavka->id_objednavky }}
                            <br>
                            <strong>Stav:</strong> {{ $objednavka->stav }}<br>
                            {{-- <strong>Samozběr:</strong> {{ $objednavka->samozber ? 'Ano' : 'Ne' }}<br> --}}
                            <strong>Celková cena:</strong> {{ $objednavka->celkova_cena }}<br>

                            <strong>Obsahuje nabídku:</strong>
                            <ul>
                                @foreach($objednavka->nabidky as $nabidka)
                                    <li>{{ $nabidka->nazev }}</li>
                                    @if($nabidka->samozber === 'ANO')
                                        <span class="text-success">(Samosběr!)</span>
                                    @endif
                                @endforeach
                            </ul>
                            
                            <strong>Zakazník:</strong> 
                            <a href="{{ route('uzivatele.show', $objednavka->vlastnikUzivatel) }}">
                                {{ $objednavka->vlastnikUzivatel->jmeno ?? 'Neznámý zákazník' }}
                            </a><br>
    
                            <form action="{{ route('objednavky.updateForFarmer', $objednavka->id_objednavky) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <label for="status">Aktualizovat stav:</label>
                                <select name="stav" id="status" class="form-select">
                                    <option value="vytvorena" @if($objednavka->stav === 'vytvorena') selected @endif>Vytvořena</option>
                                    <option value="zaplacena" @if($objednavka->stav === 'zaplacena') selected @endif>Zaplacena</option>
                                    <option value="zpracovana" @if($objednavka->stav === 'zpracovana') selected @endif>Zpracována</option>
                                    <option value="zaslany" @if($objednavka->stav === 'zaslany') selected @endif>Zaslaný</option>
                                    <option value="stornovana" @if($objednavka->stav === 'stornovana') selected @endif>Stornována</option>
                                    <option value="reklamace" @if($objednavka->stav === 'reklamace') selected @endif>Reklamace</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Aktualizovat</button>
                            </form>
                            
                            <a href="{{ route('objednavky.show', $objednavka->id_objednavky) }}" class="btn btn-info">Zobrazit detail</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Nemáte žádné objednávky pro vaše nabídky.</p>
            @endif
        </div>
    </div>
    

   <!-- Objednávky se samozběrem -->
   <div class="card mb-4">
    <div class="card-header">Seznamu událostí</div>
    <div class="card-body">
        @if($samozberObjednavky->count())
            <ul class="list-group">
                @foreach($samozberObjednavky as $objednavka)
                    <li class="list-group-item">
                        <h5 class="mt-3">Samosběr:</h5>
                        <ul>
                            @foreach($objednavka->nabidky as $nabidka)
                                <li>
                                    <p><strong>Název:</strong> {{ $nabidka->nazev }}</p>
                                    <p><strong>Popis:</strong> {{ $nabidka->popis ?? 'Neposkytnut' }}</p>
                                    <p><strong>Cena:</strong> {{ $nabidka->cena }} Kč
                                        @if ($nabidka->druh_ceny === 'HMOTNOST')
                                            (Kč/kg)
                                        @else
                                            (Kč/kus)
                                        @endif
                                
                                    <p><strong>Kategorie:</strong> {{ $nabidka->kategorie->nazev ?? 'Žádná' }}</p>
                                    <p><strong>Lokalita:</strong> {{ $nabidka->lokalita }}</p>
                                    <p><strong>Čas dostupnosti:</strong> 
                                        {{ \Carbon\Carbon::parse($nabidka->cas_od)->format('d.m.Y H:i') }} - 
                                        {{ \Carbon\Carbon::parse($nabidka->cas_do)->format('d.m.Y H:i') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('objednavky.show', $objednavka->id_objednavky) }}" class="btn btn-info">Zobrazit detail</a>
                    </li>
                @endforeach
            </ul>
        @else
            <p>Nemáte žádné objednávky se samosběrem.</p>
        @endif
    </div>
</div>
</div>
@endsection