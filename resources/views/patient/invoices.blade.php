@extends('layouts.app')

@section('title', 'Mijn Facturen')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-file-invoice-dollar me-3"></i>Mijn Facturen</h1>
        <p class="mb-0 mt-2 opacity-90">Bekijk en betaal uw facturen</p>
    </div>
</div>

<div class="container">
    @php
        $totalAmount = $invoices->sum('amount');
        $paidAmount = $invoices->where('status', 'betaald')->sum('amount');
        $pendingAmount = $invoices->where('status', 'openstaand')->sum('amount');
    @endphp

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Totaal</h6>
                    <h4 class="mb-0">€{{ number_format($totalAmount, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Betaald</h6>
                    <h4 class="mb-0 text-success">€{{ number_format($paidAmount, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Openstaand</h6>
                    <h4 class="mb-0 text-warning">€{{ number_format($pendingAmount, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    @if($invoices->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen facturen gevonden</h4>
                <p class="text-muted mb-0">U heeft momenteel geen facturen.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($invoices as $invoice)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong class="text-primary">€{{ number_format($invoice->amount, 2, ',', '.') }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Vervaldatum:</small><br>
                                    <strong>{{ $invoice->due_date->format('d-m-Y') }}</strong>
                                </div>
                                <div class="col-md-3">
                                    @if($invoice->status == 'betaald')
                                        <span class="badge bg-success">Betaald</span>
                                    @elseif($invoice->status == 'vervallen')
                                        <span class="badge bg-danger">Vervallen</span>
                                    @else
                                        <span class="badge bg-warning">Openstaand</span>
                                    @endif
                                </div>
                                <div class="col-md-3 text-end">
                                    @if($invoice->status == 'openstaand' || $invoice->status == 'vervallen')
                                        <button class="btn btn-sm btn-primary" onclick="payInvoice({{ $invoice->id }})">
                                            <i class="fas fa-credit-card me-1"></i>Betalen
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @if($invoice->description)
                                <div class="mt-2">
                                    <small class="text-muted">{{ $invoice->description }}</small>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function payInvoice(invoiceId) {
    if (confirm('Weet u zeker dat u deze factuur wilt betalen?')) {
        // Here you would typically integrate with a payment gateway
        alert('Betalingsfunctionaliteit wordt binnenkort toegevoegd.');
    }
}
</script>
@endsection

