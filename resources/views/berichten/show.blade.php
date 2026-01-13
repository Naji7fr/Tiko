@extends('layouts.app')

@section('title', 'Bericht bekijken')

@section('content')
<div class="container">
    <h1>{{ $message->subject }}</h1>
    <p><strong>Afzender:</strong> {{ $message->sender->name ?? 'Onbekend' }}</p>
    <p><strong>Ontvanger:</strong> {{ $message->receiver->name ?? 'Allen' }}</p>
    <p><strong>Datum:</strong> {{ $message->created_at->format('d-m-Y H:i') }}</p>
    <div class="mt-3">
        <p>{{ $message->content }}</p>
    </div>
    <a href="{{ route('berichten.index') }}" class="btn btn-secondary">Terug</a>
</div>
@endsection