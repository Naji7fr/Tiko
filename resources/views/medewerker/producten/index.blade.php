@extends('layouts.app')

@section('title', 'Product overzicht')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-box me-3"></i>Product overzicht</h1>
        <p class="text-muted">Bekijk alle producten met voorraad, categorie en leverancier.</p>
    </div>
</div>

<div class="container py-4">
    @include('medewerker.partials.alerts')

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('producten.create') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i> Product toevoegen</a>
            </div>
            @if($producten->isEmpty())
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>Er zijn geen producten geregistreerd.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Productnaam</th>
                                <th>Categorie</th>
                                <th>EAN-code</th>
                                <th>Voorraad</th>
                                <th>Leverancier</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($producten as $product)
                                <tr>
                                    <td>{{ $product->product_naam }}</td>
                                    <td>{{ $product->categorie_naam ?? 'Onbekend' }}</td>
                                    <td>{{ $product->ean_code }}</td>
                                    <td>{{ $product->voorraad }}</td>
                                    <td>{{ $product->leverancier_naam ?? 'Onbekend' }}</td>
                                    <td>
                                        <a href="{{ route('producten.edit', $product->id) }}" class="btn btn-sm btn-primary me-1"><i class="fas fa-edit me-1"></i>Bewerken</a>
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                data-delete-trigger
                                                data-delete-url="{{ route('producten.destroy', $product->id) }}"
                                                data-delete-name="{{ $product->product_naam }}">
                                            <i class="fas fa-trash me-1"></i>Verwijderen
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
