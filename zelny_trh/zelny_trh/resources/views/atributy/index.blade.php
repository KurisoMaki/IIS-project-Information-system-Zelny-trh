@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Seznam atributů</h1>
    
    <a href="{{ route('atributy.create') }}" class="btn btn-primary mb-3">Vytvořit nový atribut</a>

    @if ($atributy->isEmpty())
        <p>Žádné atributy nejsou k dispozici.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Název atributu</th>
                    <th>Hodnoty</th>
                    <th>Operace</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($atributy as $atribut)
                    <tr>
                        <td>{{ $atribut->nazev }}</td>
                        <td>
                            @foreach ($atribut->hodnoty as $hodnota)
                                <p>{{ $hodnota->hodnota }}</p>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('atributy.show', $atribut->id_atributu) }}" class="btn btn-info btn-sm">Zobrazit</a>
                            <a href="{{ route('atributy.edit', $atribut->id_atributu) }}" class="btn btn-warning btn-sm">Upravit</a>
                            <form action="{{ route('atributy.destroy', $atribut->id_atributu) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Opravdu chcete smazat tento atribut?')">Smazat</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
