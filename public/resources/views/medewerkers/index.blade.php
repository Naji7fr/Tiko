@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Medewerker Overzicht</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($medewerkers->isEmpty())
            <div class="alert alert-info">Er zijn geen medewerkers geregistreerd.</div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medewerkers as $medewerker)
                        <tr>
                            <td>{{ $medewerker->naam }}</td>
                            <td>{{ $medewerker->email }}</td>
                            <td>{{ $medewerker->status }}</td>
                            <td>{{ $medewerker->type }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <a href="{{ route('medewerkers.create') }}" class="btn btn-primary">Medewerker toevoegen</a>
    </div>
@endsection
