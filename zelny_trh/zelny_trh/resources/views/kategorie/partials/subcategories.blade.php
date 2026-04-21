@foreach($subcategories as $subcategory)
<tr>
    {{-- <td>{{ $subcategory->id_kategorie }}</td> --}}
    <td>{!! str_repeat('&nbsp;', $depth * 4) !!}— {{ $subcategory->nazev }}</td>
    <td>{{ $subcategory->popis }}</td>
    <td>{{ $subcategory->parentCategory ? $subcategory->parentCategory->nazev : 'Žádná' }}</td>
    <td>
        <a href="{{ route('kategorie.show', $subcategory->id_kategorie) }}" class="btn btn-info btn-sm">Zobrazit</a>
    </td>
</tr>
{{-- Rekurzivní zobrazení subkategorií --}}
@include('kategorie.partials.subcategories', ['subcategories' => $subcategory->subcategories, 'depth' => $depth + 1])
@endforeach
