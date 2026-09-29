# Estado de implementación de ORBYNIA

> Documento de continuidad. Distingue el MVP acordado de lo que ya existe en código.

**Fecha de actualización:** 29 de septiembre de 2026.

## 1. Objetivo y límites del producto

La landing `orbynia.com` presenta ORBYNIA como producto (ERP web y app móvil), recibe consultas y solicitudes, y permite a MINKA 360 S.A.C. coordinarlas desde un panel. La landing usa su propia base central para las solicitudes y su seguimiento.

Cada CLIENTE/EMPRESA recibe después una instancia propia del ERP, con base de datos y archivos independientes. La landing no inicia sesión en esa instancia, no envía datos comerciales al ERP y no aprovisiona instancias automáticamente. MINKA crea la instancia manualmente y configura en ella el plan solicitado.

**CLIENTE/EMPRESA:** quien solicita el servicio. **ADMIN:** primer usuario con rol administrador en el ERP de una empresa. **Operador MINKA:** quien atiende solicitudes, verifica pagos y prepara instancias.

## 2. Flujo MVP acordado

### Solicitar el servicio

1. La landing explica el ERP web y la app móvil y presenta los planes con sus precios, módulos y límites.
2. La persona elige un plan y pulsa el botón de ese plan.
3. El formulario solicita teléfono, nombre de la empresa, nombres, apellidos, correo, subdominio deseado y aceptación de términos y política de privacidad. El plan seleccionado se conserva como dato de la solicitud; no se vuelve a pedir que lo elija.
4. La landing valida y guarda la solicitud en su base central. La confirmación de correo puede mantenerse para verificar que la dirección funciona.
5. MINKA revisa la solicitud y se comunica con el cliente para confirmar el alcance y los siguientes pasos.

### Pago manual y alta del ERP

1. MINKA comunica al cliente cómo pagar el plan acordado. No hay pasarela ni cobro automático.
2. El cliente informa el número de operación, fecha y monto. La captura del comprobante puede ser opcional; si se recibe, se guarda en almacenamiento privado.
3. MINKA verifica el pago manualmente con su entidad financiera y registra el resultado en la solicitud.
4. Tras verificarlo, el operador crea manualmente la instancia, su base de datos, archivos, HTTPS y subdominio; ejecuta la configuración del ERP, aplica módulos y límites del plan y crea al primer ADMIN.
5. MINKA registra la instancia como activa y comunica al ADMIN cómo acceder. El cliente inicia sesión en su propio subdominio y selecciona su empresa en la app móvil.

El registro del plan y el pago sirve para coordinar la operación desde la landing. No configura ni modifica automáticamente el ERP.

### Devoluciones y Libro de Reclamaciones

- La landing debe permitir solicitar una devolución y registrar el pedido en la base central. MINKA revisa el caso y coordina manualmente cualquier devolución; el sitio no transfiere dinero.
- El Libro de Reclamaciones recibe quejas y reclamos y genera una constancia. MINKA los gestiona en el panel.
- La solicitud de eliminación de cuenta ya existe. Mantenerla como tipo de atención en el panel salvo decisión posterior de retirarla.

## 3. Planes comerciales por definir

No publicar como definitivos los planes ni sus precios hasta que MINKA apruebe su alcance. Para cada plan hay que decidir:

- Nombre y precio.
- Periodo de cobro y duración del servicio.
- Módulos habilitados en el ERP y funciones incluidas en la app móvil.
- Límites de clientes, pedidos por mes, usuarios u otros recursos que se decida limitar.
- Qué ocurre al superar límites, y si existe prueba o periodo de evaluación.

Para el MVP basta un catálogo informativo sencillo en la landing (por ejemplo, configuración mantenida por el equipo). No hace falta crear ahora una administración de planes ni conectar el catálogo al ERP. La solicitud debe guardar qué plan eligió el cliente; MINKA lo aplica manualmente al aprovisionar.

## 4. Estado actual del código

### Landing y Panel de MINKA

**Carpeta:** `C:\Users\venta\OneDrive\Escritorio\orbynia-landing`

