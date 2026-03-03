@extends('layouts.app')

@section('title', 'Bericht bewerken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-edit me-3"></i>Bericht bewerken</h1>
        <p class="mb-0 mt-2 opacity-90">Bewerk uw verzonden bericht</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('patient.messages.update', $message->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user me-2"></i>Ontvanger
                            </label>
                            @if($message->is_read)
                                <div class="form-control bg-light" readonly>
                                    {{ $message->receiver ? $message->receiver->name . ' (' . $message->receiver->email . ')' : 'Allen (Algemeen bericht)' }}
                                </div>
                                <input type="hidden" name="receiver_id" value="{{ $message->receiver_id }}">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>De ontvanger kan niet meer worden gewijzigd omdat het bericht al gelezen is.
                                </small>
                            @else
                                <select class="form-select @error('receiver_id') is-invalid @enderror" id="receiver_id" name="receiver_id">
                                    <option value="">Allen (Algemeen bericht)</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('receiver_id', $message->receiver_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('receiver_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">
                                <i class="fas fa-tag me-2"></i>Onderwerp <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                   id="subject" name="subject"
                                   value="{{ old('subject', $message->subject) }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Bericht <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="8" required>{{ old('content', $message->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Opslaan
                            </button>
                            <a href="{{ route('patient.messages') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuleren
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
