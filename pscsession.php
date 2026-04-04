
<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class pscsession extends Module
{
    const INIT_DATE = "2026-04-10T23:59:59";

    public function __construct()
    {
        $this->name = 'pscsession';
        $this->version = '1.1.0';
        $this->author = 'PSConfigurator';
        $this->tab = 'administration';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'PSC Session';
        $this->description = 'Manage session duration for pop-up demo shops';
        
    }

    public function install()
    {
        return parent::install() 
        && $this->registerHook('displayBackOfficeHeader') 
        && $this->registerHook('actionDispatcherBefore')
        && Configuration::updateValue('PSC_SESSION_END_DATE', $this::INIT_DATE); // Valeur par défaut
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitform')) {
            // Récupérer la valeur du champ date (end_date)
            $endDate = Tools::getValue('end_date');
            if ($endDate && strtotime($endDate)) {
                Configuration::updateValue('PSC_SESSION_END_DATE', $endDate);
                $output = $this->displayConfirmation($this->l('Settings updated successfully.'));
                $output .= $this->renderDateForm();
                return $output;
            } else {
                $output = $this->displayError($this->l('Invalid date format.'));
                $output .= $this->renderDateForm();
                return $output;
            }
        }

        // Afficher le formulaire de configuration
        return $this->renderDateForm();

    }

    public function hookActionDispatcherBefore($params)
    {
        $now = date('Y-m-d H:i:s');
        $endDate = Configuration::get('PSC_SESSION_END_DATE');

        $employee = $this->context->employee;

        // allow superadmin
        if ((int)$employee->id_profile === 1) {
            return;
        }

        // back management
        if ($this->context->employee) {
            // if session expired
            if ($now > $endDate && !Tools::getValue('expired')) {
                if ($employee && $employee->isLoggedBack()) {

                    $employee->logout();

                    Tools::redirect(
                        $this->context->link->getModuleLink('pscsession', 'expired', ['expired' => true])
                    );
                }
            }

        // front management 
        } else {
            if ($now > $endDate && !Tools::getValue('expired')) {
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
        $now = date('Y-m-d H:i:s');
        $endDate = Configuration::get('PSC_SESSION_END_DATE'); // Use configuration value

        if ($this->context->employee->id_profile === 1 && $now > $endDate) {
            $this->context->controller->addJS($this->_path . 'views/js/dumpbar.js');
            return;
        }

        // Define an init date if it do not exists
        if (!$endDate) {
            $endDate = $this::INIT_DATE;
        }

        // Check if date is valid (format 'Y-m-d H:i:s')
        $timestamp = strtotime($endDate);
        if ($timestamp === false) {
            $endDate = $this::INIT_DATE;
            return;
        }

        Media::addJsDef([
            'end_date' => date('c', $timestamp),
        ]);

        $this->context->controller->addJS($this->_path . 'views/js/countdown.js');
    }

    public function renderDateForm()
    {
        $helper = new HelperForm();
        
        $endDate = Configuration::get('PSC_SESSION_END_DATE');
        if (!$endDate) {
            $endDate = $this::INIT_DATE; // Default value if no date is defined
        }
        
        // Form initialisation
        $helper->show_form = true;
        $helper->module = $this;
        $helper->submit_action = 'submitform';
        $helper->currentIndex = AdminController::$currentIndex
             . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => [
                'end_date' => $endDate,
            ],
        ];

        // Fields definition
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Session Configuration'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'datetime',
                        'label' => $this->l('End Date'),
                        'name' => 'end_date',
                        'size' => 20,
                        'required' => true,
                        'desc' => $this->l('Select the end date for the session.'),
                        'value' => $endDate,  // Field initialisation
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        // Generate and return form
        return $helper->generateForm([$fieldsForm]);
    }
}
