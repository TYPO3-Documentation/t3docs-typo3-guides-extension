<?php

declare(strict_types=1);

use phpDocumentor\Guides\Cli\Command\Run;
use phpDocumentor\Guides\Interlink\InventoryRepository;
use phpDocumentor\Guides\Renderer\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use T3Docs\GuidesExtension\Command\RunDecorator;
use T3Docs\GuidesExtension\Renderer\UrlGenerator\RenderOutputUrlGenerator;
use T3Docs\GuidesExtension\Renderer\UrlGenerator\SingleHtmlUrlGenerator;
use T3Docs\Typo3DocsTheme\Inventory\Typo3InventoryRepository;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
        ->autowire()
        ->set(RunDecorator::class)
        ->decorate(
            Run::class,
        )->args([service('.inner')])
        ->set(\T3Docs\GuidesExtension\Renderer\SinglePageRenderer::class)
        ->tag(
            'phpdoc.renderer.typerenderer',
            [
                'noderender_tag' => 'phpdoc.guides.noderenderer.singlepage',
                'format' => 'singlepage',
            ],
        )
        ->set(\T3Docs\GuidesExtension\Renderer\NodeRenderer\SinglePageDocumentRenderer::class)
        ->tag('phpdoc.guides.noderenderer.singlepage')

        ->set(InventoryRepository::class, Typo3InventoryRepository::class)
        ->arg('$inventoryConfigs', param('phpdoc.guides.inventories'))

        ->set(SingleHtmlUrlGenerator::class)
        ->set(UrlGeneratorInterface::class, RenderOutputUrlGenerator::class)

        ->set(\phpDocumentor\Guides\NodeRenderers\DelegatingNodeRenderer::class)
        ->call('setNodeRendererFactory', [service('phpdoc.guides.noderenderer.factory.html')])
        ->tag('phpdoc.guides.noderenderer.singlepage')
    ;
};
