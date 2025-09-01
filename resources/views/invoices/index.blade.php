@extends('layouts.app')
@section('title', 'Ingresos')
@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Ingresos</h3>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">Crear factura</a>
  </div>
  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>#</th><th>Cliente</th><th>Servicio</th><th>Monto</th><th>Estado</th><th>Creada</th><th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td>{{ $inv->id }}</td>
              <td>{{ $inv->client->nombre ?? 'N/A' }}</td>
              <td>{{ $inv->servicio->nombre_servicio ?? 'N/A' }}</td>
              <td>{{ $inv->currency }} {{ number_format($inv->amount, 2) }}</td>
              <td><span class="badge bg-{{ $inv->status === 'paid' ? 'success' : ($inv->status === 'canceled' ? 'secondary' : 'warning') }}">{{ $inv->status }}</span></td>
              <td>{{ $inv->created_at->format('Y-m-d') }}</td>
              <td><a class="btn btn-sm btn-outline-primary" href="{{ route('invoices.show', $inv) }}">Ver</a></td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">Sin facturas aún.</td></tr>
          @endforelse
        </tbody>
      </table>
      <div>{{ $invoices->links() }}</div>
    </div>
  </div>
  </div>
@endsection
