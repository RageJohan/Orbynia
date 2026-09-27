# ORBYNIA · landing y Panel de MINKA

Aplicación Laravel 12 para el sitio público `orbynia.com` y el Panel de MINKA. Es independiente del ERP web de cada cliente y de la app móvil. La landing recibe solicitudes y conserva los datos comerciales centrales; cada empresa usa su propio ERP, base de datos, archivos y subdominio (`empresa.orbynia.com`).

## Estado actual · 27 de septiembre de 2026

La primera versión funcional de la landing y del Panel de MINKA está desarrollada en local. Las migraciones de la base central local están aplicadas. Las vistas Blade, la sintaxis PHP de los cambios recientes y los recursos Vite se han compilado correctamente. Esto no sustituye un recorrido completo con una instancia aislada de cliente ni la comprobación de entrega de correos reales.

No se ha desplegado `orbynia.com` ni `admin.orbynia.com`. El entorno local utiliza SQLite y `MAIL_MAILER=log`: los mensajes quedan en el registro de Laravel y no llegan a una bandeja de entrada. Los planes de la portada son referenciales; todavía falta fijar el alcance y los precios comerciales.

## Avance realizado

### Sitio público

- Portada adaptable a móvil con identidad ORBYNIA, presentación del ERP y la app, planes referenciales y enlaces a contacto y evaluación. Los números y gráficos del panel de la portada son ilustrativos.
- Páginas de términos y condiciones, política de privacidad, Libro de Reclamaciones y solicitud de eliminación de cuenta.
- Libro de Reclamaciones con referencia correlativa y constancia consultable e imprimible; solicitud de eliminación con número de seguimiento.
- Formulario de contacto para consultas y demostraciones, y formulario de evaluación con empresa, responsable, plan de interés y subdominio deseado. Ninguno crea una cuenta del ERP ni solicita su contraseña.
- Comprobación de disponibilidad del subdominio. Tras confirmar el correo, el sistema intenta reservarlo durante la revisión; si otro solicitante lo reservó antes, permite escoger otro nombre.
- Página que muestra el estado del intento de envío del correo de verificación, reenvío con intervalo mínimo de un minuto y recuperación del enlace mediante el correo de la solicitud. El enlace firmado de confirmación dura 24 horas. En local, el estado indica que el mensaje quedó solo en el log.
- Página `/acceder` para localizar una empresa activa por código o correo del primer ADMIN y abrir su ERP en otra pestaña. En desarrollo local, el código `cobeles` abre el ERP local. La landing nunca valida contraseñas de usuarios del ERP.

### Panel interno de MINKA

- Acceso reservado a operadores internos; disponible en `/panel-minka` en local y previsto en `admin.orbynia.com` en producción.
- Listas de contactos y solicitudes de evaluación. Para cada uno se puede asignar responsable comercial, etapa y próxima fecha de seguimiento, y registrar llamadas, correos, reuniones, demostraciones y notas en un historial.
- Registro de propuestas con monto, periodo, módulos, alcance, vigencia y estado. El registro es interno: guardar una propuesta no la envía al cliente. El panel muestra los seguimientos vencidos y los previstos dentro de siete días; no envía recordatorios automáticos.
- Gestión de reclamaciones y solicitudes de eliminación con estado, responsable, notas, respuesta e historial de cambios. El envío real de respuestas por correo requiere un transportador de producción.
- Revisión, aprobación y rechazo de solicitudes de evaluación, además del registro de activación después del aprovisionamiento manual. La etapa comercial y el estado de alta son independientes. Aprobar o marcar activa no crea un servidor, una base de datos ni un usuario ERP.

## Flujo de alta de una empresa

1. El visitante envía una consulta o una solicitud de evaluación desde la landing. La evaluación intenta enviar un enlace de verificación; si no llega, el solicitante puede pedir otro.
2. Al confirmar el correo se intenta reservar el subdominio solicitado. El operador de MINKA registra el seguimiento comercial, acuerda el alcance y puede aprobar la evaluación.
3. MINKA prepara manualmente una instancia ERP aislada, con sus bases y archivos, configura módulos y vencimiento, y crea al primer ADMIN mediante una invitación para establecer contraseña.
4. Tras comprobar que la instancia y la invitación funcionan, el operador registra el correo del primer ADMIN y pulsa «Marcar activa». La empresa ya puede localizar su ERP desde `/acceder`; sus colaboradores usan ese mismo subdominio desde la app móvil.

