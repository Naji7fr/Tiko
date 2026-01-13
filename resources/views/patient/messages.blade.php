@extends('layouts.app')

@section('title', 'Mijn Berichten')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-envelope me-3"></i>Mijn Berichten</h1>
        <p class="mb-0 mt-2 opacity-90">Bekijk en stuur berichten</p>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Mijn Berichten</h2>
            <p class="text-muted mb-0">Totaal: {{ $messages->count() }} berichten</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendMessageModal">
            <i class="fas fa-plus me-2"></i>Nieuw Bericht
        </button>
    </div>

    @if($messages->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen berichten gevonden</h4>
                <p class="text-muted mb-4">U heeft nog geen berichten ontvangen.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendMessageModal">
                    <i class="fas fa-plus me-2"></i>Stuur eerste bericht
                </button>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($messages as $message)
                        <div class="list-group-item {{ !$message->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @if(!$message->is_read)
                                            <i class="fas fa-circle text-primary me-2" style="font-size: 0.5rem;"></i>
                                        @endif
                                        <h6 class="mb-0">{{ $message->subject }}</h6>
                                    </div>
                                    <p class="text-muted mb-2">{{ Str::limit($message->content, 100) }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>Van: {{ $message->sender->name ?? 'Onbekend' }} | 
                                        <i class="fas fa-calendar me-1"></i>{{ $message->created_at->format('d-m-Y H:i') }}
                                    </small>
                                </div>
                                <a href="{{ route('berichten.show', $message->id) }}" class="btn btn-sm btn-outline-primary ms-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Send Message Modal -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendMessageModalLabel">Nieuw Bericht</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('patient.messages.send') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Onderwerp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Bericht <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Versturen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

