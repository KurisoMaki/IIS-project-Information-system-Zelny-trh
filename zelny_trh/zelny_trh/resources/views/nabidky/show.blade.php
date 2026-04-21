@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail nabídky: {{ $nabidka->nazev }}</h1>

    <table class="table table-bordered">
        <tr>
            <th>Vlastník</th>
            <td>
                <a href="{{ route('uzivatele.show', $nabidka->vlastnik) }}">
                    {{ $nabidka->vlastnik }}
                </a>
            </td>
        </tr>
        <tr>
            <th>Kategorie</th>
            <td>
                @if ($nabidka->kategorie)
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
        </tr>
        <tr>
            <th>Popis</th>
            <td>{{ $nabidka->popis }}</td>
        </tr>
        <tr>
            <th>Cena</th>
            <td>{{ $nabidka->cena }} Kč</td>
        </tr>
        <tr>
            <th>Množství</th>
            <td>{{ $nabidka->mnozstvi }}</td>
        </tr>
        <tr>
            <th>Druh ceny</th>
            <td>{{ $nabidka->druh_ceny }}</td>
        </tr>
        <tr>
            <th>Trvanlivost</th>
            <td>{{ $nabidka->trvanlivost ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Samozber</th>
            <td>{{ $nabidka->samozber }}</td>
        </tr>
        <tr>
            <th>Lokalita</th>
            <td>{{ $nabidka->lokalita }}</td>
        </tr>
    </table>

    <h3>Atributy</h3>
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

    <div class="mt-3">
        <a href="{{ route('objednavky.create', ['nabidka' => $nabidka->id_nabidky]) }}" 
            class="btn btn-primary">Objednat</a>
        <a href="{{ route('hodnoceni.create', ['nabidka' => $nabidka->id_nabidky]) }}" 
        class="btn btn-info">Hodnotit</a>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
    </div>
</div>
@endsection
