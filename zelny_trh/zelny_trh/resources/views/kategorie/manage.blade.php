@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Seznam Kategorií</h1>
    <a href="{{ route('kategorie.create') }}" class="btn btn-primary mb-3">Vytvorit novou kategorii</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Název</th>
                <th>Popis</th>
                <th>Rodičovská Kategorie</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategorie as $k)
                @if(!$k->parent) {{-- Pouze nejvyšší kategorie --}}
                    <tr>
                        <td>{{ $k->nazev }}</td>
                        <td>{{ $k->popis }}</td>
                        <td>{{ $k->parentCategory ? $k->parentCategory->nazev : 'Žádná' }}</td>
                        <td>
                            <a href="{{ route('kategorie.show', $k->id_kategorie) }}" class="btn btn-info btn-sm">Zobrazit</a>
                            <a href="{{ route('kategorie.edit', $k->id_kategorie) }}" class="btn btn-warning btn-sm">Upravit</a>
                            <form action="{{ route('kategorie.destroy', $k->id_kategorie) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Smazat</button>
                            </form>
                        </td>
                    </tr>
                    {{-- Rekurzivní zobrazení subkategorií --}}
                    @include('kategorie.partials.managesubs', ['subcategories' => $k->subcategories, 'depth' => 1])
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection
