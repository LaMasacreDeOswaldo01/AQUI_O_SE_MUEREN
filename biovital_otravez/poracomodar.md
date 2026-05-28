# Por acomodar — BioVital

Lista de pendientes detectados en la auditoría del repo. Ordenados por severidad. El punto #21 (`config/routes.php` con 260+ rutas hardcodeadas y conflictos de merge) **ya se resolvió** y por eso no aparece aquí.

---

## Críticos — atender ya

### 1. Conflictos de merge sin resolver en ~49 archivos PHP
Quedan marcadores `<<<<<<< HEAD` / `=======` / `>>>>>>>` en casi todos los controladores, modelos y en `index.php`. El intérprete PHP fallará al cargarlos.
Archivos afectados (lista parcial — correr `grep -rl '<<<<<<< HEAD' --include='*.php' .` para el listado completo):
- `index.php`
- `config/app.php`
- `controlador/*.php` (todos)
- `modelo/*.php` (la mayoría)

**Acción:** revisar cada archivo, decidir qué versión queda y borrar los marcadores. Probablemente vino del merge del PR #6.

### 2. Sin `.gitignore`
Nada está protegido contra commits accidentales (`.env`, logs, sesiones, backups).
**Acción:** crear `.gitignore` que excluya al menos: `.env`, `*.sql`, `*.zip`, `logs/`, `vendor/`, `node_modules/`, `.DS_Store`, `.idea/`, `.vscode/`.

### 3. Dump completo de la BD versionado
`biovital (3).sql` (83 KB, schema + datos reales) está commiteado.
**Acción:** `git rm --cached "biovital (3).sql"`, moverlo fuera del repo o a `scripts/schema/` con datos anonimizados.

### 4. Código fuente comprimido dentro del repo
`app.zip` (3.3 KB) commiteado sin razón clara.
**Acción:** `git rm app.zip`.

### 5. `get_hash.php` accesible públicamente
Endpoint que imprime un `password_hash('123456', …)`. Sin auth, sin restricción.
**Acción:** borrarlo, o moverlo a `scripts/` y bloquear con `.htaccess`.

### 6. Credenciales hardcodeadas en `modelo/Conexion.php`
Líneas 3-8: `usuario="root"`, `contrasena=""`. El proyecto tiene `.env.example` pero no se carga.
**Acción:** crear un loader de `.env` (o usar `vlucas/phpdotenv` vía Composer) y leer host/user/pass/db desde variables de entorno.

### 7. `.env.example` con valores reales de desarrollo
Contiene `DB_USER=root`, `DB_PASS=`, `APP_ENV=development`.
**Acción:** dejar solo placeholders (`DB_USER=tu_usuario`) y verificar que el `.env` real esté en `.gitignore`.

---

## Altos — seguridad

### 8. CSRF ausente en cambio de contraseña
`controlador/PerfilController.php:109-122` (`cambiarPassword`) procesa el cambio sin verificar token CSRF, aunque el resto de endpoints sí lo hacen.
**Acción:** agregar `Security::verificarCsrf()` (o equivalente) al inicio del método.

### 9. Posible LFI vía `require_once` dinámico con `$_SESSION['rol']`
`controlador/PerfilController.php:56-107` arma el nombre del archivo del controlador con `ucfirst($this->rol) . 'Controller'` y lo incluye. Si `$_SESSION['rol']` se contamina, puede cargar archivos arbitrarios.
**Acción:** validar contra una whitelist explícita: `['paciente','medico','asistente','administrador']`.

### 10. Endpoints públicos sin rate-limit
`/api/registro/*`, `/api/ubicacion/*` y el `login` no limitan intentos. Habilitan brute-force, spam y enumeración.
**Acción:** middleware de rate-limit (por IP + endpoint), o al menos contador en sesión/Redis para login y registro.

### 11. Enumeración de usuarios en login
`modelo/LoginPaciente.php:14-34`: si el usuario no existe se devuelve rápido; si existe, se ejecuta `password_verify`. Diferencia de tiempo + diferencia de mensaje permite enumerar correos válidos.
**Acción:** ejecutar siempre un `password_verify` dummy y devolver el mismo mensaje genérico ("credenciales inválidas") en ambos casos.

