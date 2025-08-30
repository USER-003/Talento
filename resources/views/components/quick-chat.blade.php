<!-- Quick Chat Component - LinkedIn Style -->
<div id="quick-chat-container" class="quick-chat-container">
    <!-- Quick Chat Notification Icon -->
    <button id="chat-notification-icon" 
            class="chat-notification-icon" 
            aria-label="Abrir mensajes rápidos"
            title="Mensajes">
        <i class="fas fa-comment-dots"></i>
        <span id="total-unread-badge" class="notification-badge d-none">0</span>
    </button>

    <!-- Quick Chat List -->
    <div id="quick-chat-list" class="quick-chat-list d-none">
        <div class="quick-chat-header">
            <h6 class="mb-0">
                <i class="fas fa-comments me-2"></i>
                Mensajes
            </h6>
            <div class="quick-chat-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='/chat'">
                    <i class="fas fa-expand-arrows-alt"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="toggleQuickChatList()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div id="quick-conversations-list" class="quick-conversations-list">
            <!-- Conversations will be loaded here -->
        </div>
        
        <div class="quick-chat-footer">
            <a href="/chat" class="btn btn-primary btn-sm w-100">
                Ver todos los mensajes
            </a>
        </div>
    </div>

    <!-- Active Quick Chats -->
    <div id="active-quick-chats" class="active-quick-chats">
        <!-- Active chat windows will appear here -->
    </div>
</div>

<style>
.quick-chat-container {
    position: fixed;
    bottom: 0;
    right: 20px;
    z-index: 1050;
    font-family: 'Inter', sans-serif;
}

.chat-notification-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    transition: all 0.3s ease;
    position: relative;
    margin-bottom: 10px;
    border: none;
    outline: none;
}

.chat-notification-icon:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
}

.chat-notification-icon i {
    font-size: 24px;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: bold;
    min-width: 18px;
    text-align: center;
    border: 2px solid white;
}

.quick-chat-list {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 320px;
    max-height: 400px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border: 1px solid #e0e0e0;
    animation: slideUpFadeIn 0.3s ease-out;
}

@keyframes slideUpFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.quick-chat-header {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.quick-chat-actions {
    display: flex;
    gap: 5px;
}

.quick-conversations-list {
    max-height: 300px;
    overflow-y: auto;
    background: #ffffff;
}

.quick-conversation-item {
    padding: 10px 14px;
    border-bottom: 1px solid #f0f2f5;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    position: relative;
    background: white;
    min-height: 52px;
}

.quick-conversation-item:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateX(3px);
    box-shadow: 0 3px 12px rgba(0, 102, 204, 0.15);
    border-left: 4px solid #0066cc;
}

.quick-conversation-item:active {
    transform: translateX(1px) scale(0.98);
}

.quick-conversation-item:last-child {
    border-bottom: none;
    border-radius: 0 0 12px 12px;
}

.quick-conversation-item:first-child {
    border-radius: 12px 12px 0 0;
}

.quick-conversation-item.unread {
    background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%);
    border-left: 4px solid #0066cc;
    font-weight: 600;
}

.quick-conversation-item.unread:hover {
    background: linear-gradient(135deg, #d1e9ff 0%, #e1f0ff 100%);
    transform: translateX(3px);
}
}

.quick-conversation-item .avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 10px;
    position: relative;
    flex-shrink: 0;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.quick-conversation-item .avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    transition: transform 0.2s ease;
}

.quick-conversation-item:hover .avatar img {
    transform: scale(1.05);
}

.quick-conversation-item .online-dot {
    width: 10px;
    height: 10px;
    background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
    border-radius: 50%;
    position: absolute;
    bottom: 0;
    right: 0;
    border: 1px solid white;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
    animation: pulse 2s infinite;
}

.quick-conversation-item .content {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding-right: 6px;
}

.quick-conversation-item .name {
    font-weight: 600;
    font-size: 13px;
    color: #1a1a1a;
    margin-bottom: 2px;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.quick-conversation-item.unread .name {
    color: #0066cc;
    font-weight: 700;
}

.quick-conversation-item .last-message {
    font-size: 11px;
    color: #65676b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
    font-weight: 400;
    max-width: 200px;
}

.quick-conversation-item.unread .last-message {
    color: #0066cc;
    font-weight: 500;
}

.quick-conversation-item .meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-left: 8px;
    flex-shrink: 0;
    min-width: 50px;
}

.quick-conversation-item .time {
    font-size: 11px;
    color: #8a8d91;
    margin-bottom: 2px;
    font-weight: 400;
    white-space: nowrap;
}

.quick-conversation-item.unread .time {
    color: #0066cc;
    font-weight: 600;
}

.quick-conversation-item .unread-count {
    background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
    min-width: 18px;
    text-align: center;
    font-weight: 700;
    box-shadow: 0 1px 4px rgba(0, 102, 204, 0.3);
    animation: bounce 0.5s ease-in-out;
}

.quick-chat-footer {
    padding: 10px 15px;
    border-top: 1px solid #f0f0f0;
    background: #f8f9fa;
    border-radius: 0 0 12px 12px;
}

.active-quick-chats {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.quick-chat-window {
    width: 300px;
    height: 400px;
    background: white;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.15);
    border: 1px solid #e0e0e0;
    border-bottom: none;
    display: flex;
    flex-direction: column;
    animation: slideUpFadeIn 0.3s ease-out;
}

