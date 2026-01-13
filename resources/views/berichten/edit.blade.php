@extends('layouts.app')

@section('title', 'Bericht Bewerken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-envelope me-3"></i>Bericht Bewerken</h1>
        <p class="mb-0 mt-2 opacity-90">Bewerk dit bericht</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('berichten.update', $message->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="receiver_id" class="form-label">
                                <i class="fas fa-user me-2"></i>Ontvanger
                            </label>
                            <select class="form-select @error('receiver_id') is-invalid @enderror" id="receiver_id" name="receiver_id">
                                <option value="">Allen (Algemeen bericht)</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('receiver_id', $message->receiver_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Laat leeg voor een algemeen bericht aan iedereen</small>
                            @if($errors->has('receiver_id'))
                                <div class="invalid-feedback">{{ $errors->first('receiver_id') }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">
                                <i class="fas fa-tag me-2"></i>Onderwerp <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" 
                                   value="{{ old('subject', $message->subject) }}" 
                                   placeholder="Onderwerp van het bericht..." required>
                            @if($errors->has('subject'))
                                <div class="invalid-feedback">{{ $errors->first('subject') }}</div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Bericht <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="8" 
                                      placeholder="Typ hier uw bericht..." required>{{ old('content', $message->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Bijwerken
                            </button>
                            <a href="{{ route('berichten.index') }}" class="btn btn-secondary">
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