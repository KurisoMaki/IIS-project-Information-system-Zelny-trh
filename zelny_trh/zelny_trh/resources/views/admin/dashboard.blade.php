@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Admin Dashboard</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.uzivatele.manage') }}" class="btn btn-primary">Spravovat uživatele</a>
    <a href="{{ route('admin.kategorie.manage') }}" class="btn btn-primary">Spravovat kategorie</a>
    <a href="{{ route('admin.nabidky.manage') }}" class="btn btn-primary">Spravovat nabidky</a>
    <a href="{{ route('admin.atributy.manage') }}" class="btn btn-primary">Spravovat atributy</a>
    <a href="{{ route('admin.objednavky.manage') }}" class="btn btn-primary">Spravovat objednavky</a>
    <a href="{{ route('admin.hodnoceni.manage') }}" class="btn btn-primary">Spravovat hodnoceni</a>

    <div class="card">
        <div class="card-header">Schvalovani kategorii</div>
        <div class="card-body">
            @if($kategorie->count())
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Approved</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kategorie as $kategorieItem)
                            <tr>
                                <td>{{ $kategorieItem->id_kategorie }}</td>
                                <td>{{ $kategorieItem->nazev }}</td>
                                <td>{{ $kategorieItem->popis }}</td>
                                <td>{{ $kategorieItem->schvaleno }}</td>
                                <td>
                                <form action="{{ route('admin.updateCategoryStatus', $kategorieItem->id_kategorie) }}" method="POST">
                                    @csrf
                                    <select name="schvaleno" class="form-select">
                                        <option value="ANO" @if($kategorieItem->schvaleno === 'ANO') selected @endif>ANO</option>
                                        <option value="NE" @if($kategorieItem->schvaleno === 'NE') selected @endif>NE</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                                </form>
                                <a href="{{ route('kategorie.show', $kategorieItem->id_kategorie) }}" class="btn btn-secondary mt-2">Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No categories found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
