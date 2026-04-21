@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Upravit uživatele: {{ $user->jmeno }}</h1>

    <form action="{{ route('uzivatele.update', $user->prihlasovaci_jmeno) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="jmeno">Jméno <span style="color: red;">*</span></label>
            <input type="text" class="form-control" id="jmeno" name="jmeno" value="{{ $user->jmeno }}" required>
        </div>

        <div class="form-group">
            <label for="urole">Úloha <span style="color: red;">*</span></label>
            <select class="form-control" id="urole" name="urole" required>
                <option value="Admin" {{ $user->urole == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Moderator" {{ $user->urole == 'Moderator' ? 'selected' : '' }}>Moderator</option>
                <option value="Farmar" {{ $user->urole == 'Farmar' ? 'selected' : '' }}>Farmar</option>
                <option value="Zakaznik" {{ $user->urole == 'Zakaznik' ? 'selected' : '' }}>Zakaznik</option>
            </select>
        </div>

        <div class="form-group">
            <label for="email">Mailová adresa <span style="color: red;">*</span></label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>

        <div class="form-group">
            <label for="datum_narozeni">Datum narození</label>
            <input type="date" class="form-control" id="datum_narozeni" name="datum_narozeni" value="{{ old('datum_narozeni', $user->datum_narozeni ? $user->datum_narozeni->format('Y-m-d') : '') }}">
        </div>

        <div class="form-group">
            <label for="adresa">Adresa</label>
            <input type="text" class="form-control" id="adresa" name="adresa" value="{{ $user->adresa }}">
        </div>

        <div class="form-group">
            <label for="dalsi_osobni_udaje">Další osobní údaje</label>
            <input type="text" class="form-control" id="dalsi_osobni_udaje" name="dalsi_osobni_udaje" value="{{ $user->dalsi_osobni_udaje }}">
        </div>

        <button type="submit" class="btn btn-success">Uložit změny</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
    </form>
</div>
@endsection
