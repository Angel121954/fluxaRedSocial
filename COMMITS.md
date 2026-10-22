# Convención de Commits

Este proyecto utiliza una convención basada en **Conventional Commits** para mantener un historial organizado y facilitar la lectura de cambios.

## Formato

```bash
tipo(alcance): descripción
```

Ejemplo:

```bash
feat(profile): Agregar edición de experiencia laboral

- Formulario para crear, editar y eliminar experiencias.
- Validación con FormRequest y autorización por Policy.
- Actualizar el CV visual después de guardar los cambios.
```

* **tipo:** Acción del cambio realizado (en inglés).
* **alcance:** Módulo o área afectada (en inglés).
* **descripción:** Resumen del cambio realizado (en español).

---

## Tipos de commits

### feat

Nueva funcionalidad.

```bash
feat(profile): Agregar edición de experiencia laboral
```

### fix

Corrección de errores.

```bash
fix(comments): Corregir el conteo de likes al eliminar un comentario
```

### refactor

Cambios internos en el código sin modificar funcionalidades.

```bash
refactor(projects): Extraer la subida de medios a ProjectService
```

### ui

Cambios visuales o de interfaz.

```bash
ui(explore): Ajustar los filtros de tecnologías
```

### style

Cambios de formato que no modifican el comportamiento del código (espacios, comillas, orden de imports, indentación).

```bash
style(app): Aplicar Pint a los controladores
```

### docs

Cambios en documentación.

```bash
docs(readme): Actualizar instrucciones de instalación
```

### test

Agregar o modificar pruebas.

```bash
test(projects): Agregar pruebas para ProjectService
```

### chore

Mantenimiento general, dependencias o configuración.

```bash
chore(dependencies): Actualizar paquetes del proyecto
```

### perf

Mejoras de rendimiento.

```bash
perf(feed): Eager loading de relaciones en el listado de proyectos
```

### build

Cambios relacionados con compilación o herramientas.

```bash
build(docker): Actualizar configuración del contenedor de Reverb
```

### ci

Cambios en integración continua.

```bash
ci(github): Configurar ejecución automática de pruebas
```

### revert

Revierte un commit anterior. El mensaje indica qué cambio se deshace.

```bash
revert(diary): Revertir respuestas anónimas
```

---

## Cambios que rompen compatibilidad

Si un cambio deja de ser compatible con versiones anteriores, se agrega `!` después del alcance:

```bash
feat(api)!: Cambiar el formato de respuesta de /api/search
```

El detalle debe explicar qué se rompe y cómo migrar, y el commit cierra con un pie `BREAKING CHANGE:`:

```bash
feat(api)!: Cambiar el formato de respuesta de /api/search

- La respuesta ahora usa `data` en lugar de `results`.
- Los clientes deben actualizar el campo que leen.

Por qué:
- Unificar el contrato de todas las respuestas de la API.

BREAKING CHANGE: `results` se reemplaza por `data`; los clientes que lean `results` dejarán de funcionar.
```

---

## Alcances recomendados

Ejemplos:

```
auth          Autenticación (Fortify, Socialite, 2FA)
users         Usuarios
profile       Perfil, experiencia y educación
cv            Generación de CV (PDF, ATS, JSON)
projects      Proyectos, media y comentarios
feed          Feed de proyectos
explore       Exploración y filtros
messages      Mensajería
notifications Notificaciones
follows       Seguidores
onboarding    Onboarding
suggestions   Sugerencias de usuarios
admin         Panel de administración
api           Servicios API internos
database      Base de datos y migraciones
services      Servicios
controllers   Controladores
ui            Interfaz
estilos       Estilos
config        Configuración
tests         Pruebas
```

---

## Reglas

* Usar verbos en infinitivo en la descripción.
* No utilizar punto al final.
* Mantener los commits pequeños y enfocados en un solo cambio.
* Evitar mensajes genéricos.

Correcto:

```bash
feat(projects): Agregar filtro por tecnología
```

Incorrecto:

```bash
Cambios varios
```

Incorrecto:

```bash
fix: arreglar errores
```

---

## Mensajes detallados

Si el commit requiere detalle, se usa la misma convención y cada punto se agrega como un guión `-` con un solo salto de línea entre ellos (sin líneas en blanco entre los guiones):

```bash
feat(cv): Exportar el CV en formato JSON
- Endpoint que descarga un archivo .json con datos estructurados del perfil.
- Solo el dueño del perfil puede exportar el JSON; a los demás se les entrega el PDF.
- La ruta vive dentro del grupo con middleware verified + restrict.guest.
```

Incorrecto (con líneas en blanco entre guiones):

```bash
feat(cv): Exportar el CV en formato JSON

- Endpoint que descarga un archivo .json con datos estructurados del perfil.

- Solo el dueño del perfil puede exportar el JSON; a los demás se les entrega el PDF.
```

Salto de línea en blanco solo se permite después del título y, si aplica, entre secciones (por ejemplo una sección de motivos o "Por qué:").

---

## Sección de motivo

El motivo del cambio va en una sección propia que arranca con `Por qué:`. Se escribe **después** de los guiones de detalle, separado por una línea en blanco, y **nunca** como un guión más de la lista.

Correcto:

```bash
feat(follows): Actualizar el contador de seguidores en tiempo real

- El contador se actualiza con el evento FollowToggled a través de Reverb.
- El botón alterna entre Seguir y Siguiendo sin recargar la página.

Por qué:
- El contador solo se actualizaba al recargar y mostraba datos desactualizados.
```

Incorrecto (`Por qué:` como guión de detalle):

```bash
feat(follows): Actualizar el contador de seguidores en tiempo real

- El contador se actualiza con el evento FollowToggled a través de Reverb.
- Por qué: el contador solo se actualizaba al recargar la página.
```

Cuando el motivo ocupe más de un punto, cada uno lleva su guión debajo del `Por qué:`.

---

## Mensajes desde archivo

Para reducir errores de escapado en el shell (espacios, comillas, salto de líneas), los mensajes de commit se crean como texto en un archivo y se aplican con:

```bash
git commit -F ruta/al/archivo-mensaje.txt
```

El archivo (por ejemplo en `/tmp/commit-mensaje.txt`):

- La primera línea es el título: `tipo(alcance): descripción`.
- Después del título puede ir una línea en blanco.
- Cada punto detallado va en su propia línea precedido de `- `, separado por un solo salto de línea y sin líneas en blanco entre los guiones.
- La sección `Por qué:` se escribe en el archivo igual que en el mensaje, después de los guiones de detalle y precedida de una línea en blanco.
- Antes de commitear se verifica el mensaje con `git log` o con un diff del archivo si hace falta.

Al enmendar (`git commit --amend`) hay que fijarse en a qué commit se aplica. `--amend` siempre reescribe el último, así que si el mensaje que se quiere corregir es de un commit anterior primero hay que llegar a él:

```bash
git rebase -i HEAD~2
```

En el `rebase` interactivo se marca el commit a corregir con `reword` y `HEAD~n` con la cantidad de commits que hay desde el que se quiere reescribir hasta el último.
