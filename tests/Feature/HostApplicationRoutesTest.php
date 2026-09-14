<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as FoundationRouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Innoboxrr\LaravelAuth\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ReflectionProperty;

/**
 * En Laravel 11+ las rutas de la aplicación las carga el RouteServiceProvider
 * de Foundation con el callback de `withRouting()`. Un proveedor de paquete que
 * hereda de ese proveedor vuelve a llamar al callback al arrancar, y la
 * aplicación registra sus rutas dos veces.
 */
class HostApplicationRoutesTest extends TestCase
{
    public static int $hostRouteLoads = 0;

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        self::$hostRouteLoads = 0;

        FoundationRouteServiceProvider::loadRoutesUsing(function (): void {
            self::$hostRouteLoads++;
        });
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(FoundationRouteServiceProvider::class, 'alwaysLoadRoutesUsing'))->setValue(null, null);

        parent::tearDown();
    }

    #[Test]
    public function el_paquete_no_vuelve_a_cargar_las_rutas_de_la_aplicacion(): void
    {
        $this->assertSame(0, self::$hostRouteLoads);
    }

    #[Test]
    public function las_rutas_del_paquete_siguen_registradas(): void
    {
        $this->assertTrue(Route::has($this->authRouteName('login')));
    }

    private function authRouteName(string $name): string
    {
        return config('laravel-auth.routes.as').$name;
    }
}
