<?php declare(strict_types=1);

namespace LokiCheckout\Hyva\Test\Integration\Config;

use Loki\Field\Config\Config as FieldConfig;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertStoreConfigValueEquals;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GetObjectManager;

#[AppArea('frontend')]
class UseRequiredBlockTest extends TestCase
{
    use GetObjectManager;
    use AssertStoreConfigValueEquals;

    public function testDefaultValueIsDisabled(): void
    {
        $this->assertStoreConfigValueEquals(0, 'loki_field_components/fields/use_required_block');
        $this->assertFalse($this->getFieldConfig()->useRequiredBlock());
    }

    #[ConfigFixture('loki_field_components/fields/use_required_block', 1, 'store', 'default')]
    public function testValueCanBeOverridden(): void
    {
        $this->assertTrue($this->getFieldConfig()->useRequiredBlock());
    }

    private function getFieldConfig(): FieldConfig
    {
        return $this->getObjectManager()->get(FieldConfig::class);
    }
}
