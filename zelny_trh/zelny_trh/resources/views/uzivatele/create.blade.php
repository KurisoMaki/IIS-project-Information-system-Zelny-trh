@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Přidat uživatele</h1>

    <form action="{{ route('uzivatele.store') }}" method="POST" id="userForm" novalidate>
        @csrf

        <div class="form-group">
            <label for="prihlasovaci_jmeno">Přihlašovací jméno <span style="color: red;">*</span></label>
            <input type="text" class="form-control" id="prihlasovaci_jmeno" name="prihlasovaci_jmeno" value="{{ old('prihlasovaci_jmeno') }}" required maxlength="100">
            <small class="text-danger">{{ $errors->first('prihlasovaci_jmeno') }}</small>
        </div>

        <div class="form-group">
            <label for="heslo">Heslo <span style="color: red;">*</span></label>
            <div class="input-group">
                <input type="password" class="form-control" id="heslo" name="heslo" value="{{ old('heslo') }}" required minlength="8">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">👁️</button>
            </div>
            <small class="text-danger">{{ $errors->first('heslo') }}</small>
        </div>

        <div class="form-group">
            <label for="jmeno">Jméno <span style="color: red;">*</span></label>
            <input type="text" class="form-control" id="jmeno" name="jmeno" value="{{ old('jmeno') }}" required maxlength="100">
            <small class="text-danger">{{ $errors->first('jmeno') }}</small>
        </div>

        <div class="form-group">
            <label for="urole">Úloha <span style="color: red;">*</span></label>
            <select class="form-control" id="urole" name="urole" required>
                <option value="Admin" {{ old('urole') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Moderator" {{ old('urole') == 'Moderator' ? 'selected' : '' }}>Moderator</option>
                <option value="Farmar" {{ old('urole') == 'Farmar' ? 'selected' : '' }}>Farmar</option>
                <option value="Zakaznik" {{ old('urole') == 'Zakaznik' ? 'selected' : '' }}>Zakaznik</option>
            </select>
        </div>

        <div class="form-group">
            <label for="email">Mailová adresa <span style="color: red;">*</span></label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required maxlength="100">
            <small class="text-danger">{{ $errors->first('email') }}</small>
        </div>

        <div class="form-group">
            <label for="datum_narozeni">Datum narození</label>
            <input type="date" class="form-control" id="datum_narozeni" name="datum_narozeni" value="{{ old('datum_narozeni') }}">
        </div>

        <div class="form-group">
            <label for="adresa">Adresa</label>
            <input type="text" class="form-control" id="adresa" name="adresa" value="{{ old('adresa') }}">
        </div>

        <div class="form-group">
            <label for="dalsi_osobni_udaje">Další osobní údaje</label>
            <input type="text" class="form-control" id="dalsi_osobni_udaje" name="dalsi_osobni_udaje" value="{{ old('dalsi_osobni_udaje') }}">
        </div>

        <button type="submit" class="btn btn-success">Přidat uživatele</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
    </form>
</div>

<script>
function togglePasswordVisibility() {
    const passwordField = document.getElementById("heslo");
    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
    passwordField.setAttribute("type", type);
}
</script>
@endsection
