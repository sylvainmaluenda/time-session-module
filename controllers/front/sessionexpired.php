<?php

require_once _PS_MODULE_DIR_ . 'pscsession/infrastructure/ViteAssetManager.php';

use Pscsession\Infrastructure\ViteAssetManager;

class PscsessionSessionexpiredModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();

        // security : if someone arrives here logged in as BO
        $employee = Context::getContext()->employee;

        if ($employee) {
            $employee->logout();
        }
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            'module_dir' => __PS_BASE_URI__ . 'modules/' . $this->module->name . '/',
            'reactProps' => [
                'loginUrl' => $this->context->link->getAdminLink('AdminDashboard'),
            ],
        ]);

        $vite = $this->module->container()->get(ViteAssetManager::class);

        $this->context->smarty->assign([
            'vite' => $vite->render('src/sessionExpired/main.tsx'),
        ]);

        $this->setTemplate('module:pscsession/views/templates/front/SessionExpired.tpl');
    }
}
