# ORBYNIA — resumen integral del avance

**Actualización posterior (27 de septiembre de 2026):** el sistema comprueba subdominios ya asignados y reservados. Una solicitud sin correo verificado solo registra el nombre deseado; al verificar el correo se intenta reservarlo de forma única para la revisión comercial. Si otro lo tomó primero, el solicitante puede elegir otro desde el enlace de confirmación. La aprobación confirma la asignación, el rechazo libera la reserva y la activación sigue dependiendo del aprovisionamiento manual. Las referencias de este resumen a una reserva recién al aprobar describen el flujo anterior.

**Seguimiento comercial y correo (27 de septiembre de 2026):** el Panel de MINKA permite asignar un responsable, etapa y próxima fecha de seguimiento a contactos y solicitudes; registrar actividades y propuestas con historial. La etapa comercial es independiente del estado de verificación, aprobación y activación. La página de solicitud muestra si el enlace de verificación se envió, falló o quedó solo en el log local; ofrece reenvío con límite de frecuencia y una página para recuperar el enlace mediante el correo registrado. El envío de propuestas sigue siendo una acción manual del operador.

**Fecha de corte:** 26 de septiembre de 2026  
**Ámbito:** landing pública, Panel de MINKA, ERP web local, app Android y plan de publicación.  
**Criterio:** se distingue lo implementado en el equipo local de lo que todavía requiere una instancia de prueba o infraestructura pública. Este documento no contiene contraseñas ni claves.

## 1. Explicación básica

ORBYNIA es el nombre comercial del software. MINKA 360 S.A.C. es la titular y operadora del producto. El producto que recibe cada **CLIENTE/EMPRESA** está compuesto por un ERP web y una app móvil. Cada empresa tendrá su propio subdominio, bases de datos y archivos. El **ADMIN** es el primer usuario administrador de ese ERP; no es un operador de MINKA.

La landing `orbynia.com` es un proyecto Laravel independiente: presenta el producto, recibe solicitudes y contiene las páginas públicas. El **Panel de MINKA** pertenece a esa landing y sirve para que su equipo revise solicitudes. En local está en `/panel-minka`; para producción se prevé `admin.orbynia.com`. La landing no comparte la base de datos ni autentica las contraseñas de los ERP de clientes.

```text
Empresa interesada → landing → solicitud → Panel de MINKA
                                      ↓ aprobación comercial
                           operador prepara la instancia manualmente
                                      ↓
                [empresa].orbynia.com → login del ERP propio
                                      ↑
                        app móvil selecciona esa empresa
```

La aprobación comercial **no crea automáticamente** una instancia. El operador prepara la infraestructura y las bases aisladas, configura módulos y vencimiento, crea al primer ADMIN y finalmente marca la solicitud como activa.

## 2. Identidad y decisiones del plan

- **Marca y dominio:** ORBYNIA y `orbynia.com`. Nombre visible de la app: **Orbynia**. Identificador Android: `com.orbynia.movil`.
- **Titular:** MINKA 360 S.A.C., RUC **20607217191**. La ficha RUC y el plan legal se usaron para los datos corporativos de la web.
- **Facturación:** MINKA factura sus servicios SaaS, licencia y soporte. Cada CLIENTE/EMPRESA sigue siendo el emisor de sus ventas ante SUNAT; NubeFact presta la tecnología de emisión contratada para esa empresa. Estas funciones no se confunden con la facturación de MINKA.
- **Identidad visual:** la landing usa variantes de logotipo e isotipo de ORBYNIA, colores de marca y fuentes Montserrat/Poppins servidas localmente.
- **D-U-N-S®:** MINKA ya lo solicitó y eligió consultar la opción acelerada de tres días hábiles. A la fecha documentada aún esperaba la propuesta, costo e instrucciones de D&B; no se ha registrado un número recibido.
- **Plan original:** `C:\Users\venta\Downloads\plan_erp_legal_compliance.md`. Algunas descripciones anteriores del acceso deben sincronizarse con la nueva ruta `/acceder` acordada después. Esa ruta localiza la instancia; el login real del ERP sigue en el subdominio del cliente.

## 3. Landing pública: avance funcional

**Proyecto:** `C:\Users\venta\OneDrive\Escritorio\orbynia-landing`

### Presentación y páginas públicas

La portada presenta la plataforma, cómo funciona, movilidad, planes referenciales, empresa y contacto. Se trabajó el fondo sutil de estrellas en el hero, un encabezado que adapta logo y fondo durante el desplazamiento, la franja animada de áreas de operación y la simetría de las cuatro tarjetas informativas. Las cifras del panel ilustrado son conceptuales.

Están creadas las páginas de **términos y condiciones**, **política de privacidad**, **Libro de Reclamaciones** y **solicitud de eliminación de cuenta**. El footer identifica a MINKA, muestra `info@minka360.com` y ordena los enlaces legales. Los textos públicos deberán revisarse contra el servicio y la versión de la app que efectivamente se publiquen.

