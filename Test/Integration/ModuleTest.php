<?php declare(strict_types=1);

namespace LokiCheckout\Hyva\Test\Integration;

use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;

class ModuleTest extends TestCase
{
    use AssertModuleIsRegistered;
    use AssertModuleIsRegisteredForReal;
    use AssertModuleIsEnabled;

    public function testIfModuleIsEnabled(): void
    {
        $this->assertModuleIsRegistered('LokiCheckout_Hyva');
        $this->assertModuleIsRegisteredForReal('LokiCheckout_Hyva');
        $this->assertModuleIsEnabled('LokiCheckout_Hyva');
    }

    public function testIfRequiredModulesAreEnabled(): void
    {
        $requiredModules = [
            'Hyva_Theme',
            'LokiCheckout_Core',
            'Yireo_HyvaThemeAutoRegistration',
        ];

        foreach ($requiredModules as $moduleName) {
            $this->assertModuleIsRegistered($moduleName);
            $this->assertModuleIsEnabled($moduleName);
        }
    }
}
