@extends('layouts.app')

@section('title', 'Bericht bekijken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-envelope me-3"></i>Bericht</h1>
        <p class="mb-0 mt-2 opacity-90">Bekijk uw bericht</p>
    </div>
</div>

<div class="container">
    <div class="card">
        <div class="card-body">
            <h2 class="h4 mb-3">{{ $message->subject }}</h2>
            <p class="text-muted mb-2">
                <strong><i class="fas fa-user me-1"></i>Van:</strong> {{ $message->sender->name ?? 'Onbekend' }}
            </p>
            <p class="text-muted mb-2">
                <strong><i class="fas fa-user me-1"></i>Aan:</strong> {{ $message->receiver ? $message->receiver->name : 'Allen' }}
            </p>
            <p class="text-muted mb-4">
                <strong><i class="fas fa-calendar me-1"></i>Datum:</strong> {{ $message->created_at->format('d-m-Y H:i') }}
            </p>
            <div class="border-top pt-3">
                <p class="mb-0">{{ $message->content }}</p>
            </div>
            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('patient.messages') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Terug naar overzicht
                </a>
                @if($message->sender_id === auth()->id())
                    <a href="{{ route('patient.messages.edit', $message->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Bewerken
                    </a>
                @endif
                <form action="{{ route('patient.messages.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Weet u zeker dat u dit bericht wilt verwijderen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Verwijderen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