El Libro de Reclamaciones guarda una hoja con referencia correlativa y ofrece una constancia consultable/imprimible. El formulario se simplificó para describir el servicio, monto si aplica, reclamo o queja, motivo y solución solicitada. Conserva los datos de representante cuando el reclamo se presenta en nombre de un menor; ese supuesto pertenece al Libro y no habilita cuentas ERP para menores. La solicitud de eliminación genera su propio número de seguimiento. Los correos locales se registran en el log, porque todavía no hay SMTP de producción.

### Solicitud comercial y Panel de MINKA

Los botones comerciales llevan a formularios de contacto o evaluación. La evaluación recoge empresa, contacto, correo, teléfono, plan de interés y subdominio solicitado; no pide contraseña. Envía un enlace firmado para verificar el correo. El Panel de MINKA muestra contactos y solicitudes, registra eventos y permite aprobar, rechazar y marcar activa una solicitud. Al aprobar se reservan subdominio, módulos y fin de evaluación; el aprovisionamiento continúa siendo manual.

Al activar, el operador registra el correo del **primer ADMIN del ERP**. Ese dato permite localizar la empresa desde la landing sin replicar cuentas o contraseñas del ERP. Hay **un operador de MINKA creado en la base local**; no se documenta aquí ninguna credencial.

### Acceso central

La ruta local `http://127.0.0.1:8017/acceder` reúne el enlace para solicitar el servicio y el acceso a quienes ya usan ORBYNIA. Recibe un código de empresa, el correo del primer ADMIN o el identificador de un operador de MINKA:

- Una empresa **activa** abre `https://[empresa].orbynia.com/` en otra pestaña. El usuario vuelve a iniciar sesión allí con las credenciales de su ERP.
- Un operador de MINKA es llevado al login del Panel de MINKA; su contraseña solo se valida contra la base de la landing.
- Solo en desarrollo local, `cobeles` abre `http://127.0.0.1:8000/`, sin registrar a Cobeles como una solicitud comercial ficticia.

La landing **no recibe ni verifica contraseñas de usuarios de las instancias ERP**. Si un mismo correo de ADMIN pertenece a varias empresas activas, se pide usar el código de empresa.

## 4. Landing: detalle técnico

- **Pila:** Laravel 12, Blade, Vite, CSS y JavaScript. Se despliega como aplicación separada del ERP.
- **Base central local:** SQLite propia de la landing. Las migraciones públicas crean `public_complaints` y `account_deletion_requests`; las comerciales crean `commercial_leads`, `company_applications` y `company_application_events`, añaden `users.is_minka_operator` y `company_applications.admin_email`.
- **Código principal:** `PublicSiteController.php` para páginas y formularios legales; `CommercialController.php` para contacto/evaluación/verificación; `MinkaPanelController.php` para el panel; `AccessPortalController.php` para orientar el acceso.
- **Operación:** `orbynia:create-operator` crea un operador con contraseña introducida de forma interactiva. `docs/alta-manual-cliente.md` describe el alta manual de clientes.
- **Entorno actual:** `APP_ENV=local`, `MAIL_MAILER=log`; las migraciones locales están aplicadas. Al cierre de este informe hay **0 solicitudes comerciales** y **0 empresas activas** en la base central local.
- **Rutas de producción previstas:** web pública en `orbynia.com` y panel en `admin.orbynia.com`. No se ha realizado desde este proyecto el despliegue público ni la configuración de DNS/HTTPS/SMTP.

## 5. ERP web: avance funcional y técnico

**Código principal actual:** `C:\xampp\htdocs\CobelesApp\cobelesapp`. Los cambios de ORBYNIA se trasladaron a este ERP local por decisión posterior del usuario. Los 18 archivos trasladados coincidieron por SHA-256 con la antigua copia separada; ese worktree quedó archivado.

La migración `2026_09_26_000001_prepare_orbynia_tenant_access.php` ya corrió en la base local `cobeles_erp`. Añade el indicador `password_setup_pending` y crea `tenant_access` para guardar el subdominio, estado, vencimiento de evaluación y módulos de una futura instancia. En Cobeles local **`ORBYNIA_TENANT_MODE=false` y `tenant_access` no tiene registros**; por ello el control de instancia no interrumpe su uso actual.

Se incorporaron estos componentes:

- `minka:configure-instance`: registra manualmente la configuración de una instancia ya preparada; no crea servidor ni bases de datos.
- `minka:create-admin`: crea el primer ADMIN dentro de esa instancia y envía una invitación de un solo uso para que establezca su contraseña. No se envía una contraseña por correo. Puede reenviar una invitación pendiente.
- `EnsureTenantAccess`: al activar el modo por instancia, controla estado, vencimiento y algunos módulos en web/API. La cobertura se amplió para pedidos, inventario, GPS, rutas, vehículos, transportistas y otras rutas evidentes; **no está cerrada aún para todas las áreas del ERP**.
- `/api/instance-info`: permite a la app confirmar el subdominio de una instancia antes del login. Se eliminó el registro público de usuarios ERP y se adaptaron autenticación, restablecimiento e invitación inicial.
- `database/schema/mysql-schema.sql`: volcado de estructura sin datos comerciales, con **103 tablas** y registro de **10 migraciones anteriores**. El intento de generación mostró un aviso de compatibilidad de `mysqldump`; falta comprobar restauración en una base vacía.
- `OrbyniaPermissionSeeder` y `database/seeders/data/orbynia_permissions.json`: catálogo de **167 permisos** reutilizables para nuevas instancias, sin copiar usuarios ni operaciones de Cobeles. El seeder no se ejecutó para inventar una nueva empresa en la base local.

