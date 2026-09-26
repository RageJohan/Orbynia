# Alta manual de un CLIENTE/EMPRESA en ORBYNIA

La landing mantiene una base de datos central para contactos y solicitudes. El Panel de MINKA nunca crea una instancia ni escribe en la base de datos de un ERP.

## Accesos

- orbynia.com: sitio público y formularios; no contiene login del ERP.
- admin.orbynia.com: Panel de MINKA para operadores internos. En desarrollo local se usa /panel-minka.
- [cliente].orbynia.com: instancia ERP exclusiva del CLIENTE/EMPRESA.
- App móvil ORBYNIA: conecta directamente con [cliente].orbynia.com/api/.

## Primer operador del Panel de MINKA

Después de configurar la base central y migrarla, ejecutar php artisan orbynia:create-operator --name="Nombre" --email="correo@empresa.com" dentro de la carpeta de la landing. El comando pide la contraseña de forma interactiva; no se pasa por argumentos.

## Activación asistida

1. Revisar en el Panel de MINKA la solicitud con correo verificado.
2. Acordar alcance y vencimiento; aprobar. Esto solo reserva el subdominio en la base central.
3. El operador despliega manualmente una copia aislada del código ERP, crea las bases MySQL y PostgreSQL propias, los archivos propios y el host HTTPS. No se reutilizan las bases o los archivos de otra empresa.
4. Configurar APP_URL=https://[cliente].orbynia.com y ORBYNIA_TENANT_SLUG=[cliente]. En la base MySQL nueva y vacía, importar el esquema base, ejecutar `php artisan migrate` y `php artisan db:seed --class=OrbyniaPermissionSeeder` para instalar el catálogo de permisos sin copiar usuarios ni datos de Cobeles. Preparar también la base PostgreSQL de GPS. Solo después activar ORBYNIA_TENANT_MODE=true.
5. Dentro de esa instancia, ejecutar php artisan minka:configure-instance --slug=[cliente] --status=evaluation --ends-at=AAAA-MM-DD --modules=ventas,inventario usando exactamente los módulos aprobados en el panel.
6. Dentro de esa misma instancia, ejecutar php artisan minka:create-admin --name="Nombre" --email="correo@cliente.com" --username="usuario" --dni="..." --phone="...". El comando crea al primer ADMIN del ERP y le envía un enlace para definir su clave. No envía contraseña por correo.
7. Comprobar el acceso al ERP y que llegó la invitación. Solo entonces introducir en el Panel de MINKA el correo del primer ADMIN y pulsar «Marcar activa». Ese botón registra la finalización del trabajo manual; no ejecuta los pasos anteriores.

El ERP ya contiene un volcado de estructura MySQL y un catálogo inicial de permisos. Antes de habilitar nuevas instancias falta comprobar la restauración del volcado en una base vacía y preparar la estructura PostgreSQL de GPS. No se debe reutilizar la base ni los archivos de Cobeles.

## Acceso central de la landing

La ruta `/acceder` permite solicitar el servicio o localizar una instancia activa por código de empresa o correo del primer ADMIN. Abre el dominio del cliente en otra pestaña. El ERP valida sus credenciales allí; la landing no recibe su contraseña. El correo o nombre de un operador de MINKA lleva al login del Panel de MINKA. En desarrollo local, `cobeles` abre el ERP en `http://127.0.0.1:8000` sin registrar una instancia comercial ficticia.

## Correo y dominio

Configurar correo transaccional real, DNS y HTTPS antes de usar este flujo con clientes. La configuración local MAIL_MAILER=log escribe los enlaces de verificación en el registro del servidor y no entrega correos.

