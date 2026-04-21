@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Objednávky</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                {{-- <th>ID</th> --}}
                <th>Vlastník</th>
                <th>Celková cena</th>
                <th>Stav</th>
                <th>Datum vytvoření</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($objednavky as $objednavka)
                <tr>
                    {{-- <td>{{ $objednavka->id_objednavky }}</td> --}}
                    <td>{{ $objednavka->vlastnikUzivatel->prihlasovaci_jmeno ?? 'N/A' }}</td>
                    <td>{{ number_format($objednavka->celkova_cena, 2) }} Kč</td>
                    <td>{{ $objednavka->stav }}</td>
                    <td>{{ $objednavka->datum_vytvoreni }}</td>
                    <td>
                        <a href="{{ route('objednavky.show', $objednavka->id_objednavky) }}" class="btn btn-info btn-sm">Zobrazit</a>
                        <a href="{{ route('objednavky.edit', $objednavka->id_objednavky) }}" class="btn btn-warning btn-sm">Upravit</a>
                        <form action="{{ route('objednavky.destroy', $objednavka->id_objednavky) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Opravdu chcete tuto objednávku smazat?')">Smazat</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