- Laravel con Blade/Vite; las páginas públicas, formularios y el Panel de MINKA se ejecutan en la misma aplicación y usan la base central de la landing.
- Existen formularios de contacto y evaluación/solicitud, verificación de correo, consulta de disponibilidad del subdominio, términos, privacidad, Libro de Reclamaciones y solicitud de eliminación de cuenta.
- El flujo de solicitud actual todavía se llama evaluación y contempla reserva de subdominio, aprobación, módulos y activación manual. No representa aún el flujo comercial final con planes y pago informado.
- Todavía no existe el reporte de número de operación/comprobante ni un estado de pago verificado en el panel. Tampoco existe formulario de devolución.
- El Panel de MINKA se simplificó a dos secciones: **Gestión comercial** (contactos, demostraciones, evaluaciones y seguimientos en una lista) y **Atención legal** (reclamaciones y eliminación de cuenta en una lista). La pantalla inicial usa tarjetas visuales con iconos y botones explícitos; cada tarjeta abre su sección.
- Vistas nuevas: `resources/views/panel/commercial.blade.php` y `resources/views/panel/legal.blade.php`; inicio simplificado en `resources/views/panel/dashboard.blade.php`. Rutas locales: `/panel-minka/comercial` y `/panel-minka/atencion-legal`.
- Se comprobó sintaxis PHP y registro de rutas. Los recursos se compilaron con npm run build; no se ejecutó una suite de pruebas y la revisión visual integrada no estuvo disponible en esta sesión.
- El correo local usa el transportador `log`; la entrega real requiere configurar SMTP.

#### Favicon e identidad visual

- El panel muestra el logotipo oficial completo. Para los favicons se preparó una variante basada en el isotipo negativo, con fondo violeta de esquinas redondeadas y el símbolo ampliado para tamaños pequeños.
- `resources/views/partials/favicon.blade.php` centraliza los enlaces y está incluido en las nueve vistas que tienen `<head>`.
- Recursos generados en `public/images/orbynia/`: `isotipo-negativo.png` y `isotipo-favicon-16.png`, `isotipo-favicon-32.png`, `isotipo-favicon-48.png`, `isotipo-favicon-64.png`, `isotipo-favicon-128.png` y `isotipo-favicon-180.png`. `public/favicon.ico` contiene tamaños 16, 32, 48 y 64 px. El isotipo positivo original se conserva.
- Se limpió la caché de vistas de Laravel después de actualizar los enlaces.
- **Problema pendiente:** el usuario informa que el favicon aún se ve extraño en la pestaña real y que el símbolo no se distingue bien a tamaño reducido. La variante actual no se considera aprobada; habrá que revisarla de nuevo visualmente antes del despliegue. La herramienta de navegador integrado no inició en esta sesión, así que el estado se registra según la captura y observación del usuario.

### Código base ERP de ORBYNIA

**Carpeta:** `C:\xampp\htdocs\CobelesApp\cobelesapp`

- El usuario decidió desarrollar los cambios en el ERP local actual, no en una copia separada.
- Existen `minka:configure-instance` para registrar subdominio, estado, vencimiento y módulos, y `minka:create-admin` para crear al primer ADMIN y enviar una invitación para establecer contraseña.
- Existe middleware de control de instancia, configuración de tenant y seeder de permisos. El modo de tenant sigue desactivado en el entorno Cobeles local.
- La configuración de una empresa debe ejecutarse manualmente en una instancia/base independiente. No se ha demostrado aún una alta completa de punta a punta en una base nueva.

### App móvil Android

**Carpeta:** `C:\Users\venta\AndroidStudioProjects\Cobeles`

- La app permite elegir empresa y construye la URL de API con su subdominio ORBYNIA.
- El identificador de aplicación aprobado es `com.orbynia.movil`.
- `assembleDebug` compiló el 26/09/2026; falta probar acceso contra una instancia real publicada.

## 5. Pendientes para completar el MVP

