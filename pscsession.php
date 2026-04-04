<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class pscsession extends Module
{
    const END_DATE = '2026-04-10 23:59:59';
    public function __construct()
    {
        $this->name = 'pscsession';
        $this->version = '1.0.1';
        $this->author = 'PSConfigurator';
        $this->tab = 'administration';

        parent::__construct();

        $this->displayName = 'PSC Session';
        $this->description = 'Manage session duration for pop-up demo shops';
        
    }

    public function install()
    {
        return parent::install() 
        && $this->registerHook('displayBackOfficeHeader') 
        && $this->registerHook('actionDispatcherBefore');
    }

    public function hookActionDispatcherBefore($params)
    {
        $now = date('Y-m-d H:i:s');

        // back management
        if ($this->context->employee) {
            $employee = $this->context->employee;

            // allow superadmin
            if ((int)$employee->id_profile === 1) {
                return;
            }

            // if session expired
            if ($now > self::END_DATE && !Tools::getValue('expired')) {
                if ($employee && $employee->isLoggedBack()) {

                    $employee->logout();

                    Tools::redirect(
                        $this->context->link->getModuleLink('pscsession', 'expired', ['expired' => true])
                    );
                }
            }

        // front management 
        } else {
            if ($now > self::END_DATE && !Tools::getValue('expired')) {
                Tools::redirect(
                    $this->context->link->getModuleLink('pscsession', 'expired', ['expired' => true])
                );
            }
        }

        $this->context->smarty->assign([
            'module_path' => $this->_path,
            'admin_path' => 'dashboard'
        ]);
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->context->employee->id_profile === 1) {
            return;
        }

        Media::addJsDef([
            'end_date' => date('c', strtotime(self::END_DATE)),
        ]);

        $this->context->controller->addJS($this->_path . 'views/js/countdown.js');
    }
}
