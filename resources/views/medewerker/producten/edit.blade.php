@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product wijzigen</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('producten.update', ['id' => $product->id]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Naam</label>
            <input type="text" name="naam" class="form-control" value="{{ old('naam', $product->naam) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Categorie</label>
            <select name="categorie_id" class="form-control" required>
                <option value="">-- Kies categorie --</option>
                @foreach($categorieen as $c)
                    <option value="{{ $c->id }}" @if($product->categorie_id == $c->id) selected @endif>{{ $c->naam }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Leverancier</label>
            <select name="leverancier_id" class="form-control" required>
                <option value="">-- Kies leverancier --</option>
                @foreach($leveranciers as $l)
                    <option value="{{ $l->id }}" @if($product->leverancier_id == $l->id) selected @endif>{{ $l->bedrijfsnaam }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Prijs</label>
            <input type="number" step="0.01" name="prijs" class="form-control" value="{{ old('prijs', $product->prijs) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Beschrijving</label>
            <textarea name="beschrijving" class="form-control">{{ old('beschrijving', $product->beschrijving) }}</textarea>
        </div>

        <button class="btn btn-primary">Opslaan</button>
        <a href="{{ route('producten.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection
