@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Vytvořit nový atribut</h1>

    <form action="{{ route('atributy.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nazev">Název atributu <span style="color: red;">*</label>
            <input type="text" name="nazev" id="nazev" class="form-control" value="{{ old('nazev') }}" required>
            @error('nazev')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <h3>Hodnoty atributu <span style="color: red;">*</h3>
        <div id="values-container">
            <div class="form-group">
                <label for="hodnoty[0]">Hodnota atributu</label>
                <input type="text" name="hodnoty[0]" class="form-control" required>
                @error('hodnoty.0')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="footer">
            <button type="button" class="btn btn-primary" id="add-value">Přidat hodnotu</button>
            <button type="submit" class="btn btn-primary">Vytvořit atribut</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
        </div>
    </form>
</div>

<!-- Template for a new value input -->
<template id="value-template">
    <div class="form-group">
        <label for="hodnoty[INDEX]">Hodnota atributu</label>
        <input type="text" name="hodnoty[INDEX]" class="form-control" required>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let valueIndex = 1; // Start with the second value input (index 1)
        const container = document.getElementById('values-container');
        const template = document.getElementById('value-template').innerHTML;

        document.getElementById('add-value').addEventListener('click', function () {
            const newValue = template.replace(/INDEX/g, valueIndex++);
            container.insertAdjacentHTML('beforeend', newValue);
        });
    });
</script>
@endsection
