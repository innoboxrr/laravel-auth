<?php

namespace Innoboxrr\LaravelAuth\Tests\Package;

use Illuminate\Support\Facades\Route;
use Innoboxrr\LaravelAuth\Tests\TestCase;

/**
 * Lo minimo que tiene que cumplir cualquier version que se publique: que el
 * paquete arranca dentro de la version de Laravel contra la que se prueba.
 *
 * No sustituye a tests de comportamiento. Existe para que la puerta de
 * publicacion compruebe algo real en un paquete que todavia no los tiene: que
 * sus clases cargan, que sus proveedores se registran, que sus rutas apuntan a
 * metodos que existen y que sus migraciones corren.
 */
final class PackageBootsTest extends TestCase
{
    /**
     * Los proveedores que declara composer.json, ademas de los que registre la
     * TestCase del paquete, si tiene una propia.
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return array_values(array_unique([...parent::getPackageProviders($app), ...self::packageProviders()]));
    }

    public function test_los_proveedores_declarados_se_registran(): void
    {
        foreach (self::packageProviders() as $provider) {
            $this->assertNotNull($this->app->getProvider($provider), "{$provider} no se registro.");
        }

        $this->addToAssertionCount(1);
    }

    /**
     * Cargar una clase resuelve su padre, sus interfaces y sus traits. Es la
     * forma mas barata de enterarse de que una version de Laravel quito algo
     * que el paquete usa, o de que el paquete usa algo que no declara.
     *
     * Una clase que necesita un paquete sugerido, y no requerido, no cuenta:
     * sin ese paquete no se puede cargar, y es lo esperado.
     */
    public function test_todas_las_clases_del_paquete_cargan(): void
    {
        $failures = [];

        foreach (self::classes() as $class => [$file, $declared]) {
            // Un archivo cuyo namespace no cuadra con su ruta no se carga: si
            // otro archivo declara la misma clase, PHP muere con "Cannot
            // redeclare" y se lleva la suite entera. Se reporta y se sigue.
            if ($declared !== $class) {
                $failures[] = "{$file} declara {$declared}, pero por PSR-4 deberia declarar {$class}.";

                continue;
            }

            try {
                if (! class_exists($class) && ! interface_exists($class) && ! trait_exists($class) && ! enum_exists($class)) {
                    $failures[] = "{$class}: {$file} no declara una clase con ese nombre.";
                }
            } catch (\Throwable $e) {
                if (! self::fromSuggestedPackage($e->getMessage())) {
                    $failures[] = "{$class}: {$e->getMessage()}";
                }
            }
        }

        $this->assertSame([], $failures);
    }

    public function test_las_rutas_apuntan_a_metodos_que_existen(): void
    {
        $failures = [];

        foreach (Route::getRoutes() as $route) {
            $uses = $route->getAction('uses');

            if (! is_string($uses)) {
                continue;
            }

            [$controller, $method] = array_pad(explode('@', $uses, 2), 2, '__invoke');

            if (! class_exists($controller)) {
                $failures[] = "{$route->uri()}: {$controller} no existe.";
            } elseif (! method_exists($controller, $method)) {
                $failures[] = "{$route->uri()}: {$controller}::{$method}() no existe.";
            }
        }

        $this->assertSame([], $failures);
    }

    /**
     * Con nombre propio y no los de la TestCase: un paquete puede traer una
     * TestCase valida que no los tenga, o que los tenga con otra firma.
     *
     * @return array<string, mixed>
     */
    private static function packageComposer(): array
    {
        return json_decode((string) file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);
    }

    /**
     * @return array<int, class-string>
     */
    private static function packageProviders(): array
    {
        return self::packageComposer()['extra']['laravel']['providers'] ?? [];
    }

    /**
     * La clase que PSR-4 espera en cada archivo, con la que el archivo declara.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    private static function classes(): array
    {
        $root = dirname(__DIR__, 2);
        $classes = [];

        foreach (self::packageComposer()['autoload']['psr-4'] ?? [] as $prefix => $paths) {
            foreach ((array) $paths as $path) {
                $base = $root . '/' . trim($path, '/');

                if (! is_dir($base)) {
                    continue;
                }

                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS));

                foreach ($files as $file) {
                    if ($file->getExtension() !== 'php') {
                        continue;
                    }

                    $content = (string) file_get_contents($file->getPathname());

                    if (! preg_match('/^\s*(?:(?:abstract|final|readonly)\s+)*(?:class|interface|trait|enum)\s+(\w+)/m', $content, $declared)) {
                        continue;
                    }

                    $relative = substr(str_replace('\\', '/', $file->getPathname()), strlen(str_replace('\\', '/', $base)) + 1);
                    $class = $prefix . str_replace('/', '\\', substr($relative, 0, -4));

                    $namespace = preg_match('/^namespace\s+([^;\s]+)\s*;/m', $content, $ns) ? $ns[1] . '\\' : '';

                    $classes[$class] = [$relative, $namespace . $declared[1]];
                }
            }
        }

        ksort($classes);

        return $classes;
    }

    private static function fromSuggestedPackage(string $message): bool
    {
        if (! preg_match('/(?:Class|Interface|Trait|Enum) "([^"]+)" not found/', $message, $missing)) {
            return false;
        }

        foreach (array_keys(self::packageComposer()['suggest'] ?? []) as $package) {
            $namespace = implode('\\', array_map(
                fn (string $part): string => str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $part))),
                explode('/', $package)
            ));

            if (stripos($missing[1], $namespace . '\\') === 0) {
                return true;
            }
        }

        return false;
    }
}
