# Guion exposición — Proyecto Final

Voy a seguir este orden: base de datos → código → demo en el navegador.

---

## 1. Base de datos

**Abro:** `config/respaldo.sql` y phpMyAdmin (base `proyectofinal`).

### Lo que voy a contar

La base de datos guarda todo en MySQL. El archivo `respaldo.sql` es el respaldo completo: si lo importas, tienes la estructura y datos de prueba.

**La parte que más me gusta del diseño** es que no es “un juego = una fila y ya”. Lo separé en tres niveles:

| Qué es | Tabla | Ejemplo |
|--------|--------|---------|
| El videojuego | `juegos` | Elden Ring |
| Versión en una consola | `ediciones` | Elden Ring en PS5 (portada, región…) |
| Lo que tiene cada usuario | `coleccion_usuario` | Mi copia: si lo estoy jugando, estado del disco… |

Así el mismo título puede estar en PS4, PS5, Switch… y cada persona guarda solo lo que tiene en su estantería.

**Otra cosa importante del proyecto:** los usuarios no meten juegos directamente en el catálogo. Mandan una **propuesta** (`juegos_pendientes` + `ediciones_pendientes`) y yo como admin la reviso en la web. Si la apruebo, pasa a `juegos` y `ediciones`; si no, queda rechazada. Eso evita que el catálogo se llene de datos mal puestos.

También tengo:
- `prestamos` — registro de a quién le presté un juego de mi colección
- `valoraciones` — nota del 1 al 10 por juego
- `usuarios` con roles: usuario, admin y super_admin
- Un **trigger** en el SQL (`limpiar_juegos_huerfanos`): si borras la última edición de un juego, el título se elimina solo del catálogo

### En phpMyAdmin enseño rápido

1. `juegos` — títulos del catálogo  
2. `ediciones` — mismo juego en distintas consolas  
3. `coleccion_usuario` — biblioteca de cada usuario  
4. `juegos_pendientes` — propuestas esperando revisión  

---

## 2. Código

### Cómo lo organicé

Usé PHP con una estructura parecida a MVC:

| Carpeta | Para qué la uso |
|---------|------------------|
| `vistas/` | Las pantallas (HTML) |
| `controllers/` | La lógica cuando se envía un formulario |
| `models/` | Las consultas a la base de datos |

Flujo: la vista llama al controlador → el controlador usa el modelo → el modelo habla con MySQL.

### Archivos que voy a abrir

| Archivo | Qué hace |
|---------|----------|
| `config/config.php` | Conexión a MySQL con PDO |
| `includes/auth.php` | Sesión, comprobar si eres admin, traducciones ES/EN |
| `controllers/AuthController.php` | Login y registro |
| `controllers/JuegoController.php` | Envío de propuestas de juegos |
| `controllers/AdminController.php` | Aprobar o rechazar propuestas |
| `vistas/admin/validar_juegos.php` | Pantalla del panel admin para validar |
| `vistas/fronted/mi_coleccion.php` | Biblioteca del usuario |

En seguridad implementé contraseñas con hash (no van en claro), consultas preparadas con PDO y en `auth.php` bloqueo el acceso a las páginas privadas si no hay sesión.

---

## 3. Demo en el navegador

1. Entro como **usuario** → busco un juego → lo añado a **Mi colección**  
2. Entro como **admin** → `validar_juegos.php` → apruebo una propuesta  
3. Vuelvo como usuario → el juego ya aparece al buscar  

Idiomas: en varias páginas se puede cambiar ES/EN (`lang/es.php` y `lang/en.php`).

---

## Cierre

En resumen: la base de datos separa el catálogo común de la colección personal de cada uno, y en PHP repartí pantallas, lógica y acceso a datos. Lo más característico del proyecto es el sistema de propuestas con validación por el administrador.

---

## Por si preguntan

| Pregunta | Respuesta |
|----------|-----------|
| ¿Qué es el proyecto? | Una web para gestionar tu colección de videojuegos |
| ¿Dónde está la BD? | `config/respaldo.sql`, base `proyectofinal` en MySQL |
| ¿Tabla clave? | `ediciones` — une cada juego con su plataforma |
| ¿Qué lo hace distinto? | Propuestas de usuarios + validación del admin |
| ¿Dónde está en el código? | `AdminController.php` y `validar_juegos.php` |
