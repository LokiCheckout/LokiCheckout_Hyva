<?php declare(strict_types=1);

namespace LokiCheckout\Hyva\Test\Integration\Config;

use LokiCheckout\Core\Config\LokiCheckoutModules;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GetObjectManager;

class LokiCheckoutModulesTest extends TestCase
{
    use GetObjectManager;

    public function testModuleIsRegisteredAsLokiCheckoutModule(): void
    {
        $lokiCheckoutModules = $this->getObjectManager()->create(LokiCheckoutModules::class);
        $this->assertInstanceOf(LokiCheckoutModules::class, $lokiCheckoutModules);

        $this->assertTrue($lokiCheckoutModules->hasModule('LokiCheckout_Hyva'));
        $this->assertContains('LokiCheckout_Hyva', $lokiCheckoutModules->getModules());
        $this->assertContains('LokiCheckout_Hyva', $lokiCheckoutModules->getActiveModules());
    }
}
