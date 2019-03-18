<?php if (!defined('_PS_VERSION_')) exit;

/**
 * Class BackendHelperForm
 */
class BackendHelperForm extends HelperForm
{
    /**
     * BackendHelperForm constructor.
     * @param $name
     */
    public function __construct($name) {
        parent::__construct();

        $default_lang = Configuration::get('PS_LANG_DEFAULT');

        $this->module = $this;

        $this->name_controller = $name;

        $this->token = Tools::getAdminTokenLite('AdminModules');

        $this->currentIndex = AdminController::$currentIndex . '&configure=' . $name;

        $this->default_form_language = $default_lang;

        $this->allow_employee_form_lang = $default_lang;

        $this->title = $this->displayName;

        $this->show_toolbar = true;

        $this->toolbar_scroll = true;

        $this->submit_action = 'submit' . $name;

        $this->toolbar_btn = [
            'save' =>
                [
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $name . '&save' . $name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        $this->fields_value = [
            'config[PINTEREST_BOARD_WIDGET]' => Configuration::get('PINTEREST_BOARD_WIDGET'),
            'config[PINTEREST_BOARD_WIDGET_URL]' => Configuration::get('PINTEREST_BOARD_WIDGET_URL'),
            'config[PINTEREST_BOARD_WIDGET_BOARD_WIDTH]' => Configuration::get('PINTEREST_BOARD_WIDGET_BOARD_WIDTH'),
            'config[PINTEREST_BOARD_WIDGET_SCALE_HEIGHT]' => Configuration::get('PINTEREST_BOARD_WIDGET_SCALE_HEIGHT'),
            'config[PINTEREST_BOARD_WIDGET_SCALE_WIDTH]' => Configuration::get('PINTEREST_BOARD_WIDGET_SCALE_WIDTH')
        ];

        $this->fields_form = [['form' => [
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
        ]]];
    }
}