<?php
declare(strict_types=1);

namespace Pscsession\Infrastructure;

use RuntimeException;

final class ViteAssetManager
{
    // Responsabilité :
    // Produit le HTML nécessaire pour le mode dev ou le mode prod."
    // Ne connaît ni Smarty ni les contrôleurs.

    /**
     * Manifest Vite chargé une seule fois.
     */
    private ?array $manifest = null;

    public function __construct(
        /**
         * Chemin absolu du module.
         *
         * Exemple :
         * C:\wamp64\www\prestashop\modules\pscsession
         */
        private readonly string $modulePath,

        /**
         * URI publique du module.
         *
         *
         * Exemple :
         * /modules/pscsession/
         */
        private readonly string $moduleUri,

        /**
         * URL du serveur Vite.
         */
        private readonly string $devServer = 'http://localhost:5173',
    ) {}

    /**
     * Génère le HTML permettant de charger une entry Vite.
     *
     * Exemple :
     *
     * render('src/sessionExpired/main.tsx')
     */
    public function render(string $entry): string
    {
        return $this->isDevMode() ? $this->renderDev($entry) : $this->renderProd($entry);
    }

    /**
     * Détermine automatiquement si Vite est lancé.
     *
     * Si http://localhost:5173/@vite/client répond,
     * on considère qu'on est en développement.
     */
    private function isDevMode(): bool
    {
        return @file_get_contents($this->devServer . '/@vite/client') !== false;
    }

    /**
     * Génère les scripts nécessaires au mode développement.
     */
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

    /**
     * Génère les balises HTML à partir du manifest.
     */
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
                '<link rel="stylesheet" href="%sviews/dist/%s">' . PHP_EOL,
                $this->moduleUri,
                $manifest['style.css']['file'],
            );
        }

        /*
         * Bundle JS
         */
        $html .= sprintf(
            '<script type="module" src="%sviews/dist/%s"></script>',
            $this->moduleUri,
            $manifest[$entry]['file'],
        );

        return $html;
    }

    /**
     * Charge le manifest Vite une seule fois.
     */
    private function manifest(): array
    {
        if ($this->manifest !== null) {
            return $this->manifest;
        }

        $manifestPath = $this->modulePath . '/views/dist/.vite/manifest.json';

        if (!file_exists($manifestPath)) {
            throw new RuntimeException('Manifest Vite introuvable.');
        }

        $this->manifest = json_decode(file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        return $this->manifest;
    }
}
