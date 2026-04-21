@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Profil uživatele: {{ $uzivatel->jmeno }}</h1>
    <p><strong>Další osobní údaje:</strong> {{ $uzivatel->dalsi_osobni_udaje }}</p>
    <p><strong>Email:</strong> {{ $uzivatel->email }}</p>
    <p><strong>Datum narození:</strong> {{ $uzivatel->datum_narozeni }}</p>
    
    <h2>Nabídky uživatele</h2>
    @if($uzivatel->nabidky->isEmpty())
        <p>Uživatel nemá žádné nabídky.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Název</th>
                    <th>Kategorie</th>
                    <th>Popis</th>
                    <th>Cena</th>
                    <th>Množství</th>
                    <th>Trvanlivost</th>
                    <th>Atributy (Hodnoty)</th>
                    <th>Hodnocení</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uzivatel->nabidky as $nabidka)
                    <tr>
                        <td>{{ $nabidka->nazev }}</td>
                        <td>
                            @if($nabidka->kategorie)
                                @php
                                    $categoriesChain = [];
                                    $currentCategory = $nabidka->kategorie;
                                    while ($currentCategory) {
                                        $categoriesChain[] = $currentCategory->nazev;
                                        $currentCategory = $currentCategory->parentCategory; // Assumes 'parentCategory' relationship exists
                                    }
                                    $categoriesChain = array_reverse($categoriesChain);
                                @endphp
                                {{ implode(' > ', $categoriesChain) }}
                            @else
                                Bez kategorie
                            @endif
                        </td>
                        <td>{{ $nabidka->popis }}</td>
                        <td>
                            {{ $nabidka->cena }}
                            @if($nabidka->druh_ceny === 'HMOTNOST')
                                za kg
                            @elseif($nabidka->druh_ceny === 'KUSY')
                                za kus
                            @endif
                        </td>
                        <td>
                            {{ $nabidka->mnozstvi }}
                            @if($nabidka->druh_ceny === 'HMOTNOST')
                                kg
                            @elseif($nabidka->druh_ceny === 'KUSY')
                                kusu
                            @endif
                        </td>
                        <td>{{ $nabidka->trvanlivost }}</td>
                        <td>
                            @if($nabidka->atributy->isNotEmpty())
                                <ul>
                                    @foreach($nabidka->atributy as $atribut)
                                        <li>
                                            {{ $atribut->nazev }}: 
                                            {{ $atribut->pivot->id_hodnoty 
                                                ? $atribut->hodnoty->firstWhere('id_hodnoty', $atribut->pivot->id_hodnoty)?->hodnota 
                                                : 'No value' 
                                            }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span>No attributes</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $prumerHodnoceni = $nabidka->hodnoceni->avg('hodnoceni');
                                $pocetHodnoceni = $nabidka->hodnoceni->count();
                            @endphp
                            <strong>{{ $prumerHodnoceni ? number_format($prumerHodnoceni, 1) : 'No ratings' }}</strong>
                            ({{ $pocetHodnoceni }} hodnocení)
                            <a href="{{ route('hodnoceni.create', ['nabidka' => $nabidka->id_nabidky]) }}" 
                                class="btn btn-success btn-sm mt-1">Ohodnotit</a>
                            <a href="{{ route('hodnoceni.index', ['id_nabidky' => $nabidka->id_nabidky]) }}" 
                                class="btn btn-info btn-sm mt-1">Zobrazit hodnocení</a>
                        </td>
                        <td>
                            <a href="{{ route('nabidky.show', $nabidka->id_nabidky) }}" class="btn btn-info btn-sm">Detail</a>
                            <a href="{{ route('objednavky.create', ['nabidka' => $nabidka->id_nabidky]) }}" 
                                class="btn btn-success btn-sm">Objednat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
</div>
@endsection
