@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <picture>
        <source srcset="{{ asset('images/hero_cropped.webp') }}" type="image/webp">
        <source srcset="{{ asset('images/hero_cropped.jpg') }}" type="image/jpeg">
        <img src="{{ asset('images/hero_cropped.jpg') }}" alt="Fresh produce at the farmers' market" class="hero-image">
    </picture>
    <style>
    .hero-image {
        width: 100%;
        height: auto;
        object-fit: cover;
    }
    </style>

    <!-- Categories Section -->
    <h2 class="mb-4">Kategorie</h2>
    <div class="row">
        @foreach($kategorie as $kategorieItem)
            <div class="col-md-4">
                <div class="card mb-4">
                    <!-- If you have images for categories -->
                    @if($kategorieItem->image)
                        <img src="{{ asset('images/categories/' . $kategorieItem->image) }}" class="card-img-top" alt="{{ $kategorieItem->nazev }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $kategorieItem->nazev }}</h5>
                        <p class="card-text">{{ $kategorieItem->popis }}</p>
                        <a href="{{ route('kategorie.show', $kategorieItem->id_kategorie) }}" class="btn btn-success">Zobrazit kategorii</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Offers Section -->
    <h2 class="mb-4">Nabídky</h2>
    <div class="row">
        @foreach($nabidky as $nabidka)
            <div class="col-md-4">
                <div class="card mb-4">
                    <!-- If you have images for offers -->
                    @if($nabidka->image)
                        <img src="{{ asset('images/nabidky/' . $nabidka->image) }}" class="card-img-top" alt="{{ $nabidka->nazev }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $nabidka->nazev }}</h5>
                        <p class="card-text">{{ $nabidka->popis }}</p>
                        <p class="card-text">Cena: {{ $nabidka->cena }} {{ $nabidka->druh_ceny == 'HMOTNOST' ? 'Kč/kg' : 'Kč/kus' }}</p>
                        <p class="card-text">Množství: {{ $nabidka->mnozstvi }}</p>
                        @if($nabidka->samozber)
                            <p class="card-text"><strong>Možnost samozběru</strong></p>
                        @endif
                        <a href="{{ route('objednavky.create', ['id_nabidky' => $nabidka->id_nabidky]) }}" class="btn btn-success">Objednat</a>
                        <a href="{{ route('hodnoceni.create', ['nabidka' => $nabidka->id_nabidky]) }}" 
                            class="btn btn-info">Ohodnotit</a>
                        <a href="{{ route('nabidky.show', $nabidka->id_nabidky) }}" class="btn btn-secondary">Zobrazit detail</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection