# 💬 Sistema de Quick Chat - Estilo LinkedIn

![Quick Chat Preview](https://img.shields.io/badge/Status-✅_Implementado-success)
![Version](https://img.shields.io/badge/Version-1.0.0-blue)
![Laravel](https://img.shields.io/badge/Laravel-11.x-red)

## 📋 Descripción

Sistema de chat rápido estilo LinkedIn que permite a los usuarios mantener conversaciones flotantes en la parte inferior de la pantalla sin abandonar la página actual. Incluye notificaciones en tiempo real y múltiples ventanas de chat simultáneas.

## ✨ Características Principales

### 🎯 Funcionalidades Core
- **Chat flotante**: Icono de notificación fijo en la esquina inferior derecha
- **Lista de conversaciones**: Popup con las conversaciones más recientes
- **Múltiples chats**: Hasta 3 ventanas de chat abiertas simultáneamente
- **Tiempo real**: Notificaciones push con Pusher WebSocket
- **Estados de lectura**: Contadores de mensajes no leídos
- **Indicadores online**: Punto verde para usuarios conectados

### 🎨 Diseño Visual
- **Responsive**: Adaptación automática a móviles y tablets
- **Animaciones suaves**: Efectos hover, fadeIn, slide, bounce
- **Tipografía optimizada**: Tamaños legibles y jerarquía clara
- **Avatares proporcionados**: 32px desktop, 28px móvil
- **Scrollbars personalizados**: Diseño elegante y discreto

### 🔧 Integración Técnica
- **API RESTful**: Endpoints optimizados para carga rápida
- **Cache inteligente**: Gestión eficiente de memoria
- **Formateo de fechas**: Relativo y amigable ("5 min", "Ayer")
- **Escape de HTML**: Seguridad contra XSS
- **Error handling**: Manejo robusto de fallos de conexión

## 📁 Estructura de Archivos

```
├── resources/views/components/
│   └── quick-chat.blade.php          # Componente principal
├── public/js/
│   └── quick-chat.js                 # Lógica JavaScript
├── public/css/
│   └── chat.css                      # Estilos adicionales
├── app/Http/Controllers/
│   └── ChatController.php            # API backend
├── app/Models/
│   ├── Usuario.php                   # Modelo usuario (avatar optimizado)
│   ├── Conversation.php              # Modelo conversaciones
│   ├── Message.php                   # Modelo mensajes
│   └── BlockedUser.php               # Modelo usuarios bloqueados
└── resources/views/layouts/
    └── app.blade.php                 # Integración en layout
```

## 🚀 Instalación y Configuración

### 1. Integración en Layout
```blade
{{-- En resources/views/layouts/app.blade.php --}}
@auth
    @include('components.quick-chat')
@endauth
```

### 2. Configuración Pusher
```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### 3. Rutas API
```php
// En routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/api/conversations', [ChatController::class, 'getConversations']);
    Route::get('/api/messages/{userId}', [ChatController::class, 'getMessages']);
    Route::post('/api/messages', [ChatController::class, 'sendMessage']);
});
```

## 🎮 Uso del Sistema

### Para Usuarios Finales

#### Abrir Lista de Conversaciones
1. Click en el icono de chat (esquina inferior derecha)
2. Se despliega lista con conversaciones recientes
3. Click fuera para cerrar

#### Iniciar Chat Rápido
1. Click en cualquier conversación de la lista
2. Se abre ventana de chat flotante
3. Escribir mensaje y presionar Enter o click en enviar

#### Gestión de Ventanas
- **Minimizar**: Click en `-` en la cabecera
- **Cerrar**: Click en `×` en la cabecera
- **Múltiples chats**: Hasta 3 ventanas simultáneas
- **Arrastrar**: Mover ventanas por la cabecera

### Para Desarrolladores

#### API Endpoints

**GET** `/api/conversations`
```json
{
  "id": 1,
  "other_user": {
    "id": 2,
    "name": "María García",
    "avatar": "https://ui-avatars.com/api/...",
    "is_online": true
  },
  "latest_message": {
    "message": "Hola, ¿cómo estás?",
    "created_at": "2025-08-29T10:30:00.000Z",
    "is_own": false
  },
  "unread_count": 2
}
```

**GET** `/api/messages/{userId}`
```json
{
  "conversation_id": 1,
  "messages": [
    {
      "id": 1,
      "message": "Hola!",
      "sender_id": 1,
      "created_at": "2025-08-29T10:30:00.000Z",
      "is_read": true
    }
  ]
}
```

**POST** `/api/messages`
```json
{
  "recipient_id": 2,
  "message": "Hola, ¿cómo estás?"
}
```

#### Eventos Pusher

**Notificación de nuevo mensaje:**
```javascript
// Canal: user-{userId}
// Evento: new-message
{
  "conversation": { /* datos conversación */ },
  "message": { /* datos mensaje */ }
}
```

## 🎨 Personalización CSS

### Variables CSS Principales
```css
:root {
  --quick-chat-primary: #0066cc;
  --quick-chat-bg: #ffffff;
  --quick-chat-border: #f0f2f5;
  --quick-chat-shadow: rgba(0, 0, 0, 0.15);
  --quick-chat-radius: 12px;
}
```

### Clases CSS Principales
- `.quick-chat-container`: Contenedor principal
- `.quick-conversation-item`: Elemento de conversación
- `.quick-chat-window`: Ventana de chat flotante
- `.quick-message`: Burbuja de mensaje individual
- `.quick-input-form`: Formulario de envío

## 📱 Responsive Design

### Breakpoints
- **Desktop**: > 768px - Avatar 32px, altura 52px
- **Tablet**: 481px - 768px - Avatar 28px, altura 48px  
- **Mobile**: ≤ 480px - Avatar 28px, ventanas más pequeñas

### Adaptaciones Móviles
- Ventanas de chat reducidas (250px ancho)
- Lista de conversaciones compacta
- Touch-friendly (botones 44px mínimo)
- Scrolling optimizado

## 🔍 Debugging y Troubleshooting

### Problemas Comunes

#### Avatar muy grande
**Problema**: Avatar se muestra en 200px en lugar de 32px
**Solución**: Verificar `Usuario::getAvatarUrlAttribute()` use `size=64`

#### "Invalid Date" en tiempo
**Problema**: Fechas no se formatean correctamente
**Solución**: Usar `toISOString()` en lugar de `format()` en el controlador

#### Pusher no conecta
**Problema**: Notificaciones no llegan
**Solución**: Verificar configuración `.env` y canal `user-{userId}`

#### JavaScript "function not defined"
**Problema**: Funciones onclick no encontradas
**Solución**: Mover funciones fuera del DOMContentLoaded

### Logs y Debugging
```javascript
// Habilitar debug en quick-chat.js
window.quickChatDebug = true;

// Ver logs en consola
console.log('QuickChat:', window.quickChatSystem);
```

## 🔄 Actualizaciones y Mantenimiento

### Versión 1.0.0 (Actual)
- ✅ Sistema base implementado
- ✅ Diseño responsive
- ✅ Integración Pusher
- ✅ API optimizada

### Roadmap Futuro
- 🔄 Emojis y attachments
- 🔄 Búsqueda en conversaciones
- 🔄 Tema oscuro
- 🔄 Notificaciones navegador
- 🔄 Videollamadas integradas

## 👥 Contribución

### Estructura del Código
1. **HTML**: Componente Blade limpio y semántico
2. **CSS**: BEM methodology, variables CSS
3. **JavaScript**: Clases ES6, async/await
4. **PHP**: PSR-12, dependency injection

### Testing
```bash
# Limpiar cache
php artisan cache:clear

# Verificar rutas
php artisan route:list | grep chat

# Test manual en navegador
# 1. Abrir herramientas de desarrollador
# 2. Click en icono de chat
# 3. Verificar red (API calls)
# 4. Verificar consola (errores JS)
```

## 📄 Licencia

Este sistema es parte del proyecto Talento y sigue la misma licencia del proyecto principal.

---

**Desarrollado con ❤️ para mejorar la comunicación entre usuarios**

*Última actualización: Agosto 29, 2025*
