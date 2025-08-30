@extends('layouts.chat')

@section('title', 'Chat - Mis Conversaciones')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Sidebar with conversations -->
        <div class="col-md-4 border-end bg-light h-100">
            <div class="d-flex align-items-center p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>
                    Mis Conversaciones
                </h5>
            </div>
            
            <div id="conversations-list" class="h-100 overflow-auto">
                <!-- Conversations will be loaded here -->
            </div>
        </div>
        
        <!-- Main chat area -->
        <div class="col-md-8 d-flex flex-column h-100">
            <div class="flex-fill d-flex align-items-center justify-content-center">
                <div class="text-center text-muted">
                    <i class="fas fa-comment-dots fa-4x mb-3"></i>
                    <h4>Selecciona una conversación</h4>
                    <p>Elige una conversación de la lista para comenzar a chatear</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .conversation-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .conversation-item:hover {
        background-color: #f8f9fa;
    }
    
    .conversation-item.active {
        background-color: #e3f2fd;
        border-left: 4px solid #1976d2;
    }
    
    .online-indicator {
        width: 10px;
        height: 10px;
        background-color: #4caf50;
        border-radius: 50%;
        position: absolute;
        bottom: 2px;
        right: 2px;
        border: 2px solid white;
    }
    
    .message-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 18px;
        margin-bottom: 10px;
        word-wrap: break-word;
    }
    
    .message-sent {
        background-color: #1976d2;
        color: white;
        margin-left: auto;
    }
    
    .message-received {
        background-color: #f1f1f1;
        color: #333;
        margin-right: auto;
    }
    
    .message-time {
        font-size: 0.75rem;
        opacity: 0.7;
        margin-top: 5px;
    }
    
    .unread-badge {
        background-color: #dc3545;
        color: white;
        border-radius: 10px;
        padding: 2px 8px;
        font-size: 0.75rem;
        min-width: 20px;
        text-align: center;
    }
    
    #chat-messages {
        background-color: #fafafa;
    }
    
    .security-indicator {
        font-size: 0.75rem;
        color: #4caf50;
        text-align: center;
        margin-bottom: 15px;
        padding: 5px;
        background-color: #e8f5e8;
        border-radius: 10px;
    }
</style>
@endpush

@push('scripts')
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Chat script initialized');
    
    let pusher;
    let currentUserId = {{ auth()->user()->id_usuario ?? 'null' }};
    
    console.log('Current user ID:', currentUserId);
    
    // Initialize Pusher
    function initPusher() {
        try {
            pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
                cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                encrypted: true
            });
            console.log('Pusher initialized');
        } catch (error) {
            console.error('Error initializing Pusher:', error);
        }
    }
    
    // Load conversations
    function loadConversations() {
        console.log('Loading conversations...');
        
        fetch('/api/conversations', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Conversations response status:', response.status);
            return response.json();
        })
        .then(conversations => {
            console.log('Conversations loaded:', conversations);
            
            const container = document.getElementById('conversations-list');
            if (!container) {
                console.error('Conversations container not found');
                return;
            }
            
            container.innerHTML = '';
            
            if (conversations.length === 0) {
                container.innerHTML = `
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No hay conversaciones aún</p>
                    </div>
                `;
                return;
            }
            
            conversations.forEach(conversation => {
                const conversationHtml = createConversationHtml(conversation);
                container.appendChild(conversationHtml);
            });
        })
        .catch(error => {
            console.error('Error loading conversations:', error);
            const container = document.getElementById('conversations-list');
            if (container) {
                container.innerHTML = `
                    <div class="p-3 text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Error al cargar conversaciones</p>
                        <button class="btn btn-sm btn-outline-primary" onclick="loadConversations()">Reintentar</button>
                    </div>
                `;
            }
        });
    }
    
    // Create conversation HTML element
    function createConversationHtml(conversation) {
        const div = document.createElement('div');
        div.className = 'conversation-item border-bottom p-3';
        div.dataset.conversationId = conversation.id;
        div.dataset.userId = conversation.other_user.id;
        
        const unreadBadge = conversation.unread_count > 0 ? 
            `<span class="unread-badge">${conversation.unread_count}</span>` : '';
        
        const onlineIndicator = conversation.other_user.is_online ? 
            '<div class="online-indicator"></div>' : '';
        
        const lastMessage = conversation.latest_message ? 
            `<small class="text-muted">${conversation.latest_message.message.substring(0, 50)}${conversation.latest_message.message.length > 50 ? '...' : ''}</small>` : 
            '<small class="text-muted">Sin mensajes</small>';
        
        const defaultAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(conversation.other_user.name) + '&background=007bff&color=fff';
        
        div.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="position-relative me-3">
                    <img src="${conversation.other_user.avatar || defaultAvatar}" alt="${conversation.other_user.name}" 
                         class="rounded-circle" width="50" height="50" onerror="this.src='${defaultAvatar}'">
                    ${onlineIndicator}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-1">${conversation.other_user.name}</h6>
                        ${unreadBadge}
                    </div>
                    ${lastMessage}
                    <small class="text-muted">${conversation.other_user.is_online ? 'En línea' : conversation.other_user.last_seen}</small>
                </div>
            </div>
        `;
        
        div.addEventListener('click', () => {
            console.log('Conversation clicked:', conversation.other_user.id, conversation.other_user.name);
            openChat(conversation.other_user.id, conversation.other_user.name, conversation.id);
        });
        
        return div;
    }
    
    // Open chat with user
    function openChat(userId, userName, conversationId = null) {
        console.log('Opening chat with:', { userId, userName, conversationId });
        
        // Redirect to chat/{userId} instead of opening modal
        window.location.href = `/chat/${userId}`;
    }
    
    // Load chat messages (NOT USED IN INDEX - KEPT FOR COMPATIBILITY)
    function loadChatMessages(userId) {
        console.log('loadChatMessages called but not used in index view');
    }
    
    // Send message function (NOT USED IN INDEX - KEPT FOR COMPATIBILITY) 
    function sendMessage() {
        console.log('sendMessage called but not used in index view');
        return false;
    }
    
    // Update online status
    function updateOnlineStatus(isOnline = true) {
        fetch('/chat/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ is_online: isOnline })
        })
        .catch(error => console.log('Status update error (non-critical):', error));
    }
    
    // Initialize everything
    console.log('Initializing chat system...');
    initPusher();
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
    
    // Refresh conversations every 30 seconds
    setInterval(loadConversations, 30000);
    
    // Make functions globally available for debugging
    window.chatDebug = {
        loadConversations,
        openChat,
        currentUserId: () => currentUserId
    };
    
    console.log('Chat system initialized successfully');
});
</script>
@endpush
