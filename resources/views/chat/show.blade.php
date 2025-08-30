@extends('layouts.chat')

@section('title', 'Chat con {{ $otherUser->nombre }}')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Sidebar with conversations -->
        <div class="col-md-4 border-end bg-light h-100">
            <div     // Listen for new messages
    conversationChannel.bind('new-message', function(data) {
        console.log('New message received via Pusher:', data); // Debug log
        
        // Only add message if it's not from current user (to avoid duplicates)
        if (data.message.sender_id !== currentUserId) {
            addMessageToChat(data.message);
            scrollToBottom();
            
            // Mark as read if page is visible
            if (!document.hidden) {
                markMessageAsRead(data.message.id);
            }
        }
    });lex align-items-center p-3 border-bottom">
                <a href="{{ route('chat.index') }}" class="btn btn-outline-primary btn-sm me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>
                    Conversaciones
                </h5>
            </div>
            
            <div id="conversations-list" class="h-100 overflow-auto">
                <!-- Conversations will be loaded here -->
            </div>
        </div>
        
        <!-- Chat area -->
        <div class="col-md-8 d-flex flex-column h-100">
            <!-- Chat header -->
            <div class="border-bottom p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-3">
                            <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->nombre }}" 
                                 class="rounded-circle" width="50" height="50">
                            <div class="online-indicator {{ $otherUser->is_online ? 'd-block' : 'd-none' }}"></div>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $otherUser->nombre }}</h6>
                            <small class="text-muted" id="user-status">
                                @if($otherUser->is_online)
                                    En línea
                                @else
                                    {{ $otherUser->last_seen ? $otherUser->last_seen->diffForHumans() : 'Desconectado' }}
                                @endif
                            </small>
                        </div>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            @if($conversation->is_blocked)
                                @if($conversation->blocked_by === auth()->user()->id_usuario)
                                    <li><a class="dropdown-item text-success" href="#" id="unblock-user-btn">
                                        <i class="fas fa-unlock me-2"></i>Desbloquear usuario
                                    </a></li>
                                @else
                                    <li><span class="dropdown-item text-muted">
                                        <i class="fas fa-ban me-2"></i>Usuario te ha bloqueado
                                    </span></li>
                                @endif
                            @else
                                <li><a class="dropdown-item text-danger" href="#" id="block-user-btn">
                                    <i class="fas fa-ban me-2"></i>Bloquear usuario
                                </a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Security indicator -->
            <div class="security-indicator">
                <i class="fas fa-lock me-1"></i>
                Esta conversación está cifrada de extremo a extremo para proteger tu privacidad
            </div>
            
            <!-- Messages area -->
            <div id="chat-messages" class="flex-fill p-3 overflow-auto bg-light">
                @foreach($messages as $message)
                    <div class="message-container d-flex {{ $message->sender_id === auth()->user()->id_usuario ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                        <div class="message-bubble {{ $message->sender_id === auth()->user()->id_usuario ? 'message-sent' : 'message-received' }}">
                            <div class="message-text">{{ $message->decrypted_message }}</div>
                            <div class="message-time text-end">
                                {{ $message->created_at->format('H:i') }}
                                @if($message->sender_id === auth()->user()->id_usuario)
                                    <i class="fas fa-check{{ $message->is_read ? '-double text-info' : '' }} ms-1"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Message input -->
            @if(!$conversation->is_blocked || $conversation->blocked_by === auth()->user()->id_usuario)
                <div class="border-top p-3 bg-white">
                    <form id="message-form" class="d-flex">
                        <input type="hidden" id="conversation-id" value="{{ $conversation->id }}">
                        <input type="text" id="message-input" class="form-control me-2" 
                               placeholder="Escribe tu mensaje..." maxlength="1000" required
                               {{ $conversation->is_blocked ? 'disabled' : '' }}>
                        <button type="submit" class="btn btn-primary" {{ $conversation->is_blocked ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                    @if($conversation->is_blocked && $conversation->blocked_by === auth()->user()->id_usuario)
                        <small class="text-muted">Has bloqueado esta conversación. Desbloquea al usuario para enviar mensajes.</small>
                    @endif
                </div>
            @else
                <div class="border-top p-3 bg-light text-center">
                    <i class="fas fa-ban text-danger mb-2"></i>
                    <p class="text-muted mb-0">Este usuario te ha bloqueado. No puedes enviar mensajes.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Block confirmation modal -->
<div class="modal fade" id="blockConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar bloqueo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres bloquear a <strong>{{ $otherUser->nombre }}</strong>?</p>
                <p class="text-muted small">El usuario no podrá enviarte mensajes y no verá cuando estés en línea.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm-block-btn">Bloquear</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .online-indicator {
        width: 12px;
        height: 12px;
        background-color: #4caf50;
        border-radius: 50%;
        position: absolute;
        bottom: 2px;
        right: 2px;
        border: 2px solid white;
    }
    
    .message-bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        word-wrap: break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .message-sent {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        color: white;
    }
    
    .message-received {
        background-color: white;
        color: #333;
        border: 1px solid #e0e0e0;
    }
    
    .message-time {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 5px;
    }
    
    .security-indicator {
        font-size: 0.75rem;
        color: #4caf50;
        text-align: center;
        margin: 10px;
        padding: 8px;
        background-color: #e8f5e8;
        border-radius: 20px;
        border: 1px solid #c8e6c9;
    }
    
    #chat-messages {
        background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 400px;
    }
    
    .conversation-item {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .conversation-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .conversation-item.active {
        background-color: #e3f2fd;
        border-left: 4px solid #1976d2;
    }
    
    .typing-indicator {
        display: flex;
        align-items: center;
        padding: 10px;
        color: #666;
        font-style: italic;
        font-size: 0.9rem;
    }
    
    .typing-dots {
        display: inline-block;
        margin-left: 5px;
    }
    
    .typing-dots span {
        display: inline-block;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: #666;
        margin: 0 1px;
        animation: typing 1.4s infinite ease-in-out;
    }
    
    .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
    .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes typing {
        0%, 80%, 100% { 
            transform: scale(0.8);
            opacity: 0.5;
        }
        40% { 
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const conversationId = {{ $conversation->id }};
    const currentUserId = {{ auth()->user()->id_usuario }};
    const otherUserId = {{ $otherUser->id_usuario }};
    
    // Initialize Pusher
    const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
        cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
        encrypted: true
    });
    
    // Subscribe to conversation channel
    const conversationChannel = pusher.subscribe(`chat-${conversationId}`);
    const userStatusChannel = pusher.subscribe('user-status');
    
    const messagesContainer = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    
    // Debug logs
    console.log('DOM elements:', {
        messagesContainer: !!messagesContainer,
        messageForm: !!messageForm,
        messageInput: !!messageInput,
        conversationId: conversationId,
        currentUserId: currentUserId
    });
    
    if (!messageForm) {
        console.error('Message form not found!');
        return;
    }
    
    if (!messageInput) {
        console.error('Message input not found!');
        return;
    }
    
    // Listen for new messages
    conversationChannel.bind('new-message', function(data) {
        addMessageToChat(data.message);
        scrollToBottom();
        
        // Mark as read if not sent by current user
        if (data.message.sender_id !== currentUserId) {
            markMessageAsRead(data.message.id);
        }
    });
    
    // Listen for message read status
    conversationChannel.bind('message-read', function(data) {
        updateMessageReadStatus(data.message_id, data.read_at);
    });
    
    // Listen for user status updates
    userStatusChannel.bind('status-update', function(data) {
        if (data.user_id === otherUserId) {
            updateUserStatus(data.is_online, data.last_seen);
        }
    });
    
    // Send message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted'); // Debug log
        
        const message = messageInput.value.trim();
        if (!message) {
            console.log('Empty message'); // Debug log
            return;
        }
        
        console.log('Sending message:', message); // Debug log
        
        // Disable input while sending
        messageInput.disabled = true;
        const submitBtn = messageForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        
        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                message: message
            })
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug log
            if (data.success) {
                // Clear input
                messageInput.value = '';
                
                // Add message to chat immediately
                if (data.message) {
                    const messageData = {
                        id: data.message.id,
                        message: data.message.message || message,
                        sender_id: currentUserId,
                        created_at: new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }),
                        is_read: false
                    };
                    addMessageToChat(messageData);
                    scrollToBottom();
                }
                
                // Reload conversations to update last message
                loadConversations();
            } else {
                alert('Error al enviar el mensaje: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al enviar el mensaje');
        })
        .finally(() => {
            // Re-enable input
            messageInput.disabled = false;
            submitBtn.disabled = false;
            messageInput.focus();
        });
    });
    
    // Send message on Enter key
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });
    
    // Block user
    document.getElementById('block-user-btn')?.addEventListener('click', function(e) {
        e.preventDefault();
        const modal = new bootstrap.Modal(document.getElementById('blockConfirmModal'));
        modal.show();
    });
    
    document.getElementById('confirm-block-btn')?.addEventListener('click', function() {
        fetch(`/chat/block/${otherUserId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
    
    // Unblock user
    document.getElementById('unblock-user-btn')?.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (confirm('¿Desbloquear a este usuario?')) {
            fetch(`/chat/unblock/${otherUserId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
    
    // Helper functions
    function addMessageToChat(message) {
        const isOwn = message.sender_id === currentUserId;
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-container d-flex ${isOwn ? 'justify-content-end' : 'justify-content-start'} mb-3`;
        
        messageDiv.innerHTML = `
            <div class="message-bubble ${isOwn ? 'message-sent' : 'message-received'}">
                <div class="message-text">${escapeHtml(message.message)}</div>
                <div class="message-time text-end">
                    ${message.created_at}
                    ${isOwn ? `<i class="fas fa-check${message.is_read ? '-double text-info' : ''} ms-1"></i>` : ''}
                </div>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
        console.log('Message added to chat:', message); // Debug log
    }
    
    function markMessageAsRead(messageId) {
        fetch(`/chat/mark-read/${messageId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    }
    
    function updateMessageReadStatus(messageId, readAt) {
        // Update read status icons for messages
        const messageElements = document.querySelectorAll('.message-sent');
        // This would need to be implemented to match specific messages
    }
    
    function updateUserStatus(isOnline, lastSeen) {
        const statusElement = document.getElementById('user-status');
        const onlineIndicator = document.querySelector('.online-indicator');
        
        if (isOnline) {
            statusElement.textContent = 'En línea';
            onlineIndicator.classList.remove('d-none');
        } else {
            statusElement.textContent = lastSeen;
            onlineIndicator.classList.add('d-none');
        }
    }
    
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Update online status
    function updateOnlineStatus(isOnline = true) {
        fetch('/chat/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_online: isOnline })
        });
    }
    
    // Load conversations for sidebar
    function loadConversations() {
        fetch('/api/conversations')
            .then(response => response.json())
            .then(conversations => {
                const container = document.getElementById('conversations-list');
                container.innerHTML = '';
                
                conversations.forEach(conversation => {
                    const isActive = conversation.id === conversationId;
                    const unreadBadge = conversation.unread_count > 0 && !isActive ?
                        `<span class="badge bg-danger rounded-pill">${conversation.unread_count}</span>` : '';
                    
                    const conversationHtml = `
                        <div class="conversation-item border-bottom p-3 ${isActive ? 'active' : ''}" 
                             onclick="window.location.href='/chat/${conversation.other_user.id}'">
                            <div class="d-flex align-items-center">
                                <div class="position-relative me-3">
                                    <img src="${conversation.other_user.avatar}" alt="${conversation.other_user.name}" 
                                         class="rounded-circle" width="40" height="40">
                                    ${conversation.other_user.is_online ? '<div class="online-indicator"></div>' : ''}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">${conversation.other_user.name}</h6>
                                        ${unreadBadge}
                                    </div>
                                    <small class="text-muted">
                                        ${conversation.latest_message ? conversation.latest_message.message.substring(0, 30) + '...' : 'Sin mensajes'}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    container.insertAdjacentHTML('beforeend', conversationHtml);
                });
            });
    }
    
    // Initialize
    scrollToBottom();
    loadConversations();
    updateOnlineStatus(true);
    
    // Update status when page becomes visible/hidden
    document.addEventListener('visibilitychange', function() {
        updateOnlineStatus(!document.hidden);
    });
    
    // Update status when user leaves page
    window.addEventListener('beforeunload', function() {
        updateOnlineStatus(false);
    });
    
    // Auto-focus message input
    messageInput.focus();
    
    // Refresh conversations every 30 seconds
    setInterval(loadConversations, 30000);
});
</script>
@endpush
