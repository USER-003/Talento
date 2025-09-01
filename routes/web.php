<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CategoriaDeServicioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\InvoiceController;
use App\Http\Middleware\AuthM;


// Navb routes
Route::get('/', [CategoriaDeServicioController::class, 'index'])->name('inicio');
Route::get('/tecnología', [CategoriaDeServicioController::class, 'tech'])->name('categoriaTech');
Route::get('/legales', [CategoriaDeServicioController::class, 'leyes'])->name('categoriaLeyes');
Route::get('/educación', [CategoriaDeServicioController::class, 'educacion'])->name('categoriaEducacion');
Route::get('/negocios', [CategoriaDeServicioController::class, 'negocios'])->name('categoriaNegocios');

Route::get('servicios', [ServicioController::class, 'category'])->name('category');
Route::get('informacion{servicio}', [ServicioController::class, 'info'])->name('servicio.info');



Route::get('/#')->name('#inicio');
Route::get('/#nosotros')->name('nosotros');
Route::get('/#testimonios')->name('testimonios');
Route::get('/#explorar')->name('todos');
Route::get('/#tech')->name('tech');
Route::get('/#negocios')->name('negocios');
Route::get('/#educacion')->name('educacion');
Route::get('/#leyes')->name('leyes');


//Route::get('/')
Route::middleware(AuthM::class)->group(function(){
    Route::resource('servicio', ServicioController::class);
    Route::post('informacion{servicio}/comentar', [ServicioController::class, 'comentar'])->name('servicio.comentar');
    
    // Chat routes
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/{userId}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('chat/mark-read/{messageId}', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
    Route::post('chat/block/{userId}', [ChatController::class, 'blockUser'])->name('chat.block');
    Route::post('chat/unblock/{userId}', [ChatController::class, 'unblockUser'])->name('chat.unblock');
    Route::get('chat/status/{userId}', [ChatController::class, 'getUserStatus'])->name('chat.status');
    Route::post('chat/update-status', [ChatController::class, 'updateOnlineStatus'])->name('chat.update-status');
    
    // API routes for AJAX
    Route::get('api/conversations', [ChatController::class, 'getConversations'])->name('chat.conversations');
    Route::get('api/messages/{userId}', [ChatController::class, 'getMessages'])->name('chat.messages');

    // Invoices (Ingresos)
    Route::get('ingresos', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('ingresos/crear', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('ingresos', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('ingresos/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('ingresos/{invoice}/pagar', [InvoiceController::class, 'pay'])->name('invoices.pay');
});

// Routes auth
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('registrarse', [AuthController::class, 'register'])->name('registrarse');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('iniciar', [AuthController::class, 'login'])->name('iniciar');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// Debug route
Route::get('/debug-chat', function() {
    try {
        $users = App\Models\Usuario::count();
        $conversations = App\Models\Conversation::count();
        $messages = App\Models\Message::count();
        $auth = auth()->check() ? auth()->user()->nombre : 'No autenticado';
        
        // Get a sample message if exists
        $sampleMessage = App\Models\Message::with('sender')->first();
        
        return response()->json([
            'users_count' => $users,
            'conversations_count' => $conversations,
            'messages_count' => $messages,
            'auth_user' => $auth,
            'sample_message' => $sampleMessage ? [
                'id' => $sampleMessage->id,
                'sender' => $sampleMessage->sender->nombre ?? 'No sender',
                'message' => $sampleMessage->decrypted_message,
                'created_at' => $sampleMessage->created_at
            ] : null,
            'config_pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'app_id' => config('broadcasting.connections.pusher.app_id')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()]);
    }
});

// Debug chat route
Route::get('/debug-chat-show/{userId}', function($userId) {
    try {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado']);
        }
        
        $otherUser = App\Models\Usuario::find($userId);
        if (!$otherUser) {
            return response()->json(['error' => 'Usuario no encontrado']);
        }
        
        $conversation = $user->getConversationWith($userId);
        
        return response()->json([
            'current_user' => $user->nombre,
            'other_user' => $otherUser->nombre,
            'conversation_exists' => $conversation ? true : false,
            'conversation_id' => $conversation ? $conversation->id : null,
            'messages_count' => $conversation ? $conversation->messages()->count() : 0
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()]);
    }
})->middleware('auth');