1. **Definir y aprobar los planes:** precios, módulos ERP/app, límites, periodo de cobro y prueba si corresponde. Hasta entonces, mantenerlos como referenciales o no publicar cifras y límites.
2. **Alinear el formulario de solicitud:** conservar el plan elegido desde la tarjeta; pedir una sola vez teléfono, empresa, nombres, apellidos, correo y subdominio; registrar aceptación y versión de políticas. Decidir si la verificación de correo continúa en el flujo final.
3. **Cambiar el concepto evaluación por solicitud de servicio:** revisar textos, estados y datos actuales de la evaluación, y dejar claro que la aprobación comercial no crea una cuenta ni una instancia.
4. **Añadir reporte de pago manual:** guardar número de operación, fecha, monto y estado. Definir si se permite adjuntar comprobante; si se adjunta, limitar tipo/tamaño y almacenarlo de forma privada.
5. **Añadir gestión del pago en el panel:** mostrar el plan elegido y los datos reportados, permitir que MINKA marque pago pendiente, reportado, verificado o rechazado y conservar un historial de cambios.
6. **Añadir solicitud de devolución:** formulario público, referencia del pago/operación, motivo y contacto; estados de revisión y resolución manuales en el panel. No implementar reembolsos automáticos.
7. **Ampliar la sección de atención:** incluir devoluciones junto con reclamaciones y eliminación de cuenta. Se puede cambiar el rótulo **Atención legal** por **Atención al cliente** para que describa todos esos casos sin añadir una tercera sección principal.
8. **Actualizar textos legales y privacidad:** describir los datos reales que se recopilan en solicitudes, pagos y comprobantes, su finalidad, acceso, conservación y proceso de devolución. Revisar el contenido antes de producción.
9. **Definir procedimiento manual de alta:** revisar el plan y pago, preparar instancia/base/archivos/subdominio, aplicar módulos y límites, crear ADMIN, enviar invitación y registrar la activación en la landing.
10. **Configurar SMTP y desplegar:** configurar DNS/HTTPS para `orbynia.com`, `admin.orbynia.com` y subdominios de clientes; comprobar los correos en el entorno desplegado.
11. **Probar en una instancia separada:** restaurar una base inicial limpia, completar la creación manual de una empresa, comprobar ERP y app móvil y revisar que los datos no se mezclen con Cobeles.

12. **Revisar el favicon antes del despliegue:** la variante violeta con isotipo negativo y varios tamaños ya está implementada, pero el usuario informa que sigue viéndose mal en su pestaña. Reexaminar el arte a 16 px y validar el resultado en el navegador real; conservar la identidad oficial y dejar el favicon pendiente de aprobación.

## 6. Estado al corte

La landing ya centraliza las solicitudes existentes y el panel ahora presenta dos áreas de trabajo. La arquitectura objetivo queda definida: información y coordinación en la landing; cada ERP y su base de datos se crean por separado y manualmente para cada empresa.

El siguiente bloque funcional del MVP es definir planes y actualizar la solicitud para conservar el plan elegido y recoger el reporte de pago manual. Después faltan las solicitudes de devolución, su gestión desde Atención al cliente y la validación operativa en una instancia independiente. El favicon tiene variantes PNG e ICO desplegables localmente, pero su apariencia en la pestaña sigue pendiente de corrección y aprobación del usuario. **No hay todavía un despliegue público ni una instancia de cliente de producción.**

## 7. Referencias de implementación

- Guía de alta manual: `docs/alta-manual-cliente.md`.
- Migración de solicitud comercial: `database/migrations/2026_09_26_000004_create_commercial_flow.php`.
- Controladores de landing/panel: `app/Http/Controllers/CommercialController.php`, `MinkaPanelController.php` y `SalesTrackingController.php`.
- Rutas del panel: `routes/web.php`.
- Favicon compartido: `resources/views/partials/favicon.blade.php`; archivos gráficos en `public/images/orbynia/isotipo-favicon-*.png` y `public/favicon.ico`.
- Configuración del ERP y comando del ADMIN: `app/Console/Commands/ConfigureTenantAccessCommand.php` y `app/Console/Commands/CreateTenantAdminCommand.php` en el código base ERP.
