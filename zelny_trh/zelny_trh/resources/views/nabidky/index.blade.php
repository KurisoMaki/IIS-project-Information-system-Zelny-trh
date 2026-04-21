@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Nabídky</h1>

    @auth
        <a href="{{ route('nabidky.create', ['typ' => 'normalni']) }}" class="btn btn-primary mb-3">Vytvořit novou nabídku</a>
        <a href="{{ route('nabidky.create', ['typ' => 'samosber']) }}" class="btn btn-primary mb-3">Vytvořit nový samosběr</a>
    @endauth
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif    

    <!-- Filtrovací formulář -->
    <form method="GET" action="{{ route('nabidky.index') }}" class="mb-4">
        <div class="row">
            <!-- Filtrování podle názvu -->
            <div class="col-md-4">
                <h5><label for="nazev">Název nabídky</label></h5>
                <input type="text" id="nazev" name="nazev" class="form-control" 
                        value="{{ request('nazev') }}" placeholder="Hledat podle názvu">
            </div>

            <!-- Cena -->
            <div class="col-md-4 mb-3">
                <h5><label for="cena_od">Cena (od-do)</label></h5>
                <div class="input-group">
                    <input type="number" name="cena_od" id="cena_od" class="form-control" value="{{ request('cena_od') }}" placeholder="Od">
                    <span class="input-group-text">-</span>
                    <input type="number" name="cena_do" id="cena_do" class="form-control" value="{{ request('cena_do') }}" placeholder="Do">
                </div>
            </div>

            <!-- Filtrování podle kategorie -->
            <div class="col-md-4">
                <h5><label for="kategorie">Kategorie</label></h5>
                <select id="kategorie" name="kategorie" class="form-control">
                    <option value="">Všechny kategorie</option>
                    @foreach($kategorie as $kategorieItem)
                        <option value="{{ $kategorieItem->id_kategorie }}" 
                                {{ request('kategorie') == $kategorieItem->id_kategorie ? 'selected' : '' }}>
                            {{ $kategorieItem->nazev }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Checkbox pro samosběr -->
            <div class="col-md-4">
                <h5><label for="samozber">Samosběr</label></h5>
                <div class="form-check">
                    <input type="checkbox" name="samozber" id="samozber" class="form-check-input" 
                        value="1" {{ request('samozber') ? 'checked' : '' }}>
                    <label for="samozber" class="form-check-label">Pouze samosběr</label>
                </div>
            </div>

            <!-- Řazení -->
            <div class="col-md-4">
                <h5><label for="razeni">Řazení podle ceny</label></h5>
                <select id="razeni" name="razeni" class="form-control">
                    <option value="">Bez řazení</option>
                    <option value="asc" {{ request('razeni') == 'asc' ? 'selected' : '' }}>Od nejnižší</option>
                    <option value="desc" {{ request('razeni') == 'desc' ? 'selected' : '' }}>Od nejvyšší</option>
                </select>
            </div>

            <!-- Filtrování podle atributů -->
            <div class="col-md-4">
                <h5>Atributy</h5>
                @foreach($atributy as $atribut)
                    <div class="mb-3">
                        <strong>{{ $atribut->nazev }}</strong>
                        <div>
                            @foreach($atribut->hodnoty as $hodnota)
                                    <label class="me-2">
                                        <input type="checkbox" name="hodnoty[]" value="{{ $hodnota->id_hodnoty }}" 
                                        {{ in_array($hodnota->id_hodnoty, (array) request('hodnoty', [])) ? 'checked' : '' }}>
                                        {{ $hodnota->hodnota }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>



        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Filtrovat</button>
            <a href="{{ route('nabidky.index') }}" class="btn btn-secondary">Resetovat</a>
        </div>
    </form>

    @if($nabidky->isEmpty())
        <p>Žádné nabídky nejsou dostupné.</p>
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
                    <th>Vlastník</th>
                    <th>Operace</th>
                    <th>Objednat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nabidky as $nabidka)
                    <tr>
                        <td>
                            {{ $nabidka->nazev }}
                            @if($nabidka->samozber === 'ANO')
                                <div>
                                    <span class="badge bg-success">Samosběr</span>
                                </div>
                            @endif
                        </td>
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
                            {{ $nabidka->druh_ceny === 'KUSY' ? 'Kč/kus' : 'Kč/kg' }}
                        </td>
                        <td>{{ $nabidka->mnozstvi }} </td>
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
                                <span>Neznámý</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-start">
                                <a href="{{ route('nabidky.show', $nabidka->id_nabidky) }}" class="btn btn-info btn-sm mt-1">Zobrazit</a>
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
