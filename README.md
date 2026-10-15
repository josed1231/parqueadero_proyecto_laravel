# API REST - Sistema de Gestion de Parqueadero (Laravel 11)

API RESTful desarrollada en Laravel 11 para la gestion integral de un parqueadero. El sistema contempla el control de acceso vehicular, liberacion automatizada de bahias, calculo dinamico de tarifas por tiempo de estadia y liquidacion con facturacion y pagos en tiempo real.

---

## Tecnologias y Paquetes Utilizados

* Framework: Laravel 11 (PHP 8.2+)
* Base de Datos: MySQL
* Autenticacion: Laravel Sanctum (`laravel/sanctum`)
* Manejo de Fechas y Tiempo: Carbon (`nesbot/carbon`)
* ORM: Eloquent
* Herramienta de Pruebas: Postman

---

## Entidades Principales y Relaciones

1. User: Propietarios de los vehiculos registrados.
2. Vehiculo: Unidades vinculadas a un usuario (user_id).
3. Espacio: Bahias de parqueo con estados automaticos (Disponible / Ocupado).
4. Ingreso: Control del tiempo de entrada y salida del vehiculo.
5. Factura: Liquidacion del costo total calculado por horas cobradas.
6. Pago: Transaccion final para cancelar la factura (Pagado).

---

## Guia de Instalacion y Despliegue Local

Sigue estos pasos para clonar, instalar dependencias, configurar e inicializar el proyecto en tu entorno local:

### 1. Clonar el Repositorio

git clone [https://github.com/josed1231/parqueadero_proyecto_laravel.git](https://github.com/josed1231/parqueadero_proyecto_laravel.git)
cd parqueadero_proyecto_laravel
2. Instalar Dependencias del Proyecto
Ejecuta Composer para instalar el framework y las librerias requeridas:

composer install
Si estas configurando el proyecto desde cero e instalando los paquetes manualmente, estos son los comandos que se utilizaron:

# Instalacion de Laravel Sanctum para API Token Authentication
composer require laravel/sanctum

# Publicacion de la configuracion de Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Instalacion/Verificacion de Carbon para manejo de fechas
composer require nesbot/carbon

---

3. Configurar el Archivo de Entorno
Copia el archivo de ejemplo a tu .env local:

cp .env.example .env
Asegurate de ajustar las credenciales de tu base de datos MySQL en el archivo .env:

Ini, TOML
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parqueadero_db
DB_USERNAME=root
DB_PASSWORD=tu_contrasena

---

4. Generar la Clave de la Aplicacion

php artisan key:generate

---

5. Ejecutar las Migraciones de la Base de Datos
Ejecuta las migraciones para crear la estructura de tablas limpia con sus llaves foraneas:

php artisan migrate:fresh

---

6. Iniciar el Servidor de Desarrollo

php artisan serve
