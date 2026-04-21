@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Upravit Kategorii</h1>

    <form action="{{ route('kategorie.update', $kategorie->id_kategorie) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nazev">Název <span style="color: red;">*</span></label>
            <input type="text" class="form-control" id="nazev" name="nazev" value="{{ $kategorie->nazev }}" required>
        </div>

        <div class="form-group">
            <label for="popis">Popis</label>
            <textarea class="form-control" id="popis" name="popis">{{ $kategorie->popis }}</textarea>
        </div>

        <div class="form-group">
            <label for="parent">Rodičovská Kategorie</label>
            <select class="form-control" id="parent" name="parent">
                <option value="">Žádná</option>
                @foreach($parentCategories as $parentCategory)
                    <option value="{{ $parentCategory->id_kategorie }}" {{ $parentCategory->id_kategorie == $kategorie->parent ? 'selected' : '' }}>
                        {{ $parentCategory->nazev }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="foto">Fotografie</label>
            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
            @if($kategorie->foto)
                <img src="{{ asset('storage/' . $kategorie->foto) }}" alt="Current Foto" width="100">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Uložit Změny</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Zpět</a>
    </form>
</div>
@endsection
