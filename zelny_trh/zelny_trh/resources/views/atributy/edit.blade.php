@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Upravit atribut: {{ $atribut->nazev }}</h1>

    <form action="{{ route('atributy.update', $atribut->id_atributu) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nazev">Název atributu <span style="color: red;">*</label>
            <input type="text" name="nazev" id="nazev" class="form-control" value="{{ old('nazev', $atribut->nazev) }}" required>
            @error('nazev')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

        </div>

        <h3>Hodnoty atributu <span style="color: red;">*</h3>
        <div id="values-container">
            @foreach($hodnoty as $index => $hodnota)
                <div class="form-group">
                    <label for="hodnoty[{{ $index }}]">Hodnota atributu</label>
                    <input type="text" name="hodnoty[{{ $index }}]" class="form-control" value="{{ old('hodnoty.' . $index, $hodnota->hodnota) }}" required>
                    @error('hodnoty.' . $index)
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <!-- Přidání tlačítka pro smazání -->
                    <button type="button" class="btn btn-danger btn-sm" data-id="{{ $hodnota->id_hodnoty }}" onclick="deleteValue(this)">Smazat hodnotu</button>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-primary" id="add-value">Přidat hodnotu</button>
        <button type="submit" class="btn btn-primary">Uložit změny</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
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
    let valueIndex = {{ $hodnoty->count() }};
    const container = document.getElementById('values-container');
    const template = document.getElementById('value-template').innerHTML;

    document.getElementById('add-value').addEventListener('click', function () {
        const newValue = template.replace(/INDEX/g, valueIndex++);
        container.insertAdjacentHTML('beforeend', newValue);
    });

    // Funkce pro smazání hodnoty
    window.deleteValue = function(button) {
        const valueId = button.getAttribute('data-id');
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'deleted_hodnoty[]';  // Pole pro smazané hodnoty
        hiddenField.value = valueId;
        button.closest('.form-group').appendChild(hiddenField); // Přidá skrývané pole do formuláře
        button.closest('.form-group').remove(); // Odstraní příslušnou hodnotu z formuláře
    }
});

</script>
@endsection