.quick-chat-window-header {
    padding: 12px 15px;
    background: linear-gradient(135deg, #0066cc 0%, #004499 100%);
    color: white;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.quick-chat-window-header .user-info {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.quick-chat-window-header .user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 10px;
    object-fit: cover;
    flex-shrink: 0;
}

.quick-chat-window-header .user-name {
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.quick-chat-window-header .actions {
    display: flex;
    gap: 5px;
    flex-shrink: 0;
}

.quick-chat-window-header .btn {
    padding: 6px 8px;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 12px;
    min-width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-chat-window-header .btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.quick-chat-messages {
    flex: 1;
    padding: 12px;
    overflow-y: auto;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    min-height: 200px;
}

.quick-message {
    margin-bottom: 10px;
    display: flex;
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.quick-message.own {
    justify-content: flex-end;
}

.quick-message-bubble {
    max-width: 75%;
    padding: 10px 14px;
    border-radius: 18px;
    font-size: 13px;
    line-height: 1.4;
    word-wrap: break-word;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    position: relative;
}

.quick-message.own .quick-message-bubble {
    background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.quick-message:not(.own) .quick-message-bubble {
    background: white;
    color: #333;
    border: 1px solid #e6e6e6;
    border-bottom-left-radius: 4px;
}

.quick-message-time {
    font-size: 10px;
    opacity: 0.8;
    margin-top: 6px;
    text-align: right;
    font-weight: 500;
}

.quick-chat-input {
    padding: 15px;
    border-top: 1px solid #e0e0e0;
    background: white;
    border-radius: 0 0 12px 12px;
}

.quick-input-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.quick-input-form input {
    flex: 1;
    border: 2px solid #e6e6e6;
    border-radius: 25px;
    padding: 10px 16px;
    font-size: 13px;
    outline: none;
    transition: all 0.2s;
    background: #f8f9fa;
    font-family: inherit;
}

.quick-input-form input:focus {
    border-color: #0066cc;
    background: white;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

.quick-input-form input::placeholder {
    color: #999;
    font-style: italic;
}

.quick-input-form button {
    background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0, 102, 204, 0.3);
    flex-shrink: 0;
}

.quick-input-form button:hover {
    background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%);
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 102, 204, 0.4);
}

.quick-input-form button:active {
    transform: scale(0.95);
}

.quick-input-form button i {
    font-size: 14px;
}

.quick-chat-empty {
    padding: 30px 20px;
    text-align: center;
    color: #666;
}

.quick-chat-empty i {
    font-size: 28px;
    margin-bottom: 12px;
    opacity: 0.6;
    color: #0066cc;
}

.quick-chat-empty p {
    margin: 0;
    font-size: 13px;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .quick-chat-container {
        right: 10px;
    }
    
    .quick-chat-list {
        width: 280px;
        max-height: 350px;
    }
    
    .quick-chat-window {
        width: 260px;
        height: 350px;
    }
    
    .quick-conversation-item {
        padding: 8px 12px;
        min-height: 48px;
    }
    
    .quick-conversation-item .avatar {
        width: 28px;
        height: 28px;
        margin-right: 8px;
    }
    
    .quick-conversation-item .name {
        font-size: 12px;
    }
    
    .quick-conversation-item .last-message {
        font-size: 10px;
        max-width: 140px;
    }
    
    .quick-conversation-item .time {
        font-size: 9px;
    }
    
    .quick-messages {
        font-size: 12px;
    }
    
    .quick-input-form input {
        font-size: 12px;
        padding: 8px 14px;
    }
    
    .quick-input-form button {
        width: 36px;
        height: 36px;
    }
}

@media (max-width: 480px) {
    .quick-chat-window {
        width: 250px;
        height: 320px;
        right: 5px;
    }
    
    .quick-chat-list {
        width: 250px;
        right: 5px;
    }
    
    .quick-chat-icon {
        right: 15px;
        bottom: 15px;
        width: 50px;
        height: 50px;
    }
}

/* Custom Scrollbars */
.quick-messages::-webkit-scrollbar {
    width: 6px;
}

.quick-messages::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 10px;
}

.quick-messages::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #e0e0e0 0%, #c0c0c0 100%);
    border-radius: 10px;
    border: 1px solid transparent;
}

.quick-messages::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #c0c0c0 0%, #a0a0a0 100%);
}

.quick-conversation-list::-webkit-scrollbar {
    width: 4px;
}

.quick-conversation-list::-webkit-scrollbar-track {
    background: transparent;
}

.quick-conversation-list::-webkit-scrollbar-thumb {
    background: #d0d0d0;
    border-radius: 6px;
}

.quick-conversation-list::-webkit-scrollbar-thumb:hover {
    background: #b0b0b0;
}

/* Animation for new messages */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-3px); }
    60% { transform: translateY(-2px); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUpFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.new-message-pulse {
    animation: pulse 0.6s ease-in-out;
}

.conversation-appear {
    animation: fadeInUp 0.4s ease-out;
}

/* Improved conversation list hover effects */
.quick-conversation-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 0;
    background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
    transition: width 0.3s ease;
    border-radius: 0 4px 4px 0;
}

.quick-conversation-item:hover::before {
    width: 4px;
}

.quick-conversation-item.unread::before {
    width: 4px;
}

/* Empty state styling */
.quick-conversations-empty {
    padding: 40px 20px;
    text-align: center;
    color: #8a8d91;
}

.quick-conversations-empty i {
    font-size: 32px;
    margin-bottom: 12px;
    opacity: 0.6;
    color: #0066cc;
}

.quick-conversations-empty p {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
}

/* See more link styling */
.quick-see-more {
    border-top: 1px solid #f0f2f5;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.quick-see-more a {
    transition: all 0.2s ease;
    border-radius: 0 0 12px 12px;
}

.quick-see-more a:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
    color: #0052a3 !important;
}

/* Loading state */
.quick-conversations-loading {
    animation: fadeInUp 0.3s ease-out;
}

/* Minimized state */
.quick-chat-minimized .quick-chat-messages {
    display: none;
}

.quick-chat-minimized .quick-chat-input {
    display: none;
}

.quick-chat-minimized {
    height: auto;
}
</style>
