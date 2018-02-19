<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class shmoPinterestBoardWidget extends Module
{

    protected $errors = [];

    protected $config = [
        'SHMO_PINTEREST_BOARD_WIDGET' => '',
        'SHMO_PINTEREST_BOARD_WIDGET_URL' => '',
        'SHMO_PINTEREST_BOARD_WIDGET_BOARD_WIDTH' => '',
        'SHMO_PINTEREST_BOARD_WIDGET_SCALE_HEIGHT' => '',
        'SHMO_PINTEREST_BOARD_WIDGET_SCALE_WIDTH' => '',

    ];

    public function __construct() {
        $this->name = 'shmopinterestboardwidget';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Andre Matthies';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Pinterest Board Widget');
        $this->description = $this->l('Adds a block with Pinterest Board Widget.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete Pinterest Board Widget?');
    }

    public function install() {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()
            OR !$this->installConfig()
            OR !$this->registerHook('displayHeader')
            OR !$this->registerHook('displayTop')
            OR !$this->registerHook('displayHome')
            OR !$this->registerHook('displayLeftColumn')
            OR !$this->registerHook('displayRightColumn')
            OR !$this->registerHook('displayFooter')
            OR !$this->registerHook('backOfficeHeader')) {
            return false;
        }
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall()
            OR !$this->removeConfig()) {
            return false;
        }
        return true;
    }

    private function installConfig() {
        foreach ($this->config as $keyname => $value) {
            Configuration::updateValue(strtoupper($keyname), $value);
        }
        return true;
    }

    private function removeConfig() {
        foreach ($this->config as $keyname => $value) {
            Configuration::deleteByName(strtoupper($keyname));
        }
        return true;
    }

    public function getConfig() {
        $config_keys = array_keys($this->config);
        return Configuration::getMultiple($config_keys);
    }

    public function getContent() {
        $output = null;
        if (Tools::isSubmit('submitshmopinterestboardwidget')) {
            foreach (Tools::getValue('config') as $key => $value) {
                Configuration::updateValue($key, $value);
            }
            if ($this->errors) {
                $output .= $this->displayError(implode($this->errors, '<br/>'));
            }
            else {
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        $vars = [];
        $vars['config'] = $this->getConfig();
        return $output . $this->displayForm($vars);
    }

    public function displayForm($vars) {
        extract($vars);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $twtfdForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'name' => 'config[SHMO_PINTEREST_BOARD_WIDGET]',
                    'label' => $this->l('Enable Board Widget?'),
                    'hint' => $this->l('When one Pin isn’t enough, add an entire board (up to 50 Pins) to your site.'),
                    'is_bool' => true,
                    'required' => false,
                    'values' => [
                        [
                            'id' => 'board_widget_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'board_widget_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ]
                    ]
                ],
                [
                    'type' => 'text',
                    'name' => 'config[SHMO_PINTEREST_BOARD_WIDGET_URL]',
                    'label' => $this->l('Board Profile URL'),
                    'hint' => 'e.g. https://www.pinterest.com/pinterest/official-news/',
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'name' => 'config[SHMO_PINTEREST_BOARD_WIDGET_BOARD_WIDTH]',
                    'label' => $this->l('Board width inside the widget.'),
                    'hint' => $this->l('This width does not include the white border on either side.'),
                    'desc' => 'Minimum width of 130px. Default: Fill width of parent.',
                    'suffix' => 'px',
                    'required' => false
                ],
                [
                    'type' => 'text',
                    'name' => 'config[SHMO_PINTEREST_BOARD_WIDGET_SCALE_HEIGHT]',
                    'label' => $this->l('Board height inside the widget.'),
                    'hint' => $this->l('This does not include the white border above and below.'),
                    'desc' => 'Minimum height of 60px. Default: 175px.',
                    'suffix' => 'px',
                    'required' => false
                ],
                [
                    'type' => 'text',
                    'name' => 'config[SHMO_PINTEREST_BOARD_WIDGET_SCALE_WIDTH]',
                    'label' => $this->l('Width of the Pin thumbnails within the widget.'),
                    'desc' => 'Minimum width of 60px. Default: 92px',
                    'suffix' => 'px',
                    'required' => false
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' =>
                [
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];
        $helper->fields_value['config[SHMO_PINTEREST_BOARD_WIDGET]'] = Configuration::get('SHMO_PINTEREST_BOARD_WIDGET');
        $helper->fields_value['config[SHMO_PINTEREST_BOARD_WIDGET_URL]'] = Configuration::get('SHMO_PINTEREST_BOARD_WIDGET_URL');
        $helper->fields_value['config[SHMO_PINTEREST_BOARD_WIDGET_BOARD_WIDTH]'] = Configuration::get('SHMO_PINTEREST_BOARD_WIDGET_BOARD_WIDTH');
        $helper->fields_value['config[SHMO_PINTEREST_BOARD_WIDGET_SCALE_HEIGHT]'] = Configuration::get('SHMO_PINTEREST_BOARD_WIDGET_SCALE_HEIGHT');
        $helper->fields_value['config[SHMO_PINTEREST_BOARD_WIDGET_SCALE_WIDTH]'] = Configuration::get('SHMO_PINTEREST_BOARD_WIDGET_SCALE_WIDTH');

        return $helper->generateForm($twtfdForm);
    }

    public function hookDisplayLeftColumn() {
        $config = $this->getConfig();
        $this->context->smarty->assign([
            'shmoPntrstBrdWdgt' => $config
        ]);
        if (Configuration::get('SHMO_PINTEREST_BOARD_WIDGET')) {
            $this->context->controller->addJS('//assets.pinterest.com/js/pinit.js');
        }
        return $this->display(__FILE__, 'shmopinterestboardwidget.tpl');
    }

    public function hookDisplayRightColumn($params) {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayTop($params) {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayHome($params) {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayFooter($params) {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookBackOfficeHeader() {
        $this->context->controller->addJS('/js/jquery/plugins/jquery.validate.js');
        $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/js/shmopinterestboardwidget.js');
    }

}