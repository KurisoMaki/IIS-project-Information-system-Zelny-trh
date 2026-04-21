@extends('layouts.app')

@section('content')
<div class="container">
    @if(isset($id_nabidky))
        <h1>Hodnocení nabídky: {{ $hodnoceni->first()->nabidka->nazev ?? 'Neznámá nabídka' }}</h1>
    @else
        <h1>Všechna hodnocení</h1>
    @endif

    <!-- Success message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table of reviews -->
    <table class="table table-striped">
        <thead>
            <tr>
                {{-- <th>#</th> --}}
                <th>Nabídka</th>
                <th>Zákazník</th>
                <th>Hodnocení</th>
                <th>Komentář</th>
                <th>Datum hodnocení</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hodnoceni as $item)
                <tr>
                    {{-- <td>{{ $item->id_hodnoceni }}</td> --}}
                    <td>{{ $item->nabidka->nazev }}</td>
                    <td>{{ $item->zakaznik }}</td>
                    <td>{{ $item->hodnoceni }}</td>
                    <td>{{ $item->komentar }}</td>
                    <td>{{ $item->datum_hodnoceni }}</td>
                    <td>
                        <a href="{{ route('hodnoceni.show', $item->id_hodnoceni) }}" class="btn btn-info btn-sm">Zobrazit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!isset($id_nabidky))
        {{-- <a href="{{ route('hodnoceni.create') }}" class="btn btn-primary">Přidat hodnocení</a> --}}
    @else
        <a href="{{ route('hodnoceni.create', ['nabidka' => $id_nabidky]) }}" 
            class="btn btn-primary">Ohodnotit</a>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
    @endif
</div>
@endsection
