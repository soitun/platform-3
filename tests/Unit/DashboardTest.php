<?php

declare(strict_types=1);

namespace Orchid\Tests\Unit;

use Orchid\Platform\Dashboard;
use Orchid\Platform\Models\User;
use Orchid\Tests\TestUnitCase;

/**
 * Class DashboardTest.
 */
class DashboardTest extends TestUnitCase
{
    public function testIsVersion(): void
    {
        $this->assertEquals(Dashboard::VERSION, Dashboard::version());
    }

    public function testIsModelDefault(): void
    {
        $class = Dashboard::modelClass('UnknownClass', User::class);

        $default = new User();

        $this->assertEquals($class, $default);
    }

    public function testIsModelCustomNotFound(): void
    {
        Dashboard::useModel(User::class, 'MyCustomClass');

        $user = Dashboard::modelClass(User::class);

        $this->assertEquals('MyCustomClass', $user);
    }

    public function testIsModelConfigure(): void
    {
        Dashboard::configure([
            'models' => [
                User::class => 'MyCustomClass',
            ],
        ]);

        $class = Dashboard::model(User::class);
        $option = Dashboard::option('models.'.User::class);

        $this->assertEquals('MyCustomClass', $class);
        $this->assertEquals('MyCustomClass', $option);
        $this->assertEquals(null, Dashboard::option('random'));
    }

    public function testIsRegisterResource(): void
    {
        $dashboard = new Dashboard();

        $script = $dashboard
            ->registerResource('scripts', 'app.js')
            ->getResource('scripts');

        $this->assertEquals([
            'app.js',
        ], $script);

        $stylesheets = $dashboard
            ->registerResource('stylesheets', 'style.css')
            ->getResource('stylesheets');

        $this->assertEquals([
            'style.css',
        ], $stylesheets);

        $this->assertEquals($dashboard->getResource(), collect([
            'scripts'     => [
                'app.js',
            ],
            'stylesheets' => [
                'style.css',
            ],
        ]));

        $rewriteScript = $dashboard
            ->registerResource('scripts', 'custom-app.js')
            ->getResource('scripts');

        $this->assertEquals([
            'app.js',
            'custom-app.js',
        ], $rewriteScript);

        $rewriteStyle = $dashboard
            ->registerResource('stylesheets', 'custom-style.css')
            ->getResource('stylesheets');

        $this->assertEquals([
            'style.css',
            'custom-style.css',
        ], $rewriteStyle);
    }

    /**
     * @param string $name
     */
    public function testIsMacro($name = 'customMarcoName'): void
    {
        Dashboard::macro('returnNameMacroFunction', function (string $test) {
            return $test;
        });

        $this->assertEquals(Dashboard::returnNameMacroFunction($name), $name);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Dashboard::configure([]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Dashboard::configure([]);
    }
}
