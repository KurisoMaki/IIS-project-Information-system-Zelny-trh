@extends('layouts.app')

@section('content')
<div class="container">

    <style>
        .readonly-input {
            background-color: #e9ecef; /* Light gray background */
            opacity: 0.7; /* Slightly transparent */
            cursor: not-allowed; /* Change cursor to indicate non-editable */
        }
    </style>

    <h2>Edit Profile</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf

        <!-- Username -->
        <div class="mb-3">
            <label for="username" class="form-label">Přezdívka</label>
            <input id="username" type="text" class="form-control readonly-input @error('username') is-invalid @enderror" 
                   name="username" value="{{ old('username', $user->prihlasovaci_jmeno) }}" readonly>
            @error('username')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Jméno <span style="color: red;">*</span></label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                   name="name" value="{{ old('name', $user->jmeno) }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Mailová adresa <span style="color: red;">*</span></label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Birthday -->
        <div class="mb-3">
            <label for="birthday" class="form-label">Datum narození</label>
            <input id="birthday" type="date" class="form-control @error('birthday') is-invalid @enderror" 
                    name="birthday" value="{{ old('birthday', $user->datum_narozeni ? $user->datum_narozeni->format('Y-m-d') : '') }}">
            @error('birthday')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Address -->
        <div class="mb-3">
            <label for="address" class="form-label">Adresa</label>
            <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" 
                   name="address" value="{{ old('address', $user->adresa) }}">
            @error('address')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Additional Info -->
        <div class="mb-3">
            <label for="additional_info" class="form-label">Další informace</label>
            <textarea id="additional_info" class="form-control @error('additional_info') is-invalid @enderror" 
                      name="additional_info">{{ old('additional_info', $user->dalsi_osobni_udaje) }}</textarea>
            @error('additional_info')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Password (optional) -->
        <div class="mb-3">
            <label for="password" class="form-label">Nové heslo</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password-confirm" class="form-label">Potvrdit heslo</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" 
                   autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary">Aktualizovat profil</button>
    </form>
</div>
@endsection
