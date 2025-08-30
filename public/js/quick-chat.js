// Global functions for onclick handlers (defined immediately)
window.toggleQuickChatList = function() {
    if (window.quickChatSystem) {
        window.quickChatSystem.toggleQuickChatList();
    }
};

window.quickChatSystem = null;

// Quick Chat System - LinkedIn Style
class QuickChatSystem {
    constructor() {
        this.activeChats = new Map();
        this.isListVisible = false;
        this.currentUserId = null;
        this.pusher = null;
        this.totalUnreadCount = 0;
        
        this.init();
    }
    
    init() {
        // Get current user ID
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        if (userIdMeta) {
            this.currentUserId = parseInt(userIdMeta.getAttribute('content'));
        }
        
        // Add event listener to chat notification icon
        const chatIcon = document.getElementById('chat-notification-icon');
        if (chatIcon) {
            chatIcon.addEventListener('click', () => this.toggleQuickChatList());
            chatIcon.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggleQuickChatList();
                }
            });
        }
        
        // Initialize Pusher if available
        this.initPusher();
        
        // Load conversations
        this.loadQuickConversations();
        
        // Auto-refresh every 30 seconds
        setInterval(() => this.loadQuickConversations(), 30000);
        
        // Listen for clicks outside to close
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.quick-chat-container')) {
                this.hideQuickChatList();
            }
        });
    }
    
    initPusher() {
        if (typeof Pusher !== 'undefined' && window.pusherConfig) {
            try {
                this.pusher = new Pusher(window.pusherConfig.key, {
                    cluster: window.pusherConfig.cluster,
                    encrypted: true
                });
                
                // Listen for new messages on user channel
                if (this.currentUserId) {
                    const userChannel = this.pusher.subscribe(`user-${this.currentUserId}`);
                    userChannel.bind('new-message', (data) => {
                        this.handleNewMessage(data);
                    });
                }
            } catch (error) {
                console.error('Error initializing Pusher for quick chat:', error);
            }
        }
    }
    
    toggleQuickChatList() {
        if (this.isListVisible) {
            this.hideQuickChatList();
        } else {
            this.showQuickChatList();
        }
    }
    
    showQuickChatList() {
        const list = document.getElementById('quick-chat-list');
        if (list) {
            list.classList.remove('d-none');
            this.isListVisible = true;
            this.loadQuickConversations();
        }
    }
    
    hideQuickChatList() {
        const list = document.getElementById('quick-chat-list');
        if (list) {
            list.classList.add('d-none');
            this.isListVisible = false;
        }
    }
    
    async loadQuickConversations() {
        try {
            const response = await fetch('/api/conversations', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to load conversations');
            
            const conversations = await response.json();
            this.renderQuickConversations(conversations);
            this.updateUnreadBadge(conversations);
            
        } catch (error) {
            console.error('Error loading quick conversations:', error);
        }
    }
    
    renderQuickConversations(conversations) {
        const container = document.getElementById('quick-conversations-list');
        if (!container) return;
        
        // Show loading state first
        container.innerHTML = `
            <div class="quick-conversations-loading" style="padding: 20px; text-align: center; color: #666;">
                <i class="fas fa-spinner fa-spin"></i>
                <p style="margin: 8px 0 0 0; font-size: 13px;">Cargando conversaciones...</p>
            </div>
        `;
        
        // Small delay to show loading state
        setTimeout(() => {
            container.innerHTML = '';
            
            if (conversations.length === 0) {
                container.innerHTML = `
                    <div class="quick-conversations-empty">
                        <i class="fas fa-comment-dots"></i>
                        <p>No tienes conversaciones recientes</p>
                        <small style="color: #999; font-size: 12px;">Inicia una conversación desde el chat principal</small>
                    </div>
                `;
                return;
            }
            
            // Show only the most recent 6 conversations
            const recentConversations = conversations.slice(0, 6);
            
            recentConversations.forEach((conversation, index) => {
                const item = this.createQuickConversationItem(conversation);
                
                // Add stagger animation delay
                item.style.animationDelay = `${index * 50}ms`;
                
                container.appendChild(item);
            });
            
            // Show "see more" if there are more conversations
            if (conversations.length > 6) {
                const seeMore = document.createElement('div');
                seeMore.className = 'quick-see-more';
                seeMore.innerHTML = `
                    <a href="/chat" style="color: #0066cc; text-decoration: none; font-size: 13px; padding: 12px 18px; display: block; text-align: center; font-weight: 500;">
                        <i class="fas fa-plus-circle me-1"></i>
                        Ver ${conversations.length - 6} conversaciones más
                    </a>
                `;
                container.appendChild(seeMore);
            }
        }, 300);
    }
    
    createQuickConversationItem(conversation) {
        const div = document.createElement('div');
        div.className = 'quick-conversation-item conversation-appear';
        if (conversation.unread_count > 0) {
            div.classList.add('unread');
        }
        div.dataset.userId = conversation.other_user.id;
        div.dataset.conversationId = conversation.id;
        
        const defaultAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(conversation.other_user.name)}&background=0066cc&color=fff&size=64`;
        const lastMessage = conversation.latest_message 
            ? conversation.latest_message.message.substring(0, 45) + (conversation.latest_message.message.length > 45 ? '...' : '')
            : 'Inicia una conversación';
        
        const unreadBadge = conversation.unread_count > 0 
            ? `<div class="unread-count">${conversation.unread_count > 99 ? '99+' : conversation.unread_count}</div>` 
            : '';
        
        const onlineDot = conversation.other_user.is_online 
            ? '<div class="online-dot"></div>' 
            : '';
        
        const timeFormatted = this.formatTime(conversation.latest_message?.created_at);
        
        div.innerHTML = `
            <div class="avatar">
                <img src="${conversation.other_user.avatar || defaultAvatar}" 
                     alt="${conversation.other_user.name}"
                     onerror="this.src='${defaultAvatar}'">
                ${onlineDot}
            </div>
            <div class="content">
                <div class="name" title="${conversation.other_user.name}">${conversation.other_user.name}</div>
                <div class="last-message" title="${conversation.latest_message?.message || ''}">${lastMessage}</div>
            </div>
            <div class="meta">
                <div class="time">${timeFormatted}</div>
                ${unreadBadge}
            </div>
        `;
        
        div.addEventListener('click', () => {
            this.openQuickChat(conversation.other_user.id, conversation.other_user.name);
            this.hideQuickChatList();
        });
        
        return div;
    }
    
    updateUnreadBadge(conversations) {
        const totalUnread = conversations.reduce((sum, conv) => sum + conv.unread_count, 0);
        this.totalUnreadCount = totalUnread;
        
        const badge = document.getElementById('total-unread-badge');
        if (badge) {
            if (totalUnread > 0) {
                badge.textContent = totalUnread > 99 ? '99+' : totalUnread;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
    }
    
    openQuickChat(userId, userName) {
        // Check if chat is already open
        if (this.activeChats.has(userId)) {
            this.focusQuickChat(userId);
            return;
        }
        
        // Create new quick chat window
        const chatWindow = this.createQuickChatWindow(userId, userName);
        document.getElementById('active-quick-chats').appendChild(chatWindow);
        
        // Store reference
        this.activeChats.set(userId, {
            element: chatWindow,
            userName: userName,
            conversationId: null,
            isMinimized: false
        });
        
        // Load messages
        this.loadQuickChatMessages(userId);
    }
    
    createQuickChatWindow(userId, userName) {
        const div = document.createElement('div');
        div.className = 'quick-chat-window';
        div.dataset.userId = userId;
        
        const defaultAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=007bff&color=fff`;
        
        div.innerHTML = `
            <div class="quick-chat-window-header">
                <div class="user-info">
                    <img src="${defaultAvatar}" alt="${userName}" class="user-avatar">
                    <div class="user-name">${userName}</div>
                </div>
                <div class="actions">
                    <button class="btn" onclick="quickChatSystem.minimizeQuickChat(${userId})" title="Minimizar">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button class="btn" onclick="quickChatSystem.goToFullChat(${userId})" title="Abrir chat completo">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="btn" onclick="quickChatSystem.closeQuickChat(${userId})" title="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="quick-chat-messages" id="quick-messages-${userId}">
                <div class="text-center p-3">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <div class="mt-2">Cargando mensajes...</div>
                </div>
            </div>
            <div class="quick-chat-input">
                <form class="quick-input-form" onsubmit="quickChatSystem.sendQuickMessage(${userId}, event)">
                    <input type="text" 
                           placeholder="Escribe un mensaje..." 
                           maxlength="1000"
                           id="quick-input-${userId}"
                           required>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        `;
        
        return div;
    }
    
    async loadQuickChatMessages(userId) {
        try {
            const response = await fetch(`/api/messages/${userId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to load messages');
            
            const data = await response.json();
            this.renderQuickChatMessages(userId, data.messages);
            
            // Store conversation ID
            const chatData = this.activeChats.get(userId);
            if (chatData) {
                chatData.conversationId = data.conversation_id;
            }
            
        } catch (error) {
            console.error('Error loading quick chat messages:', error);
            this.showQuickChatError(userId, 'Error al cargar mensajes');
        }
    }
    
    renderQuickChatMessages(userId, messages) {
        const container = document.getElementById(`quick-messages-${userId}`);
        if (!container) return;
        
        container.innerHTML = '';
        
        if (messages.length === 0) {
            container.innerHTML = `
                <div class="quick-chat-empty">
                    <i class="fas fa-comment-dots"></i>
                    <p>No hay mensajes aún</p>
                </div>
            `;
            return;
        }
        
        // Show only the last 10 messages
        const recentMessages = messages.slice(-10);
        
        recentMessages.forEach(message => {
            const messageEl = this.createQuickMessageElement(message);
            container.appendChild(messageEl);
        });
        
        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    }
    
    createQuickMessageElement(message) {
        const div = document.createElement('div');
        div.className = `quick-message ${message.sender_id === this.currentUserId ? 'own' : ''}`;
        
        const time = new Date(message.created_at).toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        div.innerHTML = `
            <div class="quick-message-bubble">
                ${this.escapeHtml(message.message)}
                <div class="quick-message-time">${time}</div>
            </div>
        `;
        
        return div;
    }
    
    async sendQuickMessage(userId, event) {
        event.preventDefault();
        
        const input = document.getElementById(`quick-input-${userId}`);
        if (!input) return;
        
        const message = input.value.trim();
        if (!message) return;
        
        const chatData = this.activeChats.get(userId);
        if (!chatData) return;
        
        // Disable input
        input.disabled = true;
        
        try {
            const requestData = {
                message: message,
                recipient_id: userId
            };
            
            if (chatData.conversationId) {
                requestData.conversation_id = chatData.conversationId;
            }
            
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(requestData)
            });
            
            if (!response.ok) throw new Error('Failed to send message');
            
            const data = await response.json();
            
            if (data.success) {
                // Clear input
                input.value = '';
                
                // Update conversation ID
                if (data.conversation_id && !chatData.conversationId) {
                    chatData.conversationId = data.conversation_id;
                }
                
                // Add message to chat
                if (data.message) {
                    const messageEl = this.createQuickMessageElement(data.message);
                    const container = document.getElementById(`quick-messages-${userId}`);
                    
                    if (container) {
                        // Remove empty state if present
                        const emptyState = container.querySelector('.quick-chat-empty');
                        if (emptyState) emptyState.remove();
                        
                        container.appendChild(messageEl);
                        container.scrollTop = container.scrollHeight;
                    }
                }
                
                // Refresh conversations list
                this.loadQuickConversations();
            }
            
        } catch (error) {
            console.error('Error sending quick message:', error);
            alert('Error al enviar mensaje');
        } finally {
            input.disabled = false;
            input.focus();
        }
    }
    
    handleNewMessage(data) {
        const senderId = data.message.sender_id;
        
        // If it's from current user, ignore (already handled)
        if (senderId === this.currentUserId) return;
        
        // Show notification pulse
        this.showNewMessageNotification();
        
        // If quick chat is open for this sender, add message
        if (this.activeChats.has(senderId)) {
            const messageEl = this.createQuickMessageElement(data.message);
            const container = document.getElementById(`quick-messages-${senderId}`);
            
            if (container) {
                container.appendChild(messageEl);
                container.scrollTop = container.scrollHeight;
                
                // Add pulse effect
                container.classList.add('new-message-pulse');
                setTimeout(() => container.classList.remove('new-message-pulse'), 600);
            }
        }
        
        // Refresh conversations list
        this.loadQuickConversations();
    }
    
    showNewMessageNotification() {
        const icon = document.getElementById('chat-notification-icon');
        if (icon) {
            icon.classList.add('new-message-pulse');
            setTimeout(() => icon.classList.remove('new-message-pulse'), 600);
        }
    }
    
    minimizeQuickChat(userId) {
        const chatData = this.activeChats.get(userId);
        if (!chatData) return;
        
        const element = chatData.element;
        if (chatData.isMinimized) {
            // Restore
            element.classList.remove('quick-chat-minimized');
            chatData.isMinimized = false;
        } else {
            // Minimize
            element.classList.add('quick-chat-minimized');
            chatData.isMinimized = true;
        }
    }
    
    closeQuickChat(userId) {
        const chatData = this.activeChats.get(userId);
        if (!chatData) return;
        
        chatData.element.remove();
        this.activeChats.delete(userId);
    }
    
    focusQuickChat(userId) {
        const chatData = this.activeChats.get(userId);
        if (!chatData) return;
        
        if (chatData.isMinimized) {
            this.minimizeQuickChat(userId);
        }
        
        const input = document.getElementById(`quick-input-${userId}`);
        if (input) input.focus();
    }
    
    goToFullChat(userId) {
        window.location.href = `/chat/${userId}`;
    }
    
    showQuickChatError(userId, message) {
        const container = document.getElementById(`quick-messages-${userId}`);
        if (container) {
            container.innerHTML = `
                <div class="quick-chat-empty text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${message}</p>
                </div>
            `;
        }
    }
    
    formatTime(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'Ahora';
        if (diffMins < 60) return `${diffMins} min`;
        if (diffHours < 24) return `${diffHours}h`;
        if (diffDays === 1) return 'Ayer';
        if (diffDays < 7) return `${diffDays} días`;
        if (diffDays < 30) {
            const weeks = Math.floor(diffDays / 7);
            return weeks === 1 ? '1 semana' : `${weeks} semanas`;
        }
        
        return date.toLocaleDateString('es-ES', { 
            day: 'numeric', 
            month: 'short',
            year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
        });
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize quick chat system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if user is authenticated
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (userIdMeta && userIdMeta.getAttribute('content')) {
        window.quickChatSystem = new QuickChatSystem();
    }
});
