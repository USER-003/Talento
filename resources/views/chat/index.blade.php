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
            <!-- Chat header (hidden until a conversation is selected) -->
            <div id="chat-header" class="border-bottom p-3 bg-white d-none"></div>
            <!-- Messages area -->
            <div id="chat-messages" class="flex-fill p-3 overflow-auto bg-light d-none"></div>
            <!-- Message input / blocked notice -->
            <div id="chat-input" class="border-top p-3 bg-white d-none"></div>
            <!-- Placeholder when no conversation selected -->
            <div id="chat-placeholder" class="flex-fill d-flex align-items-center justify-content-center">
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
    .blocked-badge { background-color: #6c757d; color: #fff; border-radius: 10px; padding: 2px 6px; font-size: 0.7rem; }
    
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
    const escapeHtml = (s) => String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    
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
    const blocked = conversation.is_blocked ? `<span class="blocked-badge ms-2">Bloqueado</span>` : '';
        
        const pending = conversation.pending_count > 0 ? `
            <span class="badge bg-warning text-dark ms-2" title="Facturas pendientes">
                ${conversation.pending_currency || ''} ${Number(conversation.pending_total).toFixed(2)}
            </span>` : '';

        const billBtn = conversation.is_blocked ? '' : `
            <a href="/ingresos/crear?client_id=${conversation.other_user.id}" class="btn btn-sm btn-outline-primary ms-2" onclick="event.stopPropagation();">
                <i class="fas fa-file-invoice-dollar"></i>
            </a>`;

        div.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="position-relative me-3">
                    <img src="${conversation.other_user.avatar || defaultAvatar}" alt="${conversation.other_user.name}"
                         class="rounded-circle" width="50" height="50" onerror="this.src='${defaultAvatar}'">
                    ${onlineIndicator}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-1 d-flex align-items-center">
                            ${conversation.other_user.name}
                            ${blocked}
                            ${pending}
                        </h6>
                        <div class="d-flex align-items-center">
                            ${unreadBadge}
                            ${billBtn}
                        </div>
                    </div>
                    ${lastMessage}
                    <small class="text-muted">${conversation.other_user.is_online ? 'En línea' : (conversation.other_user.last_seen || '')}</small>
                </div>
            </div>
        `;
        
        div.addEventListener('click', () => {
            console.log('Conversation clicked:', conversation.other_user.id, conversation.other_user.name);
            openChatInline(conversation);
            // Highlight active item
            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
            div.classList.add('active');
        });
        
        return div;
    }
    
    // Open chat inline (render on the right panel)
    let activeConversationId = null;
    function openChatInline(conversation) {
        const user = conversation.other_user;
        activeConversationId = conversation.id;
    const headerDefaultAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=007bff&color=fff';

        // Header
        const header = document.getElementById('chat-header');
        header.classList.remove('d-none');
        header.innerHTML = `
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="position-relative me-3">
            <img src="${user.avatar || headerDefaultAvatar}" alt="${user.name}" class="rounded-circle" width="50" height="50" onerror="this.src='${headerDefaultAvatar}'">
                        ${user.is_online ? '<div class="online-indicator"></div>' : ''}
                    </div>
                    <div>
                        <h6 class="mb-0">${user.name}</h6>
                        <small class="text-muted">${user.is_online ? 'En línea' : (user.last_seen || '')}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    ${conversation.is_blocked ? '' : `<a href="/ingresos/crear?client_id=${user.id}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Facturar
                    </a>`}
                </div>
            </div>`;

        // Load messages from API
        const messagesContainer = document.getElementById('chat-messages');
        const inputContainer = document.getElementById('chat-input');
        const placeholder = document.getElementById('chat-placeholder');
        messagesContainer.classList.add('d-none');
        inputContainer.classList.add('d-none');
        placeholder.classList.remove('d-none');

        fetch(`/api/messages/${user.id}`)
            .then(async r => {
                const data = await r.json().catch(() => ({}));
                if (!r.ok) {
                    const msg = data && data.error ? data.error : 'No se pudieron cargar los mensajes';
                    throw new Error(msg);
                }
                return data;
            })
            .then(data => {
                if (!data.success) throw new Error('No se pudieron cargar los mensajes');
                placeholder.classList.add('d-none');
                messagesContainer.classList.remove('d-none');
                messagesContainer.innerHTML = '';
                data.messages.forEach(m => {
                    const own = m.is_own;
                    const wrapper = document.createElement('div');
                    wrapper.className = `message-container d-flex ${own ? 'justify-content-end' : 'justify-content-start'} mb-3`;
                    const bubble = document.createElement('div');
                    bubble.className = `message-bubble ${own ? 'message-sent' : 'message-received'}`;
                    const textDiv = document.createElement('div');
                    textDiv.className = 'message-text';
                    textDiv.textContent = m.message || '';
                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'message-time text-end';
                    timeDiv.textContent = new Date(m.created_at).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                    bubble.appendChild(textDiv);
                    bubble.appendChild(timeDiv);
                    wrapper.appendChild(bubble);
                    messagesContainer.appendChild(wrapper);
                });
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                // Input builder (block-aware)
                const isBlocked = conversation.is_blocked;
                const blockedByMe = conversation.blocked_by_me;
                if (isBlocked && !blockedByMe) {
                    inputContainer.innerHTML = `<div class="bg-light text-center p-3 text-muted"><i class=\"fas fa-ban text-danger mb-2\"></i><p class=\"mb-0\">Este usuario te ha bloqueado. No puedes enviar mensajes.</p></div>`;
                } else {
                    inputContainer.innerHTML = `
                        <form id="message-form" class="d-flex">
                            <input type="hidden" id="conversation-id" value="${data.conversation_id}">
                            <input type="text" id="message-input" class="form-control me-2" placeholder="Escribe tu mensaje..." maxlength="1000" required ${conversation.is_blocked ? 'disabled' : ''}>
                            <button type="submit" class="btn btn-primary" ${conversation.is_blocked ? 'disabled' : ''}><i class="fas fa-paper-plane"></i></button>
                        </form>
                        ${conversation.is_blocked && blockedByMe ? '<small class="text-muted">Has bloqueado esta conversación. Desbloquea al usuario para enviar mensajes.</small>' : ''}
                    `;

                    const form = document.getElementById('message-form');
                    const input = document.getElementById('message-input');
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const text = input.value.trim();
                        if (!text) return;
                        input.disabled = true; form.querySelector('button[type="submit"]').disabled = true;
                        fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ conversation_id: data.conversation_id, message: text })
                        })
                        .then(async r => {
                            const res = await r.json().catch(() => ({}));
                            if (!r.ok || !res.success) {
                                const msg = res && res.error ? res.error : 'Error desconocido';
                                throw new Error(msg);
                            }
                            const wrapper = document.createElement('div');
                            wrapper.className = 'message-container d-flex justify-content-end mb-3';
                            const bubble = document.createElement('div');
                            bubble.className = 'message-bubble message-sent';
                            const textDiv = document.createElement('div');
                            textDiv.className = 'message-text';
                            textDiv.textContent = text;
                            const timeDiv = document.createElement('div');
                            timeDiv.className = 'message-time text-end';
                            timeDiv.textContent = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                            bubble.appendChild(textDiv);
                            bubble.appendChild(timeDiv);
                            wrapper.appendChild(bubble);
                            messagesContainer.appendChild(wrapper);
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                            input.value = '';
                        })
                        .catch(err => alert('Error al enviar: ' + err.message))
                        .finally(() => { input.disabled = false; form.querySelector('button[type="submit"]').disabled = false; input.focus(); });
                    });
                }

                inputContainer.classList.remove('d-none');
            })
            .catch(err => {
                console.error(err);
                placeholder.classList.add('d-none');
                const errBox = document.createElement('div');
                errBox.className = 'p-3 text-center text-danger';
                errBox.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${escapeHtml(err.message)}`;
                messagesContainer.classList.remove('d-none');
                messagesContainer.innerHTML = '';
                messagesContainer.appendChild(errBox);
            });
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
    openChatInline,
        currentUserId: () => currentUserId
    };
    
    console.log('Chat system initialized successfully');
});
</script>
@endpush
