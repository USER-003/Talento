<div align="center">
  <h1>TALENTO</h1>
</div>

# Descripción

Talento es una plataforma digital que conecta a proveedores de servicios con clientes. Ofrece una interfaz segura y fácil de usar para explorar servicios, acceder a perfiles de proveedores y comunicarse directamente. Talento facilita la búsqueda y contratación de servicios profesionales, creando un entorno colaborativo y transparente para todos los usuarios.

## Tecnologias

-   PHP (Versión 8.2.12)
-   Laravel (Versión 11.8.0)
-   PostgreSQL (Release 16.2)

## Instrucciones de ejecución

1. Clonar el respositorio
2. Ejecutar 'composer install' (Si ocurre algun error asegurarse de tener los permisos necesarios y que el antiviruz no este interfiriendo)
3. Renombrar '.env.example' a '.env'
4. Generar la clave de la app 'php artisan key:generate'
5. Configurar las variables de entorno

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. Restaura la base de datos

## Como restaturar la base de datos

1. Ubica el backup en "storage/app/Laravel/dump.backup" dentro de la app
2. Crea una nueva base de datos en PostgreSQL
3. Restaura la base de datos con el siguiente comando:

```
pg_restore -U tu_usuario -d nombre_de_la_base_de_datos /ruta/a/tu_respaldo.backup
```
