<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Usuario;
use App\Models\Invoice;
use App\Models\BlockedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class ChatController extends Controller
{
    private $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true
            ]
        );
    }

    public function index()
    {
        $user = Auth::user();
        
        $conversations = Conversation::where('user_one', $user->id_usuario)
            ->orWhere('user_two', $user->id_usuario)
            ->with(['userOne', 'userTwo', 'latestMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function show($userId)
    {
        try {
            $user = Auth::user();
            $otherUser = Usuario::findOrFail($userId);

            // Check if users are blocked
            if ($user->isBlockedBy($userId) || $user->hasBlocked($userId)) {
                return redirect()->route('chat.index')->with('error', 'No puedes chatear con este usuario.');
            }

            $conversation = $user->getConversationWith($userId);

            if (!$conversation) {
                $conversation = Conversation::create([
                    'user_one' => min($user->id_usuario, $userId),
                    'user_two' => max($user->id_usuario, $userId),
                    'last_message_at' => now()
                ]);
            }

            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            $conversation->messages()
                ->where('sender_id', '!=', $user->id_usuario)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return view('chat.show', compact('conversation', 'messages', 'otherUser'));
            
        } catch (\Exception $e) {
            \Log::error('Chat show error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return redirect()->route('chat.index')->with('error', 'Error al cargar la conversación.');
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'recipient_id' => 'required_without:conversation_id|exists:usuarios,id_usuario',
                'conversation_id' => 'required_without:recipient_id|exists:conversations,id'
            ]);

            $user = Auth::user();
            $conversation = null;

            if ($request->conversation_id) {
                $conversation = Conversation::findOrFail($request->conversation_id);
                
                // Check if user is participant
                if (!$conversation->isParticipant($user->id_usuario)) {
                    return response()->json(['error' => 'No autorizado'], 403);
                }
            } else {
                // Find or create conversation with recipient
                $recipientId = $request->recipient_id;
                
                // Check if users are blocked
                if ($user->isBlockedBy($recipientId) || $user->hasBlocked($recipientId)) {
                    return response()->json(['error' => 'No puedes enviar mensajes a este usuario'], 403);
                }
                
                $conversation = $user->getConversationWith($recipientId);
                
                if (!$conversation) {
                    $conversation = Conversation::create([
                        'user_one' => min($user->id_usuario, $recipientId),
                        'user_two' => max($user->id_usuario, $recipientId),
                        'last_message_at' => now()
                    ]);
                }
            }

            // Check if conversation is blocked
            if ($conversation->is_blocked) {
                return response()->json(['error' => 'Esta conversación está bloqueada'], 403);
            }

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id_usuario,
                'message' => $request->message
            ]);

            // Update conversation last message time
            $conversation->update(['last_message_at' => now()]);

            // Load sender relationship
            $message->load('sender');

            // Try Pusher broadcast with error handling
            try {
                $messageData = [
                    'id' => $message->id,
                    'message' => $message->decrypted_message,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->nombre,
                    'created_at' => $message->created_at->format('H:i'),
                    'is_read' => $message->is_read
                ];
                
                // Broadcast to conversation channel
                $this->pusher->trigger(
                    'chat-' . $conversation->id,
                    'new-message',
                    ['message' => $messageData]
                );
                
                // Broadcast to recipient's personal channel for quick chat notifications
                $recipientId = $conversation->user_one === $user->id_usuario 
                    ? $conversation->user_two 
                    : $conversation->user_one;
                    
                $this->pusher->trigger(
                    'user-' . $recipientId,
                    'new-message',
                    ['message' => $messageData]
                );
                
            } catch (\Exception $pusherError) {
                // Log Pusher error but don't fail the request
                \Log::error('Pusher error: ' . $pusherError->getMessage());
            }

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'message' => $message->decrypted_message,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                    'sender_name' => $message->sender->nombre
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('SendMessage error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    public function markAsRead(Request $request, $messageId)
    {
        $user = Auth::user();
        $message = Message::findOrFail($messageId);

        // Check if user can mark this message as read
        if ($message->sender_id === $user->id_usuario) {
            return response()->json(['error' => 'No puedes marcar tu propio mensaje como leído'], 403);
        }

        $conversation = $message->conversation;
        if (!$conversation->isParticipant($user->id_usuario)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $message->markAsRead();

        // Broadcast read status
        $this->pusher->trigger(
            'chat-' . $conversation->id,
            'message-read',
            [
                'message_id' => $message->id,
                'read_at' => $message->read_at->format('H:i')
            ]
        );

        return response()->json(['success' => true]);
    }

    public function blockUser(Request $request, $userId)
    {
        $user = Auth::user();

        if ($user->hasBlocked($userId)) {
            return response()->json(['error' => 'Ya has bloqueado a este usuario'], 400);
        }

        BlockedUser::create([
            'blocker_id' => $user->id_usuario,
            'blocked_id' => $userId
        ]);

        // Block the conversation
        $conversation = $user->getConversationWith($userId);
        if ($conversation) {
            $conversation->update([
                'is_blocked' => true,
                'blocked_by' => $user->id_usuario
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Usuario bloqueado correctamente']);
    }

    public function unblockUser(Request $request, $userId)
    {
        $user = Auth::user();

        BlockedUser::where('blocker_id', $user->id_usuario)
            ->where('blocked_id', $userId)
            ->delete();

        // Unblock the conversation
        $conversation = $user->getConversationWith($userId);
        if ($conversation && $conversation->blocked_by === $user->id_usuario) {
            $conversation->update([
                'is_blocked' => false,
                'blocked_by' => null
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Usuario desbloqueado correctamente']);
    }

    public function getUserStatus($userId)
    {
        $user = Usuario::findOrFail($userId);
        
        return response()->json([
            'is_online' => $user->is_online,
            'last_seen' => $user->last_seen ? $user->last_seen->diffForHumans() : null
        ]);
    }

    public function updateOnlineStatus(Request $request)
    {
        $user = Auth::user();
        $user->setOnlineStatus($request->boolean('is_online', true));

        // Broadcast status update
        $this->pusher->trigger(
            'user-status',
            'status-update',
            [
                'user_id' => $user->id_usuario,
                'is_online' => $user->is_online,
                'last_seen' => $user->last_seen->diffForHumans()
            ]
        );

        return response()->json(['success' => true]);
    }

    public function getConversations()
    {
        $user = Auth::user();

        // Preload pending invoice totals grouped by client for the provider (current user)
        $pendingByClient = Invoice::selectRaw('client_id, SUM(amount) as total, MIN(currency) as currency, COUNT(*) as count')
            ->where('provider_id', $user->id_usuario)
            ->where('status', 'pending')
            ->groupBy('client_id')
            ->get()
            ->keyBy('client_id');

        $conversations = Conversation::where('user_one', $user->id_usuario)
            ->orWhere('user_two', $user->id_usuario)
            ->with(['userOne', 'userTwo'])
            ->withCount([
                'messages as unread_count' => function ($query) use ($user) {
                    $query->where('sender_id', '!=', $user->id_usuario)
                          ->where('is_read', false);
                }
            ])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user, $pendingByClient) {
                $otherUser = $conversation->getOtherUser($user->id_usuario);
                $latestMessage = $conversation->messages()->latest()->first();
                $pending = $pendingByClient->get($otherUser->id_usuario);
                
                return [
                    'id' => $conversation->id,
                    'other_user' => [
                        'id' => $otherUser->id_usuario,
                        'name' => $otherUser->nombre,
                        'avatar' => $otherUser->avatar_url,
                        'is_online' => $otherUser->is_online,
                        'last_seen' => $otherUser->last_seen ? $otherUser->last_seen->diffForHumans() : null
                    ],
                    'latest_message' => $latestMessage ? [
                        'message' => $latestMessage->decrypted_message,
                        'created_at' => $latestMessage->created_at->toISOString(),
                        'is_own' => $latestMessage->sender_id === $user->id_usuario
                    ] : null,
                    'unread_count' => $conversation->unread_count,
                    'is_blocked' => $conversation->is_blocked,
                    'blocked_by_me' => $conversation->blocked_by === $user->id_usuario,
                    'pending_total' => $pending ? (float) $pending->total : 0.0,
                    'pending_currency' => $pending ? $pending->currency : null,
                    'pending_count' => $pending ? (int) $pending->count : 0
                ];
            });

        return response()->json($conversations);
    }

    public function getMessages($userId)
    {
        try {
            $user = Auth::user();
            $otherUser = Usuario::findOrFail($userId);

            // Check if users are blocked
            if ($user->isBlockedBy($userId) || $user->hasBlocked($userId)) {
                return response()->json(['error' => 'No puedes chatear con este usuario.'], 403);
            }

            $conversation = $user->getConversationWith($userId);

            if (!$conversation) {
                // Create new conversation if it doesn't exist
                $conversation = Conversation::create([
                    'user_one' => min($user->id_usuario, $userId),
                    'user_two' => max($user->id_usuario, $userId),
                    'last_message_at' => now()
                ]);
            }

            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) use ($user) {
                    return [
                        'id' => $message->id,
                        'conversation_id' => $message->conversation_id,
                        'sender_id' => $message->sender_id,
                        'message' => $message->decrypted_message,
                        'is_read' => $message->is_read,
                        'created_at' => $message->created_at,
                        'sender_name' => $message->sender->nombre,
                        'is_own' => $message->sender_id === $user->id_usuario
                    ];
                });

            // Mark messages as read
            $conversation->messages()
                ->where('sender_id', '!=', $user->id_usuario)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'messages' => $messages,
                'other_user' => [
                    'id' => $otherUser->id_usuario,
                    'name' => $otherUser->nombre,
                    'avatar' => $otherUser->avatar_url,
                    'is_online' => $otherUser->is_online
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('GetMessages error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return response()->json(['error' => 'Error al cargar mensajes: ' . $e->getMessage()], 500);
        }
    }
}
