<?php

require_once _PS_MODULE_DIR_ . 'pscsession/bootstrap.php';

use Pscsession\infrastructure\ViteAssetManager;

class PscsessionSessionexpiredModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();

        $employee = Context::getContext()->employee;

        if ($employee) {
            $employee->logout();
        }

        $vite = $this->module->container()->get(ViteAssetManager::class);

        $props = htmlspecialchars(
            json_encode([
                'reviewUrl' => $this->context->link->getModuleLink('pscsession', 'review'),
            ]),
            ENT_QUOTES,
            'UTF-8',
        );

        echo sprintf(
            '%s<div id="session-expired-root" data-props="%s"></div>',
            $vite->render('src/sessionExpired/main.tsx'),
            $props,
        );

        exit();
    }
}
