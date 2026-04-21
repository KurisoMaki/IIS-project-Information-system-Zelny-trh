{{-- resources/views/objednavky/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail objednávky #{{ $objednavka->id_objednavky }}</h1>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $objednavka->id_objednavky }}</td>
        </tr>
        <tr>
            <th>
                @foreach($objednavka->nabidky as $nabidka)
                    @if ($nabidka->samozber === 'ANO')
                        Samozběr
                        <td>
                            SAMOSBER
                        </td>
                    @else
                        Nabídka
                        <td>
                            NABIDKA
                        </td>
                    @endif
                @endforeach
            </th>
        </tr>        
        <tr>
            <th>Vlastník</th>
            <td>{{ $objednavka->vlastnikUzivatel->prihlasovaci_jmeno ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Celková cena</th>
            <td>{{ number_format($objednavka->celkova_cena, 2) }} Kč</td>
        </tr>
        <tr>
            <th>Stav</th>
            <td>{{ $objednavka->stav }}</td>
        </tr>
        <tr>
            <th>Datum vytvoření</th>
            <td>{{ $objednavka->datum_vytvoreni }}</td>
        </tr>
        <tr>
            <th>Nabídka</th>
            <td>
                <ul>
                    @foreach ($objednavka->nabidky as $nabidka)
                        <li>
                            @if ($nabidka->samozber === 'ANO')
                                <h5>{{ 'Samozběr: ' . $nabidka->nazev }}</h5>
                                <ul>
                                    <li><strong>Popis:</strong> {{ $nabidka->popis ?? 'Neposkytnut' }}</li>
                                    <li><strong>Cena:</strong> {{ $nabidka->pivot->cena }} Kč</li>
                                    <li><strong>Druh ceny:</strong>
                                        @if ($nabidka->druh_ceny === 'HMOTNOST')
                                            Hmotnost (Kč/kg)
                                        @else
                                            Kus (Kč/kus)
                                        @endif
                                    </li>
                                    <li><strong>Kategorie:</strong> {{ $nabidka->kategorie->nazev ?? 'Žádná' }}</li>
                                    <li><strong>Lokalita:</strong> {{ $nabidka->lokalita }}</li>
                                    <li><strong>Čas Od:</strong> {{ \Carbon\Carbon::parse($nabidka->cas_od)->format('d.m.Y H:i') }}</li>
                                    <li><strong>Čas Do:</strong> {{ \Carbon\Carbon::parse($nabidka->cas_do)->format('d.m.Y H:i') }}</li>
                                    <li><strong>Atributy:</strong>
                                        @if ($nabidka->atributy->isNotEmpty())
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Název atributu</th>
                                                        <th>Hodnota</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($nabidka->atributy as $atribut)
                                                        <tr>
                                                            <td>{{ $atribut->nazev }}</td>
                                                            <td>{{ $atribut->hodnoty->firstWhere('id_hodnoty', $atribut->pivot->id_hodnoty)?->hodnota ?? 'Žádná hodnota' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p>Žádné atributy</p>
                                        @endif
                                    </li>
                                </ul>
                            @else
                                <h5>{{ 'Nabídka: ' . $nabidka->nazev }}</h5>
                                <ul>
                                    <li><strong>Popis:</strong> {{ $nabidka->popis ?? 'Neposkytnut' }}</li>
                                    <li><strong>Množství:</strong> {{ $nabidka->pivot->objem }}</li>
                                    <li><strong>Cena:</strong> {{ $nabidka->pivot->cena }} Kč</li>
                                    <li><strong>Druh ceny:</strong>
                                        @if ($nabidka->druh_ceny === 'HMOTNOST')
                                            Hmotnost (Kč/kg)
                                        @else
                                            Kus (Kč/kus)
                                        @endif
                                    </li>
                                    <li><strong>Kategorie:</strong> {{ $nabidka->kategorie->nazev ?? 'Žádná' }}</li>
                                    <li><strong>Místo původu:</strong> {{ $nabidka->misto_puvodu ?? 'Neposkytnuto' }}</li>
                                    <li><strong>Trvanlivost:</strong> 
                                        {{ $nabidka->trvanlivost ? \Carbon\Carbon::parse($nabidka->trvanlivost)->format('d.m.Y') : 'Neposkytnuto' }}
                                    </li>
                                    <li><strong>Atributy:</strong>
                                        @if ($nabidka->atributy->isNotEmpty())
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Název atributu</th>
                                                        <th>Hodnota</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($nabidka->atributy as $atribut)
                                                        <tr>
                                                            <td>{{ $atribut->nazev }}</td>
                                                            <td>{{ $atribut->hodnoty->firstWhere('id_hodnoty', $atribut->pivot->id_hodnoty)?->hodnota ?? 'Žádná hodnota' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p>Žádné atributy</p>
                                        @endif
                                    </li>
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </td>
        </tr>
    </table>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
</div>
@endsection
