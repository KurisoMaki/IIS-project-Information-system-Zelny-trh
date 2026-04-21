@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nabídky</h1>

    <a href="{{ route('nabidky.create', ['typ' => 'normalni']) }}" class="btn btn-primary mb-3">Vytvořit novou nabídku</a>
    <a href="{{ route('nabidky.create', ['typ' => 'samosber']) }}" class="btn btn-primary mb-3">Vytvořit nový samosběr</a>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif    


    @if($nabidky->isEmpty())
        <p>Žádné nabídky nejsou dostupné.</p>
    @else
    <table class="table table-striped">
        <thead>
            <tr>
                {{-- <th>#</th> --}}
                <th>Název</th>
                <th>Kategorie</th>
                <th>Popis</th>
                <th>Cena</th>
                <th>Množství</th>
                <th>Trvanlivost</th>
                <th>Atributy (Hodnoty)</th>
                <th>Hodnocení</th>
                <th>Vlastník</th>
                <th>Actions</th>
                <th>Objednat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nabidky as $nabidka)
                <tr>
                    {{-- <td>{{ $nabidka->id_nabidky }}</td> --}}
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
                    <td>{{ $nabidka->cena }}</td>
                    <td>{{ $nabidka->mnozstvi }}</td>
                    <td>{{ $nabidka->trvanlivost }}</td>
                    <td>
                        @if($nabidka->atributy->isNotEmpty())
                            <ul>
                                @foreach($nabidka->atributy as $atribut)
                                    <li>
                                        {{ $atribut->nazev }}: 
                                        {{ $atribut->pivot->id_hodnoty 
                                            ? $atribut->hodnoty->firstWhere('id_hodnoty', $atribut->pivot->id_hodnoty)?->hodnota 
                                            : 'Bez hodnoty' 
                                        }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span>Žádné atributy</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-column align-items-start">
                            @php
                                $prumerHodnoceni = $nabidka->hodnoceni->avg('hodnoceni');
                                $pocetHodnoceni = $nabidka->hodnoceni->count();
                            @endphp
                        
                            <div>
                                <strong>
                                    {{ $prumerHodnoceni ? number_format($prumerHodnoceni, 1) : 'No ratings' }}
                                </strong>
                                ({{ $pocetHodnoceni }} hodnocení)
                            </div>
                        
                            <div class="mt-2">
                                <a href="{{ route('hodnoceni.create', ['nabidka' => $nabidka->id_nabidky]) }}" 
                                   class="btn btn-success btn-sm mb-2">Ohodnotit</a>
                                <a href="{{ route('hodnoceni.index', ['id_nabidky' => $nabidka->id_nabidky]) }}" 
                                   class="btn btn-info btn-sm">Zobrazit hodnocení</a>
                            </div>
                        </div>
                        
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
                        <div class="d-flex flex-column align-items-start">
                            <a href="{{ route('nabidky.show', $nabidka->id_nabidky) }}" class="btn btn-info btn-sm mt-1">Zobrazit</a>
                            <a href="{{ route('nabidky.edit', $nabidka->id_nabidky) }}" class="btn btn-warning btn-sm mt-1">Upravit</a>
                            <form action="{{ route('nabidky.destroy', $nabidka->id_nabidky) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mt-1" onclick="return confirm('Opravdu chcete smazat tuto nabídku?')">Smazat</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('objednavky.create', ['nabidka' => $nabidka->id_nabidky]) }}" 
                            class="btn btn-success btn-sm">Objednat</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
