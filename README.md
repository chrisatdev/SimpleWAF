```
# Simple WAF - Versión 1.0

Este es un Web Application Firewall (WAF) simple escrito en PHP, diseñado para bloquear solicitudes maliciosas basadas en geolocalización, patrones comunes de ataques como XSS y SQL injection, y limitar la tasa de solicitudes por IP.

## Requisitos

- PHP 7.4 o superior
- Composer (para instalar las dependencias)
- Base de datos GeoLite2 para la geolocalización (disponible en: https://dev.maxmind.com/geoip/geolite2-free-geolocation-data)

## Instalación

1. **Clona el repositorio o descarga los archivos** en tu servidor web.

2. **Instala las dependencias** usando Composer. Asegúrate de que Composer está instalado y ejecuta el siguiente comando en la carpeta raíz del proyecto:
   ```bash
   composer install
   ```

3. **Descarga la base de datos GeoLite2**:
   - Crea una carpeta `db/` en la raíz del proyecto.
   - Descarga el archivo `GeoLite2-Country.mmdb` desde el sitio web de MaxMind y colócalo en la carpeta `db/`.

4. **Asegúrate de que el archivo `waf_log.txt` tiene permisos de escritura** para que el sistema pueda registrar las solicitudes bloqueadas:
   ```bash
   chmod 666 waf_log.txt
   ```

## Funcionalidades

1. **Protección por geolocalización:**
   El WAF bloquea accesos provenientes de países específicos. Actualmente, los siguientes países están bloqueados:
   - Rusia (RU)
   - Arabia Saudita (SA)
   - Irán (IR)
   - China (CN)
   - India (IN)

   Se pueden agregar o eliminar países modificando la lista `blockedCountries` en el código.

2. **Patrones peligrosos:**
   El WAF inspecciona las solicitudes (GET, POST, COOKIES, REQUEST) en busca de patrones comunes de ataques, como:
   - XSS (Cross-site scripting)
   - Inyecciones SQL
   - Inyecciones de scripts

3. **Limitación de tasa (Rate limiting):**
   Se limita el número de solicitudes permitidas por IP a 100 por hora. Si se supera este límite, se bloqueará la IP temporalmente.

4. **Registro de solicitudes bloqueadas:**
   Cada vez que se bloquea una solicitud, se registra en el archivo `waf_log.txt` con el IP y el motivo del bloqueo.

## Uso

Para proteger tu aplicación con el WAF, simplemente incluye el siguiente código al inicio de tus archivos PHP principales:

```php
require_once 'SimpleWAF.php';
$waf = new SimpleWAF();
$waf->protect();
```

Este código verificará las solicitudes y aplicará las medidas de seguridad de acuerdo con las reglas establecidas.

## Personalización

1. **Agregar IPs a la lista blanca (whitelist):**
   Si deseas que ciertas IPs estén exentas de los bloqueos, puedes agregarlas a la lista `whitelistedIps` en el archivo `SimpleWAF.php`.

2. **Modificar las reglas de geolocalización:**
   Si deseas cambiar los países bloqueados, simplemente edita la lista `blockedCountries`.

3. **Modificar las reglas de limitación de tasa:**
   Puedes cambiar el límite de solicitudes y el tiempo de espera modificando las variables `$limit` y `$timeFrame` en el método `rateLimit`.

## Contacto

Si tienes preguntas o comentarios sobre este proyecto, no dudes en contactarme.

```

Este archivo proporciona instrucciones claras sobre la instalación, configuración y personalización de tu WAF, lo que lo hace más accesible para futuros usuarios o colaboradores.
