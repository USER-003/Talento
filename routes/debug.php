<?php

use Illuminate\Support\Facades\Route;
use App\Models\Usuario;
use App\Models\Conversation;

Route::get('/debug-chat', function() {
    try {
        $users = Usuario::count();
        $conversations = Conversation::count();
        $auth = auth()->check() ? auth()->user()->nombre : 'No autenticado';
        
        return response()->json([
            'users_count' => $users,
            'conversations_count' => $conversations,
            'auth_user' => $auth,
            'config_pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'app_id' => config('broadcasting.connections.pusher.app_id')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
