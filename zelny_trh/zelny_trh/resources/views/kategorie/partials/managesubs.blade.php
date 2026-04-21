@foreach($subcategories as $subcategory)
<tr>
    {{-- <td>{{ $subcategory->id_kategorie }}</td> --}}
    <td>{!! str_repeat('&nbsp;', $depth * 4) !!}— {{ $subcategory->nazev }}</td>
    <td>{{ $subcategory->popis }}</td>
    <td>{{ $subcategory->parentCategory ? $subcategory->parentCategory->nazev : 'Žádná' }}</td>
    <td>
        <a href="{{ route('kategorie.show', $subcategory->id_kategorie) }}" class="btn btn-info btn-sm">Zobrazit</a>
        <a href="{{ route('kategorie.edit', $subcategory->id_kategorie) }}" class="btn btn-warning btn-sm">Upravit</a>
        <form action="{{ route('kategorie.destroy', $subcategory->id_kategorie) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Smazat</button>
        </form>
    </td>
</tr>
{{-- Rekurzivní zobrazení subkategorií --}}
@include('kategorie.partials.managesubs', ['subcategories' => $subcategory->subcategories, 'depth' => $depth + 1])
@endforeach
