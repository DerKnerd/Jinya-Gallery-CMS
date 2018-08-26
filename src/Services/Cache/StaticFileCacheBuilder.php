<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 19:33
 */

namespace Jinya\Services\Cache;

use Doctrine\Common\Collections\Collection;
use Jinya\Components\Form\FormGeneratorInterface;
use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Menu\MenuItem;
use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;
use Jinya\Services\Pages\PageServiceInterface;
use Jinya\Services\Twig\CompilerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Underscore\Types\Strings;

class StaticFileCacheBuilder implements CacheBuilderInterface
{
    /** @var ConfigurationServiceInterface */
    private $configurationService;

    /** @var ArtworkServiceInterface */
    private $artworkService;

    /** @var ArtGalleryServiceInterface */
    private $artGalleryService;

    /** @var VideoGalleryServiceInterface */
    private $videoGalleryService;

    /** @var PageServiceInterface */
    private $pageService;

    /** @var FormServiceInterface */
    private $formService;

    /** @var FormGeneratorInterface */
    private $formGenerator;

    /** @var CompilerInterface */
    private $compiler;

    /** @var string */
    private $kernelProjectDir;

    /**
     * StaticFileCacheBuilder constructor.
     * @param ConfigurationServiceInterface $configurationService
     * @param ArtworkServiceInterface $artworkService
     * @param ArtGalleryServiceInterface $artGalleryService
     * @param VideoGalleryServiceInterface $videoGalleryService
     * @param PageServiceInterface $pageService
     * @param FormServiceInterface $formService
     * @param FormGeneratorInterface $formGenerator
     * @param CompilerInterface $compiler
     * @param string $kernelProjectDir
     */
    public function __construct(ConfigurationServiceInterface $configurationService, ArtworkServiceInterface $artworkService, ArtGalleryServiceInterface $artGalleryService, VideoGalleryServiceInterface $videoGalleryService, PageServiceInterface $pageService, FormServiceInterface $formService, FormGeneratorInterface $formGenerator, CompilerInterface $compiler, string $kernelProjectDir)
    {
        $this->configurationService = $configurationService;
        $this->artworkService = $artworkService;
        $this->artGalleryService = $artGalleryService;
        $this->videoGalleryService = $videoGalleryService;
        $this->pageService = $pageService;
        $this->formService = $formService;
        $this->formGenerator = $formGenerator;
        $this->compiler = $compiler;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * Builds the cache
     */
    public function buildCache(): void
    {
        $routes = $this->getRoutesFromTheme();

        foreach ($routes as $route) {
            if ('empty' !== $route->getMenuItem()->getPageType() && 'external' !== $route->getMenuItem()->getPageType()) {
                $compiledTemplate = $this->compileRoute($route);
                $file = $this->cacheTemplate($compiledTemplate, $route);
                $this->addEntryToCacheList($file);
            }
        }

        $startPageRoute = new RoutingEntry();
        $startPageRoute->setUrl('index.html');
        $compiledTemplate = $this->compileRoute($startPageRoute);
        $file = $this->cacheTemplate($compiledTemplate, $startPageRoute);
        $this->addEntryToCacheList($file);
    }

    /**
     * @return RoutingEntry[]
     */
    private function getRoutesFromTheme(): array
    {
        $currentTheme = $this->configurationService->getConfig()->getCurrentTheme();
        $routes = [];
        if ($currentTheme->getPrimaryMenu()) {
            foreach ($this->findRoutesInMenu($currentTheme->getPrimaryMenu()) as $route) {
                $routes[] = $route;
            }
        }
        if ($currentTheme->getSecondaryMenu()) {
            foreach ($this->findRoutesInMenu($currentTheme->getSecondaryMenu()) as $route) {
                $routes[] = $route;
            }
        }
        if ($currentTheme->getFooterMenu()) {
            foreach ($this->findRoutesInMenu($currentTheme->getFooterMenu()) as $route) {
                $routes[] = $route;
            }
        }

        return $routes;
    }

    private function findRoutesInMenu(Menu $menu): array
    {
        $items = $menu->getMenuItems();

        return $this->getItems($items);
    }

    /**
     * @param Collection $items
     * @return array
     */
    private function getItems(Collection $items): array
    {
        $routes = [];

        foreach ($items as $item) {
            if ('empty' === $item->getPageType()) {
                foreach ($this->findRoutesInMenuItem($item) as $entry) {
                    $routes[] = $entry;
                }
            } else {
                $routes[] = $item->getRoute();
            }
        }

        return $routes;
    }

    /**
     * @param MenuItem $menuItem
     * @return array
     */
    private function findRoutesInMenuItem(MenuItem $menuItem): array
    {
        $items = $menuItem->getChildren();

        return $this->getItems($items);
    }

    private function compileRoute(RoutingEntry $route): string
    {
        $routeParameter = $route->getRouteParameter();

        if (is_array($routeParameter) && array_key_exists('slug', $routeParameter)) {
            $slug = $routeParameter['slug'];
        } else {
            $slug = '';
        }

        if (Strings::find($route->getRouteName(), 'artwork')) {
            $viewData = ['artwork' => $this->artworkService->get($slug)];
            $template = '@Frontend/Artwork/detail.html.twig';
        } elseif (Strings::find($route->getRouteName(), 'video_gallery')) {
            $viewData = [
                'gallery' => $this->videoGalleryService->get($slug),
                'type' => 'video',
            ];
            $template = '@Frontend/Gallery/detail.html.twig';
        } elseif (Strings::find($route->getRouteName(), 'art_gallery') || Strings::find($route->getRouteName(), 'gallery')) {
            $viewData = [
                'gallery' => $this->artGalleryService->get($slug),
                'type' => 'art',
            ];
            $template = '@Frontend/Gallery/detail.html.twig';
        } elseif (Strings::find($route->getRouteName(), 'form')) {
            $formEntity = $this->formService->get($slug);
            $form = $this->formGenerator->generateForm($formEntity);
            $viewData = [
                'formEntity' => $formEntity,
                'form' => $form->createView(),
            ];
            $template = '@Frontend/Form/detail.html.twig';
        } elseif (Strings::find($route->getRouteName(), 'page')) {
            $viewData = ['page' => $this->pageService->get($slug)];
            $template = '@Frontend/Page/detail.html.twig';
        } else {
            $viewData = [];
            $template = '@Frontend/Default/index.html.twig';
        }

        return $this->compiler->compile($template, $viewData);
    }

    private function cacheTemplate(string $template, RoutingEntry $route): string
    {
        $path = $this->kernelProjectDir . '/public/' . $route->getUrl();
        $fs = new Filesystem();
        $fs->dumpFile($path, $template);

        return $path;
    }

    private function addEntryToCacheList(string $entry): void
    {
        $fs = new Filesystem();
        $fs->appendToFile($this->getCacheFile(), $entry);
    }

    private function getCacheFile(): string
    {
        return $this->kernelProjectDir . '/public/cache.status';
    }

    /**
     * Clears the cache
     */
    public function clearCache(): void
    {
        $handle = fopen($this->getCacheFile(), 'r');
        if ($handle) {
            try {
                while (false !== ($line = fgets($handle))) {
                    @unlink($line);
                }
            } finally {
                fclose($handle);
            }
        }
    }

    /**
     * Builds the cache for the given routing entry
     *
     * @param RoutingEntry $routingEntry
     */
    public function buildRouteCache(RoutingEntry $routingEntry): void
    {
        $compiledTemplate = $this->compileRoute($routingEntry);
        $file = $this->cacheTemplate($compiledTemplate, $routingEntry);
        $this->addEntryToCacheList($file);
    }

    /**
     * Builds the cache for the given slug and type
     *
     * @param string $slug
     * @param string $type
     */
    public function buildCacheBySlugAndType(string $slug, string $type): void
    {
        $routes = $this->getRoutesFromTheme();
        foreach ($routes as $route) {
            $routeSlug = array_key_exists('slug', $route->getRouteParameter()) ? $route->getRouteParameter()['slug'] : '';
            if ($route->getMenuItem()->getPageType() === $type && $slug === $routeSlug) {
                $compiledTemplate = $this->compileRoute($route);
                $file = $this->cacheTemplate($compiledTemplate, $route);
                $this->addEntryToCacheList($file);
            }
        }
    }
}
