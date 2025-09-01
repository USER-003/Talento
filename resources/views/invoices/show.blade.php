@extends('layouts.app')
@section('title', 'Factura #'.$invoice->id)
@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Factura #{{ $invoice->id }}</h3>
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Volver</a>
  </div>
  @if(session('status'))
    <div class="alert alert-info">{{ session('status') }}</div>
  @endif
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <h5>Proveedor</h5>
          <p class="mb-1">{{ $invoice->provider->nombre }}</p>
          <small class="text-muted">{{ $invoice->provider->email }}</small>
        </div>
        <div class="col-md-6">
          <h5>Cliente</h5>
          <p class="mb-1">{{ $invoice->client->nombre }}</p>
          <small class="text-muted">{{ $invoice->client->email }}</small>
        </div>
      </div>
      <hr>
      <p><strong>Servicio:</strong> {{ $invoice->servicio->nombre_servicio ?? 'N/A' }}</p>
      <p><strong>Monto:</strong> {{ $invoice->currency }} {{ number_format($invoice->amount,2) }}</p>
      <p><strong>Estado:</strong> <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'canceled' ? 'secondary' : 'warning') }}">{{ $invoice->status }}</span></p>
      @if($invoice->due_date)
      <p><strong>Vence:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') }}</p>
      @endif
      @if($invoice->description)
      <p><strong>Descripción:</strong> {{ $invoice->description }}</p>
      @endif
      <form method="POST" action="{{ route('invoices.pay', $invoice) }}" class="mt-3">
        @csrf
        <button class="btn btn-success" {{ $invoice->status === 'paid' ? 'disabled' : '' }}>Pagar</button>
      </form>
    </div>
  </div>
</div>
@endsection
