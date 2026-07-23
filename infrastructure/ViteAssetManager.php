<?php
declare(strict_types=1);

namespace Pscsession\Infrastructure;

use RuntimeException;

final class ViteAssetManager
{
    private ?array $manifest = null;

    public function __construct(
        private readonly string $modulePath,
        private readonly string $moduleUri,
        private readonly string $devServer,
    ) {}

    public function render(string $entry): string
    {
        return $this->isDevMode() ? $this->renderDev($entry) : $this->renderProd($entry);
    }

    private function isDevMode(): bool
    {
        return @file_get_contents($this->devServer . '/@vite/client') !== false;
    }

    private function renderDev(string $entry): string
    {
        return <<<HTML
        <script type="module">
        import RefreshRuntime from '{$this->devServer}/@react-refresh';
        RefreshRuntime.injectIntoGlobalHook(window);
        window.\$RefreshReg\$ = () => {};
        window.\$RefreshSig\$ = () => (type) => type;
        window.__vite_plugin_react_preamble_installed__ = true;
        </script>

        <script type="module" src="{$this->devServer}/@vite/client"></script>
        <script type="module" src="{$this->devServer}/{$entry}"></script>

        HTML;
    }

    private function renderProd(string $entry): string
    {
        $manifest = $this->manifest();

        if (!isset($manifest[$entry])) {
            throw new RuntimeException(sprintf('Entry "%s" introuvable dans le manifest.', $entry));
        }

        $html = '';

        /*
         * CSS global (cssCodeSplit = false)
         */
        if (isset($manifest['style.css'])) {
            $html .= sprintf(
                '<link rel="stylesheet" href="%sbuild/dist/%s">' . PHP_EOL,
                $this->moduleUri,
                $manifest['style.css']['file'],
            );
        }

        /*
         * Bundle JS
         */
        $html .= sprintf(
            '<script type="module" src="%sbuild/dist/%s"></script>',
            $this->moduleUri,
            $manifest[$entry]['file'],
        );

        return $html;
    }

    private function manifest(): array
    {
        if ($this->manifest !== null) {
            return $this->manifest;
        }

        $manifestPath = $this->modulePath . '/build/dist/.vite/manifest.json';

        if (!file_exists($manifestPath)) {
            throw new RuntimeException(sprintf('Manifest Vite introuvable : %s', $manifestPath));
        }

        $this->manifest = json_decode(file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        return $this->manifest;
    }
}
