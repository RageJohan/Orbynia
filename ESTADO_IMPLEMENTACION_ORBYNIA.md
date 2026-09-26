# Estado de implementación de ORBYNIA

> Documento de continuidad anterior. Para el resumen completo y el estado más reciente, consultar `RESUMEN_INTEGRAL_ORBYNIA.md`.

**Fecha de corte:** 26 de septiembre de 2026.  
**Propósito:** dejar un punto de continuidad sobre el flujo comercial, la creación manual de instancias y el acceso móvil.

## 1. Problema que se está resolviendo

ORBYNIA tiene tres componentes distintos. La **landing** informa sobre el producto y recibe solicitudes comerciales. El **Panel de MINKA** permite a su equipo revisar esas solicitudes. El **ERP y la app móvil** son el producto que recibe cada CLIENTE/EMPRESA, con una instancia del ERP, bases de datos y archivos propios.

Los botones de la landing enviaban al visitante a redactar un correo. Eso impedía registrar, ordenar y seguir solicitudes desde un panel. Además, era necesario definir cómo el operador entregaría la instancia sin mezclar los datos comerciales centrales con los datos del cliente.

## 2. Decisiones aprobadas

- **CLIENTE/EMPRESA:** empresa que solicita contratar ORBYNIA.
- **ADMIN:** primer usuario administrador dentro de la instancia ERP de ese cliente.
- **Panel de MINKA:** área interna prevista en `admin.orbynia.com` para revisar solicitudes; no es el ERP del cliente.
- La landing recibe la solicitud y el Panel de MINKA la revisa. **Aprobar una solicitud no crea la instancia automáticamente.**
- El operador de MINKA crea y configura manualmente la instancia aislada; después ejecuta los comandos de configuración y creación del ADMIN y marca la solicitud como activa.
- Por decisión posterior del usuario, el código base de ORBYNIA se desarrolla en el **ERP local actual**. La copia separada se archivó después de trasladar y comparar los archivos; ya no es una carpeta de trabajo activa.
- El subdominio solicitado pertenece a la instancia del cliente, por ejemplo `empresa.orbynia.com`. La app móvil selecciona esa empresa antes del inicio de sesión.

## 3. Flujo previsto

```text
Visitante en orbynia.com
  → formulario de contacto o solicitud de evaluación
  → solicitud guardada en la base central de la landing
  → verificación de correo mediante enlace
  → operador revisa y aprueba desde el Panel de MINKA
  → operador crea manualmente servidor, bases, archivos, HTTPS y subdominio
  → operador configura la instancia ERP y crea su primer ADMIN
  → invitación por correo para establecer contraseña
  → operador marca la solicitud como activa
  → CLIENTE/EMPRESA usa su ERP y la app móvil con su propio subdominio
```

La solicitud comercial no crea una cuenta en el ERP. El formulario de evaluación no pide contraseña. La contraseña del ADMIN se establece mediante una invitación de un solo uso después de que el operador haya preparado la instancia.

## 4. Cambios realizados y ubicación

### Landing y Panel de MINKA

**Carpeta:** `C:\Users\venta\OneDrive\Escritorio\orbynia-landing`

- Migración `database/migrations/2026_09_26_000004_create_commercial_flow.php`: usuarios operadores, prospectos, solicitudes y eventos.
- `app/Http/Controllers/CommercialController.php`: contacto, evaluación, disponibilidad de subdominio y verificación de correo.
- `app/Http/Controllers/MinkaPanelController.php`: acceso del operador, revisión, aprobación, rechazo y activación registrada.
- Comando `app/Console/Commands/CreateMinkaOperator.php` para crear un usuario operador.
- Vistas de formularios, acceso y panel en `resources/views/`; rutas en `routes/web.php`; estilos y comportamiento en `resources/css/app.css` y `resources/js/app.js`. La página `/acceder` orienta por código de empresa o correo y abre la instancia en otra pestaña; las contraseñas del ERP se ingresan solo en esa instancia.
- Los botones comerciales de la página principal llevan ahora a formularios; se añadieron planes de referencia.
- El Panel de MINKA registra al activar una empresa el correo del primer ADMIN para localizar su dominio desde `/acceder`. El correo o nombre de un operador interno conduce al login del Panel de MINKA. En local, el código `cobeles` abre `http://127.0.0.1:8000`. Guía operativa: `docs/alta-manual-cliente.md`.
- Las migraciones locales de la landing, incluida la columna `admin_email`, se ejecutaron; la sintaxis PHP, las vistas y la compilación de recursos se comprobaron. El correo local usa el transportador `log`, por lo que la entrega real requiere SMTP.

