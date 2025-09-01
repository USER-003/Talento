<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Usuario;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id_usuario;
        $invoices = Invoice::with(['client', 'servicio'])
            ->where('provider_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $userId = Auth::user()->id_usuario;
        $clientId = (int) $request->query('client_id');

        // Only users with whom I have a conversation
        $conversations = \App\Models\Conversation::where('user_one', $userId)
            ->orWhere('user_two', $userId)
            ->get(['user_one','user_two']);

        $otherUserIds = $conversations->map(function($c) use ($userId) {
            return $c->user_one === $userId ? $c->user_two : $c->user_one;
        })->unique()->values();
        // Exclude blocked relationships (either direction)
        $blockedIds = \App\Models\BlockedUser::where('blocker_id', $userId)->pluck('blocked_id');
        $blockedByIds = \App\Models\BlockedUser::where('blocked_id', $userId)->pluck('blocker_id');
        $excluded = $blockedIds->merge($blockedByIds)->unique();
        $allowedUserIds = $otherUserIds->reject(function ($id) use ($excluded) {
            return $excluded->contains($id);
        })->values();

        $clients = Usuario::whereIn('id_usuario', $allowedUserIds)->get();
        $services = Servicio::where('id_usuario', $userId)->get();
        return view('invoices.create', compact('clients', 'services', 'clientId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:usuarios,id_usuario',
            'servicio_id' => 'nullable|exists:servicios_personales,id_servicios_personales',
            'amount' => 'required|numeric|min:0.5',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        $invoice = Invoice::create([
            'provider_id' => Auth::user()->id_usuario,
            'client_id' => $data['client_id'],
            'servicio_id' => $data['servicio_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => strtoupper($data['currency']),
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('invoices.show', $invoice)->with('status', 'Factura creada');
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeView($invoice);
        $invoice->load(['client', 'provider', 'servicio']);
        return view('invoices.show', compact('invoice'));
    }

    public function pay(Invoice $invoice)
    {
        $this->authorizeView($invoice);
        // Placeholder: integrate Stripe Checkout here if desired.
        return back()->with('status', 'Integración de pago pendiente de configuración');
    }

    private function authorizeView(Invoice $invoice): void
    {
        $userId = Auth::user()->id_usuario;
        if ($invoice->provider_id !== $userId && $invoice->client_id !== $userId) {
            abort(403);
        }
    }
}
