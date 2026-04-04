<?php

class PscsessionExpiredModuleFrontController extends ModuleFrontController
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

        $this->display_header = false;
        $this->display_footer = false;

        $this->setTemplate('module:pscsession/views/templates/front/expired.tpl');
    }
}