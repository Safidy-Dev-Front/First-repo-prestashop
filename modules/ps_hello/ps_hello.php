<?php
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    class Ps_hello extends Module{
        public function __construct(){
            $this->name = 'Ps_hello';
            $this->tab = 'front_office_features';
            $this->version = '1.0.0';
            $this->author = 'Safidy Ny aina';
            $this->need_instance = 0;
            $this->ps_versions_compliancy = array('min'=>'1.6', 'max'=>_PS_VERSION_);
            $this->bootstrap = true;
            parent::__construct();
            $this->displayName = $this->l('Hello');
            $this->description = $this->l('Test module');

            $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
            if (!Configuration::get('MYMODULE_NAME')) {
                $this->warning = $this->l('No name provided');
           }
        }
        public function install(){
            if (Shop::isFeatureActive()) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
            if (!parent::install() ||
                !$this->registerHook('leftColumn') ||
                !$this->registerHook('actionFrontControllerSetMedia') ||
                !Configuration::updateValue('MYMODULE_NAME', 'my friend')
            ){
             return false;
            }
            return true;
        }
        public function uninstall(){
            if (!parent::uninstall() ||
                !Configuration::deleteByName('MYMODULE_NAME')
            ) {
                return false;
            }
            return true;
        }
        public function hookDisplayLeftColumn(){
            $this->context->smarty->assign([
                'module_hello_world' => Configuration::get('MYMODULE_NAME'),
                'module_link' => $this->context->link->getModuleLink('ps_hello', 'display')
            ]);
            return $this->display(__FILE__, 'ps_hello.tpl');
        }
        //hook d'action ::
        public function hookActionFrontControllerSetMedia(){
            $this->context->controller->registerStylesheet('ps_hello_style', $this->_path.'views/css/ps_hello.css',
            [
                'media' => 'all',
                'priority' => 1000
            ]
            );
        }
        /**
         * Configuration Module
         */
        public function getContent(){
            $output = null;
            if(Tools::isSubmit('submit'.$this->name)){
                $ps_hello = strval(Tools::getValue('MYMODULE_NAME'));
                if(!$ps_hello || empty($ps_hello) || !Validate::isGenericName($ps_hello)){
                    $output.= $this->displayError($this->l('Invalid Configuration value'));

                }else{
                    Configuration::updateValue('MYMODULE_NAME', $ps_hello);
                    $output .= $this->displayConfirmation($this->l('Settings updated'));
                }
            }
            return $output.$this->renderForm();
        }
        /**
         * Create function renderForm::
         */
        public function renderForm(){
            $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
            /**
             * create form
            */
            $fieldsForm[0]['form'] =[
                'legend'=>[
                    'title'=>$this->l('Parametre Test')
                ],
                    'input'=>[
                            [
                        'type'=>'text',
                        'label'=>$this->l('Configuration ps_hello'),
                        'name'=>'MYMODULE_NAME',
                        'size' => 20,
                        'required'=>true
                        ]
                    ],
                    'submit'=>[
                        'title'=> $this->l('save'),
                        'class'=> 'btn btn-default pull-right'
                    ]
            ];
            $helper = new HelperForm();
            $helper->module = $this;
            $helper->name_controller = $this->name;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

            // Language
            $helper->default_form_language = $defaultLang;
            $helper->allow_employee_form_lang = $defaultLang;

            // Title and toolbar
            $helper->title = $this->displayName;
            $helper->show_toolbar = true;        // false -> remove toolbar
            $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
            $helper->submit_action = 'submit'.$this->name;
            $helper->toolbar_btn = [
                'save' => [
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                ],
                'back' => [
                    'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                    'desc' => $this->l('Back to list')
                ]
            ];

            // Load current value
            $helper->fields_value['MYMODULE_NAME'] = Tools::getValue('MYMODULE_NAME', Configuration::get('MYMODULE_NAME'));

            return $helper->generateForm($fieldsForm);
        }
    }
