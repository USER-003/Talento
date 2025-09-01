@extends('layouts.app')
@section('title', 'Crear factura')
@section('content')
<div class="container py-4">
  <h3 class="mb-3">Crear factura</h3>
  <form method="POST" action="{{ route('invoices.store') }}" class="card p-3">
    @csrf
    <div class="mb-3">
      <label for="client_id" class="form-label">Cliente</label>
      <select id="client_id" name="client_id" class="form-select" required>
        <option value="">Seleccione…</option>
        @foreach($clients as $c)
          <option value="{{ $c->id_usuario }}" {{ ($clientId ?? null) == $c->id_usuario ? 'selected' : '' }}>{{ $c->nombre }} ({{ $c->email }})</option>
        @endforeach
      </select>
      @error('client_id')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label for="servicio_id" class="form-label">Servicio (opcional)</label>
      <select id="servicio_id" name="servicio_id" class="form-select">
        <option value="">N/A</option>
        @foreach($services as $s)
          <option value="{{ $s->id_servicios_personales }}">{{ $s->nombre_servicio }} - ${{ number_format($s->precio,2) }}</option>
        @endforeach
      </select>
      @error('servicio_id')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="row">
      <div class="col-md-4 mb-3">
        <label for="amount" class="form-label">Monto</label>
        <input id="amount" type="number" name="amount" step="0.01" min="0.5" class="form-control" required value="{{ old('amount') }}">
        @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-2 mb-3">
        <label for="currency" class="form-label">Moneda</label>
        <input id="currency" type="text" name="currency" class="form-control" value="{{ old('currency','USD') }}" required>
        @error('currency')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6 mb-3">
        <label for="due_date" class="form-label">Vence (opcional)</label>
        <input id="due_date" type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
        @error('due_date')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Descripción</label>
      <input id="description" type="text" name="description" class="form-control" value="{{ old('description') }}">
      @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div>
      <button class="btn btn-primary">Guardar</button>
      <a class="btn btn-outline-secondary" href="{{ route('invoices.index') }}">Cancelar</a>
    </div>
  </form>
</div>
@endsection
