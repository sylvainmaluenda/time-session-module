
<?php
if (!defined('_PS_VERSION_')) {
    exit();
}

require_once __DIR__ . '/core/ServiceContainer.php';
require_once __DIR__ . '/infrastructure/ViteAssetManager.php';

use Pscsession\Core\ServiceContainer;
use Pscsession\Infrastructure\ViteAssetManager;

class pscsession extends Module
{
    const INIT_DATE = '2026-04-10T23:59:59';

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
        return parent::install() &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayBackOfficeTop') &&
            $this->registerHook('actionDispatcherBefore') &&
            Configuration::updateValue('PSC_SESSION_END_DATE', $this::INIT_DATE); // Valeur par défaut
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
        if ($employee && (int) $employee->id_profile === 1) {
            return;
        }
        // back management
        if ($this->context->employee) {
            // if session expired
            if ($now > $endDate && !Tools::getValue('expired')) {
                if ($employee && $employee->isLoggedBack()) {
                    $employee->logout();

                    Tools::redirect(
                        $this->context->link->getModuleLink('pscsession', 'sessionexpired', ['expired' => true]),
                    );
                }
            }

            // front management
        } else {
            if ($now > $endDate && !Tools::getValue('expired')) {
                Tools::redirect(
                    $this->context->link->getModuleLink('pscsession', 'sessionexpired', ['expired' => true]),
                );
            }
        }

        $this->context->smarty->assign([
            'module_path' => $this->_path,
            'admin_path' => 'dashboard',
        ]);
    }

    public function hookDisplayBackOfficeHeader()
    {
        $vite = $this->container()->get(ViteAssetManager::class);
        return $vite->render('src/sessionWidget/main.tsx');
    }

    public function hookDisplayBackOfficeTop()
    {
        $endDate = Configuration::get('PSC_SESSION_END_DATE');

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

        $this->context->smarty->assign([
            'module_dir' => __PS_BASE_URI__ . 'modules/' . $this->name . '/',
            'reactProps' => [
                'endDate' => $endDate,
            ],
        ]);

        // Vite Entry without tpl
        $props = htmlspecialchars(
            json_encode([
                'endDate' => $endDate,
            ]),
            ENT_QUOTES,
            'UTF-8',
        );

        return <<<HTML
        <div id="session-widget-root" data-props="{$props}"></div>
        HTML;

        // Vite Entry with tpl
        /*
        return $this->display(__FILE__, 'views/templates/back/SessionWidget.tpl');
        */
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
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
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
                        'value' => $endDate, // Field initialisation
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

    private ?ServiceContainer $container = null;

    public function container(): ServiceContainer
    {
        if ($this->container === null) {
            $this->container = $this->buildContainer();
        }

        return $this->container;
    }

    private function buildContainer(): ServiceContainer
    {
        $container = new ServiceContainer();

        $container->set(
            ViteAssetManager::class,
            fn(ServiceContainer $container) => new ViteAssetManager(
                _PS_MODULE_DIR_ . $this->name,
                __PS_BASE_URI__ . 'modules/' . $this->name . '/',
            ),
        );

        return $container;
    }
}

