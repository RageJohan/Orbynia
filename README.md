# ORBYNIA · sitio público

Landing oficial de ORBYNIA para https://orbynia.com. Es una aplicación Laravel 12 independiente del ERP Cobeles en desarrollo y de las futuras instancias de clientes. Usa sus propias rutas, configuración y base de datos; no monta ni consulta el ERP.

## Alcance de esta primera versión

- Portada comercial adaptable a móvil con identidad ORBYNIA.
- Política de privacidad, términos y condiciones.
- Libro de Reclamaciones con registro correlativo y constancia imprimible.
- Solicitud de eliminación de cuenta con número de seguimiento.
- Formularios comerciales de contacto y evaluación, con verificación de correo.
- Panel interno de MINKA para revisar solicitudes y registrar la activación manual.
- Página `/acceder` para localizar el ERP de una empresa activa o entrar al Panel de MINKA.
- Contacto público: info@minka360.com.
- Recursos de marca extraídos de brand/identidad-visual.pdf. Las tipografías Montserrat y Poppins se sirven localmente.

Los números y gráficos del panel de la portada son ilustrativos, no datos de un cliente real. El acceso a empresas depende de que MINKA haya marcado su instancia como activa; en local, el código `cobeles` abre el ERP local.

## Arquitectura

orbynia.com: esta landing y sus formularios públicos.

Instancia de cada cliente: su ERP, sus usuarios, su base de datos y sus archivos.

La landing no almacena ni valida contraseñas del ERP. Su base de datos propia almacena reclamaciones, solicitudes de eliminación, contactos, solicitudes comerciales y las cuentas de operadores del Panel de MINKA. Los datos de representantes de menores pertenecen exclusivamente al Libro de Reclamaciones y solo se solicitan cuando se presenta una hoja en ese supuesto. Cada despliegue debe tener un APP_KEY propio. No compartir el archivo SQLite entre proyectos.

APP_KEY es la clave interna de Laravel para sesiones y cifrado; no es una clave de API de Google, NubeFact ni otro proveedor. La del entorno local ya fue generada. Una instalación de producción debe generar y guardar la suya.

## Desarrollo local

Requisitos: PHP 8.2+, Composer y Node.js 20.19+.

1. Ejecutar composer install y npm install.
2. Copiar .env.example a .env y ejecutar php artisan key:generate.
3. Crear database/database.sqlite y ejecutar php artisan migrate.
4. Ejecutar npm run build y php artisan serve.

Si el archivo .env y la base SQLite ya existen, conservarlos. APP_URL local es http://localhost:8000.

## Despliegue pendiente en orbynia.com

1. Preparar un hosting compatible con Laravel 12 y apuntar el document root a public/. Configurar DNS de orbynia.com y www según el hosting, con HTTPS y redirección a un único host canónico.
2. Configurar .env de producción: APP_ENV=production, APP_DEBUG=false, APP_URL=https://orbynia.com, APP_TIMEZONE=America/Lima, un APP_KEY nuevo y conexión a base de datos propia de la landing. Dar permisos de escritura a storage y bootstrap/cache.
3. Configurar un correo transaccional real para enviar acuses al consumidor y avisos a info@minka360.com. Con MAIL_MAILER=log los formularios se guardan y la constancia se imprime, pero no se envía correo.
4. Ejecutar composer install --no-dev --optimize-autoloader, npm ci, npm run build, php artisan migrate --force y php artisan optimize.
5. Habilitar respaldos y acceso restringido a la base de datos central; asignar personas responsables de revisar solicitudes comerciales, reclamaciones y solicitudes de eliminación. El Panel de MINKA gestiona solicitudes comerciales, pero la atención de reclamaciones y eliminación aún requiere un procedimiento operativo definido.
6. Confirmar el texto legal final y que la política publicada describa exactamente el comportamiento de la versión de Android que se lanzará. Revisar en producción las URL públicas de privacidad, Libro de Reclamaciones, términos y solicitud de eliminación antes de registrar la web en Google Play Console.

No desplegar el ERP local junto a esta aplicación. La ruta `/acceder` abre el dominio de la instancia activa en otra pestaña; cada ERP valida sus propias credenciales. El alta técnica de cada instancia la realiza manualmente un operador de MINKA. Véanse `docs/alta-manual-cliente.md` y `RESUMEN_INTEGRAL_ORBYNIA.md`.

## Carpetas principales

- resources/views/public: vistas Blade.
- resources/css/app.css y resources/js/app.js: diseño e interacción.
- public/images/orbynia: logotipos extraídos de la identidad.
- database/migrations: tablas propias de reclamaciones y eliminación.
- config/orbynia.php: identidad legal y contacto.
- brand/identidad-visual.pdf: referencia de marca; fuera del directorio público.