La guía detallada está en [docs/alta-manual-cliente.md](docs/alta-manual-cliente.md). El ERP base contiene comandos de configuración de instancia y creación del ADMIN, pero todavía no se ha preparado una instancia aislada de prueba para completar este recorrido de extremo a extremo.

## Pendiente que puede trabajarse en local

1. Definir el contenido de los planes, módulos, periodo de evaluación, precios y condiciones de la oferta; después ajustar los textos comerciales y las propuestas.
2. Revisar los textos públicos y el procedimiento de atención de reclamaciones y eliminaciones contra el servicio y la versión final de la app. Acordar responsables y forma de respuesta.
3. Preparar una instancia ERP de prueba separada de Cobeles: comprobar la restauración del esquema MySQL, preparar PostgreSQL para GPS, instalar migraciones y permisos, y cerrar el control de módulos y rutas pendientes.
4. Recorrer con datos de prueba la solicitud, verificación por el log local, reserva, seguimiento, aprobación, aprovisionamiento manual, activación y acceso desde web y app. La compilación de la app Android debug ya se realizó, pero falta comprobar este recorrido en un dispositivo y una instancia de prueba.
5. Mantener sincronizados esta guía, los resúmenes de implementación y los textos públicos cuando cambie la oferta o el flujo.

## Pendiente para producción

1. Publicar la landing y el panel con DNS, HTTPS, base central propia, `APP_KEY` exclusivo, acceso restringido, copias de seguridad y monitoreo. La infraestructura pública aún no está configurada desde este proyecto.
2. Configurar y comprobar un servicio de correo transaccional real para verificaciones, reenvíos, avisos, respuestas e invitaciones. Un envío aceptado por el servidor no garantiza por sí solo que llegue a la bandeja del destinatario.
3. Configurar DNS y HTTPS para los subdominios de clientes y desplegar cada ERP con bases, archivos y credenciales aislados. La reserva comercial del nombre no crea automáticamente esa infraestructura.
4. Completar un recorrido real con correo, dominio, ERP y app antes de incorporar clientes; comprobar también los respaldos y el procedimiento operativo de atención.
5. Validar los textos legales finales y las URL públicas que se presentarán para la publicación de la app móvil.

## Desarrollo local

Requisitos: PHP 8.2+, Composer y Node.js 20.19+.

1. Ejecutar `composer install` y `npm install`.
2. Copiar `.env.example` a `.env` y ejecutar `php artisan key:generate`.
3. Crear `database/database.sqlite` y ejecutar `php artisan migrate`.
4. Ejecutar `npm run build` y `php artisan serve`.
5. Crear el primer operador con `php artisan orbynia:create-operator --name="Nombre" --email="correo@empresa.com"`; el comando pide la contraseña de forma interactiva.

Si `.env` y la base SQLite ya existen, conservarlos. Cada despliegue necesita su propio `APP_KEY`. No compartir la base central de la landing con una instancia ERP ni subir `.env`, bases de datos locales o credenciales al repositorio.

## Archivos principales

- `resources/views/public/`: portada, acceso, formularios y páginas legales.
- `resources/views/panel/`: Panel de MINKA, seguimiento comercial y gestión de solicitudes.
- `app/Http/Controllers/CommercialController.php`: contacto, evaluación, verificación y reenvío.
- `app/Http/Controllers/SalesTrackingController.php`: etapas, actividades y propuestas comerciales.
- `app/Http/Controllers/MinkaPanelController.php`: operación interna y alta manual.
- `resources/css/app.css` y `resources/js/app.js`: diseño e interacción.
- `database/migrations/`: estructura de la base central.
- `config/orbynia.php`: identidad y contacto de MINKA.
- `brand/identidad-visual.pdf`: referencia de marca fuera del directorio público.
