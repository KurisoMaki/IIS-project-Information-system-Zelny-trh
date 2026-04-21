@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Všichni uživatelé</h1>
    <a href="{{ route('uzivatele.create') }}" class="btn btn-primary mb-3">Vytvořit nového uživatele</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Přihlašovací jméno</th>
                <th>Jméno</th>
                <th>Role</th>
                <th>Email</th>
                <th>Datum narození</th>
                <th>Adresa</th>
                <th>Další údaje</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>
                        <a href="{{ route('uzivatele.show', $user->prihlasovaci_jmeno) }}">{{ $user->prihlasovaci_jmeno }}</a>
                    </td>
                    <td>{{ $user->jmeno }}</td>
                    <td>{{ $user->urole }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->datum_narozeni ?? '-' }}</td>
                    <td>{{ $user->adresa ?? '-' }}</td>
                    <td>{{ $user->dalsi_osobni_udaje ?? '-' }}</td>
                    <td>
                        <a href="{{ route('uzivatele.show', $user->prihlasovaci_jmeno) }}" class="btn btn-info btn-sm">Zobrazit</a>
                        <a href="{{ route('uzivatele.edit', $user->prihlasovaci_jmeno) }}" class="btn btn-warning btn-sm">Upravit</a>
                        <form action="{{ route('uzivatele.destroy', $user->prihlasovaci_jmeno) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Opravdu chcete tohoto uživatele smazat?')">Smazat</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