### Código base ERP de ORBYNIA

**Carpeta principal:** `C:\xampp\htdocs\CobelesApp\cobelesapp`  
**Copia anterior:** worktree de ORBYNIA archivado por Codex; sus cambios quedaron incorporados en la carpeta principal.

- Migración `database/migrations/2026_09_26_000001_prepare_orbynia_tenant_access.php` para campos del ADMIN y estado de la instancia.
- `app/Console/Commands/ConfigureTenantAccessCommand.php`: comando `minka:configure-instance` para registrar subdominio, estado, vencimiento y módulos.
- `app/Console/Commands/CreateTenantAdminCommand.php`: comando `minka:create-admin` para el primer ADMIN e invitación de contraseña.
- `app/Http/Middleware/EnsureTenantAccess.php` y `bootstrap/app.php`: control de acceso de instancia, habilitado mediante `ORBYNIA_TENANT_MODE=true`. Se amplió la cobertura de rutas de pedidos, GPS, logística, vehículos y API.
- Ajustes de autenticación, recuperación de contraseña, correo, configuración y rutas; se retiró el registro público del ERP. `database/seeders/OrbyniaPermissionSeeder.php` instala 167 permisos genéricos desde `database/seeders/data/orbynia_permissions.json` en cada instancia nueva, sin copiar usuarios ni operaciones de Cobeles.
- El 26/09/2026 se trasladaron 18 archivos de ORBYNIA al ERP local actual por petición del usuario. Antes del traslado, el repositorio local estaba limpio y ambos proyectos partían del mismo commit. Se compararon los 18 archivos por SHA-256: ninguna diferencia. Se aplicó la migración ORBYNIA a la base local `cobeles_erp`. El modo de instancia sigue desactivado en la configuración local.

### App móvil Android

**Carpeta:** `C:\Users\venta\AndroidStudioProjects\Cobeles`

- `TenantSettings.kt`: guarda la empresa seleccionada y construye su URL `https://subdominio.orbynia.com/api/`.
- `RetrofitClient.kt`: dirige las peticiones a la instancia seleccionada.
- `LoginActivity.kt`: selector de empresa y consulta de `/api/instance-info` antes del inicio de sesión.
- Ajustes de pantalla de login y Gradle para la URL de desarrollo. El identificador de aplicación aprobado previamente es `com.orbynia.movil`.

## 5. Pendientes antes de usarlo en producción

1. **Base inicial del ERP:** el volcado MySQL contiene 103 tablas y 10 entradas de migraciones, pero `mysqldump` mostró `unknown variable 'column-statistics=0'`; falta verificar una restauración en una base nueva antes de usarlo para clientes. Preparar también la base PostgreSQL de GPS si corresponde.
2. **Cobertura del control de módulos:** se corrigieron rutas evidentes de pedidos, operación GPS, vehículos, transportistas, ubicaciones y configuración comercial. Falta definir el alcance comercial de compras, tesorería, finanzas y otras áreas no incluidas en los cinco módulos aprobables; después completar su control y revisar rutas compartidas.
3. **Android:** `assembleDebug` compiló correctamente con el JDK de Android Studio1. Falta probar el flujo en un dispositivo y con una instancia real publicada.
4. **Configuración por cliente:** la migración `2026_09_26_000001_prepare_orbynia_tenant_access` ya se aplicó en el ERP local. Para cada nueva instancia faltará instalar su propia base, ejecutar sus migraciones y el seeder de permisos, y activar `ORBYNIA_TENANT_MODE=true` con el subdominio correcto. No activar este modo en Cobeles sin configurar esa instancia.
5. **Correo real:** configurar SMTP y dominio de envío para verificación de solicitudes e invitación del ADMIN; el entorno local de la landing solo registra correos en el log.
6. **Infraestructura y dominio:** configurar DNS, HTTPS y alojamiento para `orbynia.com`, `admin.orbynia.com` y los subdominios de clientes. No se ha desplegado el servicio público.
7. **Operación:** ya existe un operador del Panel de MINKA en local. Falta fijar planes y precios definitivos si se cobrarán en línea, y completar el procedimiento manual de alta con credenciales aisladas y copia de seguridad.

## 6. Estado actual

El flujo comercial, el panel interno, los comandos base del ERP y la selección de instancia móvil están implementados en código. **Aún no hay una instancia de cliente creada ni un despliegue público.** La aprobación en el panel registra la decisión comercial; el aprovisionamiento sigue siendo una operación manual de MINKA.