### 12. Longitud mínima de contraseña: 6 caracteres
`config/app.php:35` y `controlador/RegistroController.php:551`. Insuficiente para una app médica.
**Acción:** subir a 12 mínimo, y validar también que no sean solo dígitos o solo letras.

### 13. Validación de subida de archivo confía en la extensión del nombre
`controlador/PacienteController.php:301-302`: `pathinfo(... PATHINFO_EXTENSION)` sin whitelist.
**Acción:** whitelist explícita (`['jpg','jpeg','png','webp']`), validar tamaño con `$_FILES['photo']['size']` antes de mover, y renombrar con `uniqid()` + extensión derivada del MIME real (`finfo`).

### 14. `extract($data)` en `renderView`
`index.php:187`. Cualquier clave en `$data` puede sobrescribir variables del scope de la vista.
**Acción:** pasar el array directamente (`$data['x']`) o usar `extract($data, EXTR_SKIP)` como mínimo.

### 15. Modelo emite `echo` y el controlador lo captura con `ob_start`
`modelo/LoginPaciente.php:49-52` hace `echo 'update'` / `echo 'noupdate'`; `PerfilController::cambiarPassword` envuelve la llamada con `ob_start`/`ob_get_clean` y devuelve el contenido. Cualquier `echo` accidental (warning, log) se filtra al cliente.
**Acción:** que los modelos devuelvan un valor (`return true/false`) y el controlador construya el JSON.

### 16. Cookies de sesión Secure/HttpOnly/SameSite atadas a `APP_ENV`
`index.php:35-40` solo activa flags seguros si `APP_ENV === 'production'`. Como no se carga `.env`, en prod no estarán seguras.
**Acción:** forzar `HttpOnly` y `SameSite=Lax` siempre; `Secure` activo si la request llegó por HTTPS (`$_SERVER['HTTPS']`).

### 17. `error_log` filtra parte del token CSRF de sesión
`modelo/Security.php:50-52`: `error_log("[CSRF] Session token: " . substr(... , 0, 20) . "...")`. 20 chars de un token de sesión es suficiente para correlacionarlo.
**Acción:** loguear solo "token mismatch" sin el valor.

---

## Medios — calidad y estructura

### 18. Sin foreign keys ni índices en el dump SQL
Columnas `cedula_paciente`, `correo_medico`, `cedula_medico` se usan en `WHERE` sin índice, y no hay constraints visibles entre tablas (citas ↔ paciente, recetas ↔ medico, etc.).
**Acción:** añadir índices en columnas de búsqueda y FKs con `ON DELETE` apropiado.

### 19. `isAjax()` duplicado en casi todos los controladores
Cada controller redefine el mismo método. Violación de DRY.
**Acción:** mover a un `BaseController` y heredar.

### 20. Dos formatos de respuesta JSON conviviendo
A veces `ApiResponse::success(...)`, a veces `jsonResponse(['resultado' => ...])`. El cliente JS tiene que conocer ambos.
**Acción:** estandarizar en uno (`ApiResponse` parece más completo) y migrar las llamadas restantes.

### 22. `MAX_FILE_SIZE` definido pero no se valida
La constante existe pero `$_FILES['photo']['size']` no se compara antes de `move_uploaded_file`.
**Acción:** rechazar early con error 413 si excede.

### 23. `mkdir()` de `logs/` sin manejo de error
`index.php:19-22` crea el directorio pero no maneja el caso de fallo (permisos, disco).
**Acción:** verificar `is_dir()` después y abortar con mensaje claro si no se pudo.

---

## Bajos

### 24. `var $objetos;` estilo PHP 4
`modelo/LoginPaciente.php:5` y otros modelos usan `var` en vez de `public`.
**Acción:** reemplazar por `public` o por la visibilidad correcta.

### 25. `package.json` y `scripts/` en un proyecto PHP puro
Revisar si aportan algo (build de assets, tests) o son ruido para limpiar.

---

## Notas

- Tras resolver los conflictos del punto #1, conviene volver a correr toda la auditoría — varios "altos" pueden tener parches a medio aplicar en algún branch que aún no se resuelve.
- Antes de tocar producción: hacer backup de la BD real (que **no** debe coincidir con `biovital (3).sql`).