Una nueva empresa requerirá bases MySQL y PostgreSQL propias, archivos propios, URL/HTTPS propios, migraciones, catálogo de permisos, `ORBYNIA_TENANT_MODE=true`, configuración de módulos y creación de su ADMIN. El Panel de MINKA solo registra que el operador terminó esas tareas.

## 6. App móvil Android

**Proyecto:** `C:\Users\venta\AndroidStudioProjects\Cobeles`.

El nombre visible es **Orbynia** y `applicationId` es **`com.orbynia.movil`**. El `namespace` y los paquetes fuente aún conservan `com.example.cobeles`; eso no cambia el identificador con el que se instala la app, pero queda como refactor técnico posible antes de publicar.

`TenantSettings.kt` guarda el código de empresa. La pantalla de login exige seleccionar una empresa, consulta `/api/instance-info` y, si coincide, `RetrofitClient.kt` construye `https://[empresa].orbynia.com/api/`. Al cambiar de empresa se limpia la sesión anterior para no reutilizar un token de otra instancia. La pantalla de inicio solo omite el login cuando existen token y empresa seleccionada.

En la compilación **debug**, el código `local` usa el ERP de Cobeles en `http://172.20.10.3:8000/api/` (o `ERP_DEBUG_URL` si se configura en `local.properties`). Esta excepción no está disponible en release. `assembleDebug` compiló correctamente con el JDK de Android Studio; todavía no se ha comprobado el flujo completo en un dispositivo y una instancia de prueba publicada.

## 7. Comprobaciones realizadas y límites

Se revisaron estados Git de los tres proyectos; los cambios descritos siguen **sin commit**. Se aplicaron las migraciones locales de la landing y del ERP, se comprobaron sintaxis PHP y vistas Blade, se compilaron los recursos Vite y la app Android debug. Estas comprobaciones muestran que el código local se integra y compila; **no equivalen a una prueba integral del alta y uso de un cliente real**.

Tampoco se ha publicado `orbynia.com`, creado `admin.orbynia.com`, desplegado `cobeles.orbynia.com` ni configurado correo transaccional real desde estos proyectos. El dominio oficial está definido, pero el acceso local `cobeles` es una excepción de desarrollo.

## 8. Trabajo pendiente ordenado

1. **Instancia de prueba aislada:** comprobar restauración del esquema MySQL, definir y preparar la estructura PostgreSQL GPS, ejecutar migraciones y seeder, configurar URL/HTTPS y confirmar que no se copian datos de Cobeles.
2. **Recorrido completo:** presentar una solicitud de prueba, verificar correo con SMTP real, aprobarla, crear manualmente la instancia, ejecutar `minka:configure-instance` y `minka:create-admin`, establecer contraseña, activarla y acceder por web/app. Aún no se hizo este recorrido.
3. **Alcance de módulos:** acordar qué incluyen los planes y cómo se licencian compras, tesorería, finanzas y demás áreas; completar la cobertura de rutas y acciones compartidas del middleware.
4. **Infraestructura pública:** DNS, HTTPS, VPS/hosting, aislamiento por cliente, copias de seguridad, monitoreo y correo transaccional. El plan contempla una VPS Hostinger KVM 4, pero su despliegue queda pendiente.
5. **Operación y contenidos:** definir precios y condiciones finales, revisar legalmente textos y proceso de reclamaciones/eliminación, responsables de atención, y armonizar el plan original con el flujo de acceso vigente. El README local ya se actualizó.
6. **Google Play:** continuar D-U-N-S® y verificación de la organización; revisar política de privacidad, ficha y configuración de publicación contra la versión final de la app. El APK debug compilado no es una publicación en Play.

## 9. Estado actual

**En local hay una primera integración funcional de la landing, el Panel de MINKA, el ERP base y la selección de empresa en Android.** La landing puede recibir solicitudes y el operador interno puede revisarlas. El acceso central puede abrir el ERP local de Cobeles o el panel, y está preparado para redirigir a futuras empresas activas. El ERP local contiene los comandos y controles para preparar instancias, pero su modo de instancia permanece desactivado en Cobeles. La app Android compila y puede seleccionar el servidor local o un futuro subdominio ORBYNIA.

**No hay todavía solicitudes comerciales ni empresas activas registradas en la landing local, ni una instancia de prueba aislada, ni despliegue público.** La integración completa de extremo a extremo queda para esa instancia de prueba y la infraestructura correspondiente.
