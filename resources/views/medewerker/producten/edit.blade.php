@extends('layouts.app')

@section('title', 'Product wijzigen')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-edit me-3"></i>Product wijzigen</h1>
    </div>
</div>

<div class="container py-4">
    @include('medewerker.partials.alerts')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('producten.update', $product->id) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="naam" class="form-label">Productnaam</label>
                    <input id="naam" name="naam" type="text" class="form-control @error('naam') is-invalid @enderror"
                           value="{{ old('naam', $product->naam) }}" required>
                    @error('naam')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="categorie_id" class="form-label">Categorie</label>
                    <select id="categorie_id" name="categorie_id" class="form-select @error('categorie_id') is-invalid @enderror" required>
                        <option value="">Selecteer een categorie</option>
                        @foreach($categorieen as $categorie)
                            <option value="{{ $categorie->id }}" @selected(old('categorie_id', $product->categorie_id) == $categorie->id)>{{ $categorie->naam }}</option>
                        @endforeach
                    </select>
                    @error('categorie_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="ean_code" class="form-label">EAN-code</label>
                    <input id="ean_code" name="ean_code" type="text" class="form-control @error('ean_code') is-invalid @enderror"
                           value="{{ old('ean_code', $product->ean_code) }}" required>
                    @error('ean_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="voorraad" class="form-label">Voorraad</label>
                    <input id="voorraad" name="voorraad" type="number" min="0" class="form-control @error('voorraad') is-invalid @enderror"
                           value="{{ old('voorraad', $product->voorraad ?? 0) }}" required>
                    @error('voorraad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="leverancier_id" class="form-label">Leverancier</label>
                    <select id="leverancier_id" name="leverancier_id" class="form-select @error('leverancier_id') is-invalid @enderror" required>
                        <option value="">Selecteer een leverancier</option>
                        @foreach($leveranciers as $leverancier)
                            <option value="{{ $leverancier->id }}" @selected(old('leverancier_id', $product->leverancier_id) == $leverancier->id)>{{ $leverancier->bedrijfsnaam }}</option>
                        @endforeach
                    </select>
                    @error('leverancier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="beschrijving" class="form-label">Beschrijving (optioneel)</label>
                    <textarea id="beschrijving" name="beschrijving" rows="3" class="form-control">{{ old('beschrijving', $product->beschrijving) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="prijs" class="form-label">Prijs (optioneel)</label>
                    <input id="prijs" name="prijs" type="text" class="form-control @error('prijs') is-invalid @enderror"
                           value="{{ old('prijs', $product->prijs) }}">
                    @error('prijs')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Opslaan</button>
                <a href="{{ route('producten.index') }}" class="btn btn-secondary">Annuleren</a>
            </form>
        </div>
    </div>
</div>
@endsection
