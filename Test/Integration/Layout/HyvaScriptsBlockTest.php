<?php declare(strict_types=1);

namespace LokiCheckout\Hyva\Test\Integration\Layout;

use Loki\Components\Util\Controller\LayoutLoader;
use LokiCheckout\Core\Test\Integration\Layout\AbstractLayoutTestCase;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DataFixture;

/**
 * The handle "default_hyva" is added by the Hyvä theme itself (via its Magento_Theme/layout/default.xml)
 */
#[DataFixture(GuestCartFixture::class, as: 'cart')]
#[AppArea('frontend')]
class HyvaScriptsBlockTest extends AbstractLayoutTestCase
{
    private const ALPINE_PLUGIN_BLOCKS = [
        'alpine-plugin-snap-slider',
        'alpine-plugin-htmldialog',
        'alpine-plugin-intersect',
        'alpine-plugin-ignore',
    ];

    private const BODY_END_BLOCKS = [
        'script-alpine-js',
        'script-cookies',
        'script-private-content',
        'view-model-identities',
        'form-submit.set-uenc',
        'require-alpine-v3',
        'init.external.scripts',
        'page-js-dependencies',
        'default-section-data',
    ];

    public function testHyvaScriptsAreAddedWithHyvaHandle(): void
    {
        $layout = $this->loadLayout(['checkout_index_index', 'default_hyva']);

        foreach (self::BODY_END_BLOCKS as $blockName) {
            $this->assertNotFalse($layout->getBlock($blockName), 'Block "'.$blockName.'" not found');
            $this->assertSame('before.body.end', $layout->getParentName($blockName));
        }

        foreach (self::ALPINE_PLUGIN_BLOCKS as $blockName) {
            $this->assertNotFalse($layout->getBlock($blockName), 'Block "'.$blockName.'" not found');
            $this->assertSame('script-alpine-js', $layout->getParentName($blockName));
        }

        $block = $layout->getBlock('script-alpine-js');
        $this->assertInstanceOf(Template::class, $block);
        $this->assertSame('Hyva_Theme::page/js/alpinejs.phtml', $block->getTemplate());

        $headTemplates = $this->getChildTemplates($layout, 'head.additional');
        $this->assertContains('Hyva_Theme::page/js/variables.phtml', $headTemplates);
        $this->assertContains('Hyva_Theme::page/js/hyva.phtml', $headTemplates);
    }

    public function testHyvaChildBlocksArePreserved(): void
    {
        $layout = $this->loadLayout(['checkout_index_index', 'default_hyva']);

        $childBlocks = [
            'alpine-plugin-defer' => 'script-alpine-js',
            'alpine-defer-rules' => 'alpine-plugin-defer',
        ];

        foreach ($childBlocks as $blockName => $parentName) {
            $this->assertNotFalse($layout->getBlock($blockName), 'Block "'.$blockName.'" was removed');
            $this->assertSame($parentName, $layout->getParentName($blockName));
        }
    }

    public function testHyvaHeadScriptsAreNotDuplicated(): void
    {
        $layout = $this->loadLayout(['checkout_index_index', 'default_hyva']);
        $headTemplates = $this->getChildTemplates($layout, 'head.additional');

        foreach (['Hyva_Theme::page/js/variables.phtml', 'Hyva_Theme::page/js/hyva.phtml'] as $template) {
            $this->assertCount(
                1,
                array_keys($headTemplates, $template, true),
                'Template "'.$template.'" is added more than once'
            );
        }
    }

    public function testHyvaScriptsAreNotAddedWithoutHyvaHandle(): void
    {
        $layout = $this->loadLayout(['checkout_index_index']);

        $this->assertNotContains('default_hyva', $layout->getUpdate()->getHandles());

        foreach (array_merge(self::BODY_END_BLOCKS, self::ALPINE_PLUGIN_BLOCKS) as $blockName) {
            $this->assertFalse($layout->getBlock($blockName), 'Block "'.$blockName.'" found anyway');
        }
    }

    /**
     * @param string[] $handles
     */
    private function loadLayout(array $handles): LayoutInterface
    {
        $layoutLoader = $this->getObjectManager()->get(LayoutLoader::class);
        $this->assertInstanceOf(LayoutLoader::class, $layoutLoader);
        return $layoutLoader->load($handles, [], true);
    }

    /**
     * @return string[]
     */
    private function getChildTemplates(LayoutInterface $layout, string $parentName): array
    {
        $templates = [];
        foreach ($layout->getChildNames($parentName) as $childName) {
            $block = $layout->getBlock($childName);
            if ($block instanceof Template) {
                $templates[] = $block->getTemplate();
            }
        }

        return $templates;
    }
}
