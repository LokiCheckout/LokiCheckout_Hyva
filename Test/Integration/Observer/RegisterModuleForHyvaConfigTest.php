<?php declare(strict_types=1);

namespace LokiCheckout\Hyva\Test\Integration\Observer;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerConfig;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use Yireo\HyvaThemeAutoRegistration\Observer\RegisterModuleForHyvaConfig;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GetObjectManager;

#[AppArea('frontend')]
class RegisterModuleForHyvaConfigTest extends TestCase
{
    use GetObjectManager;

    public function testDiArguments(): void
    {
        $objectManagerConfig = $this->getObjectManager()->get(ObjectManagerConfig::class);
        $this->assertInstanceOf(ObjectManagerConfig::class, $objectManagerConfig);
        $arguments = $objectManagerConfig->getArguments(RegisterModuleForHyvaConfig::class);
        $this->assertIsArray($arguments);

        $modulePrefixes = $this->getArrayArgument($arguments, 'modulePrefixes');
        foreach (['LokiCheckout_', 'Loki_'] as $prefix) {
            $this->assertContains($prefix, $modulePrefixes);
        }

        $skipModules = $this->getArrayArgument($arguments, 'skipModules');
        $this->assertContains('LokiCheckout_Luma', $skipModules);
    }

    public function testLokiModulesAreAddedToHyvaConfig(): void
    {
        $extensions = $this->runObserver();

        foreach (['LokiCheckout_Hyva', 'LokiCheckout_Core', 'Loki_FieldComponents', 'Loki_Base'] as $moduleName) {
            $this->assertModuleInExtensions($moduleName, $extensions);
        }
    }

    public function testLumaModuleIsSkipped(): void
    {
        $observer = $this->getObjectManager()->create(RegisterModuleForHyvaConfig::class, [
            'moduleNames' => ['LokiCheckout_Luma'],
        ]);
        $this->assertInstanceOf(RegisterModuleForHyvaConfig::class, $observer);

        $extensions = $this->runObserver($observer);

        foreach ($extensions as $extension) {
            $this->assertStringNotContainsString('LokiCheckout_Luma', $extension['src']);
            $this->assertStringNotContainsString('magento2-luma', $extension['src']);
        }
    }

    public function testNoDuplicatesAreAdded(): void
    {
        $extensions = $this->runObserver();
        $extensions = $this->runObserver(null, $extensions);

        $sources = array_column($extensions, 'src');
        $this->assertSame(array_unique($sources), $sources);
    }

    /**
     * @param array<int, array{src: string}> $extensions
     * @return array<int, array{src: string}>
     */
    private function runObserver(?RegisterModuleForHyvaConfig $observer = null, array $extensions = []): array
    {
        if (null === $observer) {
            $observer = $this->getObjectManager()->create(RegisterModuleForHyvaConfig::class);
            $this->assertInstanceOf(RegisterModuleForHyvaConfig::class, $observer);
        }

        $config = new DataObject(['extensions' => $extensions]);
        $event = new Event(['config' => $config]);
        $observer->execute(new Observer(['event' => $event]));

        $extensions = $config->getData('extensions');
        $this->assertIsArray($extensions);

        $validExtensions = [];
        foreach ($extensions as $extension) {
            $this->assertIsArray($extension);
            $this->assertArrayHasKey('src', $extension);
            $this->assertIsString($extension['src']);
            $validExtensions[] = ['src' => $extension['src']];
        }

        return $validExtensions;
    }

    /**
     * @param array<int, array{src: string}> $extensions
     */
    private function assertModuleInExtensions(string $moduleName, array $extensions): void
    {
        $componentRegistrar = $this->getObjectManager()->get(ComponentRegistrar::class);
        $this->assertInstanceOf(ComponentRegistrar::class, $componentRegistrar);
        $modulePath = (string)$componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);
        $this->assertNotEmpty($modulePath, 'Module "'.$moduleName.'" is not registered');

        foreach ($extensions as $extension) {
            if (str_ends_with($modulePath, $extension['src'])) {
                return;
            }
        }

        $this->fail('Module "'.$moduleName.'" not found in Hyva config: '.json_encode($extensions));
    }

    /**
     * @param array<mixed> $arguments
     * @return array<mixed>
     */
    private function getArrayArgument(array $arguments, string $name): array
    {
        $this->assertArrayHasKey($name, $arguments);
        $this->assertIsArray($arguments[$name]);
        $values = [];
        foreach ($arguments[$name] as $item) {
            $values[] = is_array($item) && array_key_exists('_v_', $item) ? $item['_v_'] : $item;
        }

        return $values;
    }
}
