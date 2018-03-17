<?php if (!defined('_PS_VERSION_')) exit;

/**
 * Class PinterestBoardWidget
 */
class PinterestBoardWidget extends Module
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $config = [
        'PINTEREST_BOARD_WIDGET' => '',
        'PINTEREST_BOARD_WIDGET_URL' => '',
        'PINTEREST_BOARD_WIDGET_BOARD_WIDTH' => '',
        'PINTEREST_BOARD_WIDGET_SCALE_HEIGHT' => '',
        'PINTEREST_BOARD_WIDGET_SCALE_WIDTH' => '',

    ];

    /**
     * PinterestBoardWidget constructor.
     */
    public function __construct() {
        $this->name = 'pinterestboardwidget';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'Andre Matthies';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Pinterest Board Widget');
        $this->description = $this->l('Adds a block with Pinterest Board Widget.');
    }

    /**
     * @return bool
     */
    public function install() {
        if (Shop::isFeatureActive()) Shop::setContext(Shop::CONTEXT_ALL);
        return parent::install()
            && $this->installConfig()
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('displayFooter');
    }

    /**
     * @return bool
     */
    public function uninstall() {
        return parent::uninstall()
            && $this->removeConfig();
    }

    /**
     * @return bool
     */
    private function installConfig() {
        foreach ($this->config as $k => $v) Configuration::updateValue($k, $v);
        return true;
    }

    /**
     * @return bool
     */
    private function removeConfig() {
        foreach ($this->config as $k => $v) Configuration::deleteByName($k);
        return true;
    }

    /**
     * @return mixed
     */
    public function getConfig() {
        return Configuration::getMultiple(array_keys($this->config));
    }

    /**
     * @return string
     */
    public function getContent() {
        $output = null;
        if (Tools::isSubmit('submitpinterestboardwidget')) {
            foreach (Tools::getValue('config') as $key => $value) Configuration::updateValue($key, $value);
            if ($this->errors) $output .= $this->displayError(implode($this->errors, '<br/>'));
            else $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output . $this->displayForm();
    }

    /**
     * @return mixed
     */
    public function displayForm() {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
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
        $helper->fields_value['config[PINTEREST_BOARD_WIDGET]'] = Configuration::get('PINTEREST_BOARD_WIDGET');
        $helper->fields_value['config[PINTEREST_BOARD_WIDGET_URL]'] = Configuration::get('PINTEREST_BOARD_WIDGET_URL');
        $helper->fields_value['config[PINTEREST_BOARD_WIDGET_BOARD_WIDTH]'] = Configuration::get('PINTEREST_BOARD_WIDGET_BOARD_WIDTH');
        $helper->fields_value['config[PINTEREST_BOARD_WIDGET_SCALE_HEIGHT]'] = Configuration::get('PINTEREST_BOARD_WIDGET_SCALE_HEIGHT');
        $helper->fields_value['config[PINTEREST_BOARD_WIDGET_SCALE_WIDTH]'] = Configuration::get('PINTEREST_BOARD_WIDGET_SCALE_WIDTH');

        return $helper->generateForm([ [ 'form' => [
            'legend' => [
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'name' => 'config[PINTEREST_BOARD_WIDGET]',
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
                    'name' => 'config[PINTEREST_BOARD_WIDGET_URL]',
                    'label' => $this->l('Board Profile URL'),
                    'hint' => 'e.g. https://www.pinterest.com/pinterest/official-news/',
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'name' => 'config[PINTEREST_BOARD_WIDGET_BOARD_WIDTH]',
                    'label' => $this->l('Board width inside the widget.'),
                    'hint' => $this->l('This width does not include the white border on either side.'),
                    'desc' => 'Minimum width of 130px. Default: Fill width of parent.',
                    'suffix' => 'px',
                    'required' => false
                ],
                [
                    'type' => 'text',
                    'name' => 'config[PINTEREST_BOARD_WIDGET_SCALE_HEIGHT]',
                    'label' => $this->l('Board height inside the widget.'),
                    'hint' => $this->l('This does not include the white border above and below.'),
                    'desc' => 'Minimum height of 60px. Default: 175px.',
                    'suffix' => 'px',
                    'required' => false
                ],
                [
                    'type' => 'text',
                    'name' => 'config[PINTEREST_BOARD_WIDGET_SCALE_WIDTH]',
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
        ] ] ]);
    }

    /**
     * @return mixed
     */
    public function hookDisplayFooter() {
        $this->context->smarty->assign($this->getConfig());
        if (Configuration::get('PINTEREST_BOARD_WIDGET')) $this->context->controller->addJS('//assets.pinterest.com/js/pinit.js');
        return $this->display(__FILE__, 'pinterestboardwidget.tpl');
    }

    /**
     * @return mixed
     */
    public function hookDisplayLeftColumn() {
        return $this->hookDisplayFooter();
    }

    /**
     * @return mixed
     */
    public function hookDisplayRightColumn() {
        return $this->hookDisplayFooter();
    }

    /**
     * @return mixed
     */
    public function hookDisplayTop() {
        return $this->hookDisplayFooter();
    }

    /**
     * @return mixed
     */
    public function hookDisplayHome() {
        return $this->hookDisplayFooter();
    }

    /**
     *
     */
    public function hookActionAdminControllerSetMedia() {
        $this->context->controller->addJqueryPlugin('validate');
        $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/backend.js');
    }
}