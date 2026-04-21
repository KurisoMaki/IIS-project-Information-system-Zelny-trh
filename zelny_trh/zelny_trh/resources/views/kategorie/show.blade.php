@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $kategorie->nazev }}</h1>

    <div>
        <strong>Popis:</strong> {{ $kategorie->popis }}
    </div>

    <div>
        <strong>Rodičovská Kategorie:</strong> {{ $kategorie->parentCategory ? $kategorie->parentCategory->nazev : 'Žádná' }}
    </div>

    <div>
        <strong>Subkategorie:</strong>
        @if($kategorie->subcategories->count())
            <div class="d-flex flex-wrap">
                @foreach($kategorie->subcategories as $subcategory)
                    <a href="{{ route('kategorie.show', $subcategory->id_kategorie) }}" class="btn btn-primary m-2">
                        {{ $subcategory->nazev }}
                    </a>
                @endforeach
            </div>
        @else
            <p>Žádné subkategorie.</p>
        @endif
    </div>

    <div>
        <h2>Nabídky v této kategorii</h2>
        @if($kategorie->nabidky->count())
            <table class="table table-striped">
                <thead>
                    <tr>
                        {{-- <th>#</th> --}}
                        <th>Název</th>
                        <th>Popis</th>
                        <th>Cena</th>
                        <th>Množství</th>
                        <th>Trvanlivost</th>
                        <th>Samozběr</th>
                        <th>Atributy</th>
                        <th>Hodnocení</th>
                        <th>Vlastník</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategorie->nabidky as $nabidka)
                        <tr>
                            {{-- <td>{{ $nabidka->id_nabidky }}</td> --}}
                            <td>{{ $nabidka->nazev }}</td>
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
                            <td>{{ $nabidka->samozber === 'ANO' ? 'Ano' : 'Ne' }}</td>
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
                                @if($nabidka->vlastnik)
                                    <a href="{{ route('uzivatele.show', $nabidka->vlastnik) }}">
                                        {{ $nabidka->vlastnik }}
                                    </a>
                                @else
                                    <span>Unknown</span>
                                @endif
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
        @else
            <p>Žádné nabídky v této kategorii.</p>
        @endif
    </div>

    @if($kategorie->foto)
        <div>
            <strong>Foto:</strong><br>
            <img src="data:image/jpeg;base64,{{ base64_encode($kategorie->foto) }}" alt="Kategorie Foto" width="200">
        </div>
    @endif

    <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
</div>
@endsection
