# Arquitectura de Despliegue - Vulnerable + SecureShop v2

Este laboratorio levanta dos escenarios comparables en Docker: una tienda vulnerable por HTTP y una version sanitizada por HTTPS/TLS. La idea es acercarse a un entorno real sin salir de una maquina local.

## Arquitectura

```text
Host local
  |
  | http://localhost:8000
  v
VulnShop vulnerable
  PHP 8.2 + Apache
  DB: vulnshop_db

Host local
  |
  | https://localhost:8443
  v
SecureShop v2
  PHP 8.2 + Apache + TLS
  DB: secureshop_db
```

## Servicios

| Servicio | Carpeta | Puerto | Base de datos | Proposito |
| --- | --- | --- | --- | --- |
| `app` | `web vulnerable` | `8000:80` | `vulnshop_db` | Version vulnerable para practica ofensiva |
| `db` | PostgreSQL 16 | `5433:5432` | `vulnshop_db` | BD de la version vulnerable |
| `secure_app` | `web seguro` | `8443:443`, `8080:80` | `secureshop_db` | Version 2 sanitizada con HTTPS |
| `secure_db` | PostgreSQL 16 | `5434:5432` | `secureshop_db` | BD de la version segura |

## Levantar Todo el Laboratorio

```bash
docker compose up --build
```

URLs:

```text
Vulnerable: http://localhost:8000
Segura v2:  https://localhost:8443
```

## Levantar Solo la Version Segura

```bash
docker compose up --build secure_app secure_db
```

## TLS Local

SecureShop v2 usa un certificado autofirmado generado con OpenSSL dentro del Dockerfile. Esto permite practicar HTTPS sin comprar dominio ni certificado publico.

El navegador mostrara advertencia por certificado no confiable. En laboratorio local, se acepta la excepcion manualmente.

## Escenario Realista del Laboratorio

- La version vulnerable representa una aplicacion legacy expuesta por HTTP.
- La version segura representa una refactorizacion defensiva v2.
- Cada version tiene su propia base de datos para no mezclar datos.
- SecureShop v2 fuerza cookies seguras y HSTS cuando corre por HTTPS.
- Los uploads de la version segura se guardan en volumen separado y con PHP deshabilitado en `storage`.

## Verificacion

```bash
docker compose config
docker compose ps
docker compose logs secure_app
docker compose logs secure_db
```

Pruebas manuales:

- Abrir `https://localhost:8443`.
- Aceptar certificado autofirmado.
- Login admin: `admin@secureshop.local` / `AdminPass123!`.
- Login usuario: `user@secureshop.local` / `UserPass123!`.
- Revisar headers HTTPS desde DevTools.
- Confirmar que `http://localhost:8080` redirige a HTTPS.

## Limpieza

Detener contenedores:

```bash
docker compose down
```

Borrar datos persistidos:

```bash
docker compose down -v
```

Usa `down -v` solo cuando quieras reiniciar completamente las bases de datos, uploads y artefactos generados.
