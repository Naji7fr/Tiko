@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Medewerker Details</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $medewerker->naam }}</h5>
                <p class="card-text"><strong>Email:</strong> {{ $medewerker->email }}</p>
                <p class="card-text"><strong>Status:</strong> {{ $medewerker->status }}</p>
                <p class="card-text"><strong>Type:</strong> {{ $medewerker->type }}</p>
                <a href="{{ route('medewerkers.edit', $medewerker->id) }}" class="btn btn-warning">Bewerken</a>
                <a href="{{ route('medewerkers.index') }}" class="btn btn-secondary">Terug</a>
            </div>
        </div>
    </div>
@endsection

