# Laravel Auth Package

## Comenzando 🚀

El paquete innoboxrr/laravel-auth nace ante la demanda de un sistema de autenticación completo y centralizado que se adapte a cualquier tipo de aplicación, ya sea una web convencional, una SPA o una APIRestful.

Nuestra ambición es que, a medida que el paquete evolucione, se consolide como una alternativa unificada y accesible, sin que su implementación requiera cambios significativos en la estructura de un proyecto existente. Además, estamos comprometidos con el desarrollo de estrategias de autenticación innovadoras y vanguardistas.

El paquete de Laravel Auth se destaca por su alto nivel de personalización. La implementación de Closures o funciones anónimas permite a los desarrolladores adaptar cada parte del código a sus necesidades específicas. Además, incluye un archivo de configuración con opciones para personalizar aún más su comportamiento.

¿Por qué elegir innoboxrr/laravel-auth en lugar de las soluciones de autenticación que Laravel ofrece actualmente? La respuesta es sencilla: flexibilidad y desacoplamiento. Este paquete ha sido diseñado para integrarse sin problemas con cualquier estructura de proyecto, minimizando los posibles conflictos.

En términos de seguridad, el paquete innoboxrr/laravel-auth adopta los mismos sistemas que Laravel recomienda y proporciona, pero de una manera unificada y armónica. Esto significa que puedes centrarte en desarrollar tu aplicación sin tener que preocuparte por cada aspecto de la implementación de la seguridad. Nuestro paquete se encarga de ello, dándote la tranquilidad de que tu aplicación está protegida.

## Pre-requisitos 📋

### Requisitos del sistema:
- PHP 8.1 o superior
- Composer

### Dependencias
- "laravel/sanctum": "^3.2",
- "laravel/socialite": "^5.6"

## Instalación 🔧

Pasos para la instalación del proyecto.

Para instalar el paquete solo debe ejecutar 

``composer require innoboxrr/laravel-auth``

## Ejecutando las pruebas ⚙️

... En construcción


## Construido con 🛠️

Lista de tecnologías y herramientas utilizadas en el proyecto:
- PHP
- Laravel 10
- Composer

## Contributing 🖇️

Si desea colaborar dar sugerencias o reportar alguna falla en el código lo puede hacer a través de los issues de GitHub en: [https://github.com/innoboxrr/laravel-auth/issues](https://github.com/innoboxrr/laravel-auth/issues)

## Versionado 📌

El proyecto emplea un sistema de versionadao SemVer que le permite identificar la corrección de errores, la implementación de nuevas características así como actualizaciones mayores. En estos casos le proporcionaremos información detallada para realizar las actualizaciones correspondientes.

## Autores ✒️

Lista de los autores del proyecto.
 - Homero Raul Vargas Cruz

## Licencia 📄

Este paquete se encuentra bajo la licencia MIT. Esto significa que puedes usarlo de manera gratuita para cualquier propósito, incluso para propósitos comerciales. No obstante, hay algunas condiciones que debes tener en cuenta.

La licencia MIT te concede el derecho de usar, copiar, modificar, fusionar, publicar, distribuir, sublicenciar y/o vender copias del software. Sin embargo, en todos estos casos, debes proporcionar una copia del aviso de la licencia y el aviso de derechos de autor en todas las copias o partes sustanciales del software.

El software se proporciona "tal cual", sin garantía de ningún tipo, expresa o implícita, incluyendo pero no limitado a las garantías de comerciabilidad, aptitud para un propósito particular y no infracción. En ningún caso, los autores o los titulares de los derechos de autor serán responsables de cualquier reclamación, daños u otras responsabilidades, ya sea en una acción de contrato, agravio o de otro modo, que surjan de, estén en conexión con el software o el uso u otras operaciones en el software.

Para ver la licencia completa, por favor visita el archivo LICENSE incluido en este paquete o haz [click aquí](https://opensource.org/licenses/MIT) para verla en la página oficial de la licencia MIT en Open Source Initiative.

## Expresiones de Gratitud 🎁

Como en todos mis proyectos agradezco a mis padres, hermanos, esposa e hijos quienes son mi inspiración mi motor y razón de dar cada día siempre lo mejor de mi.

⌨️ con ❤️ por Homero Raul Vargas Cruz 😊

## Notas para el desarrollador

De manera general el listado de funciones que ofrece el paquete actualmente son:

- Ruta `POST` para **registrar** al usuario
- Ruta `POST` para **autenticar** al usuario
- Ruta `POST` para **cerrar sesión** del usuario
- Ruta `POST` para **enviar enlace de recuperación de cuenta** para restablecer la cuenta
- Ruta `POST` para **restablecer la cuenta del usuario** mediente un hash de verificación.
- Ruta `POST` para **mandar email de verificación** 
- Ruta `POST` para **recuperar el usuario actualmente identificado**
- Ruta `GET` para **verificar el correo** después de que el usuario ha dado clic en el correo de verificación.
- Ruta `POST` para **crear un token de acceso**. Esto es util para identificaciones via API
- Ruta `POST` para **recuperar los tokens** de un usuario actualmente identificado
- Ruta `POST` para **revocar un token** especifico de un usuario
- Ruta `POST` para **revocar todos los tokens** de un usuario identificado
- Ruta `GET` para **login o registro con redes sociales**
- Ruta `GET` para **confirmar el login con redes sociales**