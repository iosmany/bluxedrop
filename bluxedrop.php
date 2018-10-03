

<?php
/**
*Created by Iosmany Rdgz
*
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_PS_MODE_DEV_')) {
	exit;
}

include 'classes/product.php';
include 'classes/accessdata.php';


class Bluxedrop extends Module
{

	public function __construct()
	{
		$this->name = 'bluxedrop';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Iosmany Rdgz';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Beauty Luxe Dropshiping');
        $this->description = $this->l('El sistema DropShipping de Nova Engel ofrece a sus clientes la posibilidad de efectuar ventas al cliente final sin tener que preocuparse de tener un stock propio de productos. Nova Engel se encarga de enviar los productos necesarios al cliente final mediante un transportista serio y eficaz.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('BLUXEDROP_NAME')) {
            $this->warning = $this->l('No name provided');
        }
	}	

	/*
	/views/templates/front/: front office features.
	/views/templates/admin/: back office features.
	/views/templates/hook/: features hooked to a PrestaShop (so can be displayed either on the front office or the back office).
	*/

	//hooks
	public function hookDisplayLeftColumn($params)
	{
	    $this->context->smarty->assign([
	        'my_module_name' => Configuration::get('BLUXEDROP_NAME'),
	        'my_module_link' => $this->context->link->getModuleLink('bluxedrop', 'display'),
	        'my_module_message' => $this->l('This is a simple text message')
	      ]);

	      return $this->display(__FILE__, 'bluxedrop.tpl');
	}

	public function hookDisplayRightColumn($params)
	{
	    return $this->hookDisplayLeftColumn($params);
	}

	public function hookDisplayHeader()
	{
	    $this->context->controller->addCSS($this->_path.'css/bluxedrop.css', 'all');
	}

	public function hookDisplayHome()
	{
	    $this->context->smarty->assign([
	        'my_module_name' => Configuration::get('BLUXEDROP_NAME'),
	        'my_module_link' => $this->context->link->getModuleLink('bluxedrop', 'display'),
	        'my_module_message' => $this->l('This is a simple text message')
	      ]);

	      return $this->display(__FILE__, 'bluxedrop.tpl');
	}



	public function install()
	{
	    if (Shop::isFeatureActive()) {
	        Shop::setContext(Shop::CONTEXT_ALL);
	    }

	    if (!parent::install() ||
	        !$this->registerHook('leftColumn') ||
	        !$this->registerHook('header') ||
	        !Configuration::updateValue('BLUXEDROP_NAME', 'Bluxe Dropshiping')
	    ) 
	    {
	        return false;
	    }

	    return true;
	}

	public function uninstall()
	{
	    if (!parent::uninstall() ||
	        !Configuration::deleteByName('BLUXEDROP_NAME')
	    ) {
	        return false;
	    }

	    return true;
	}


	public function installsql()
	{
		Db::getInstance()->execute('CREATE TABLE IF NOT EXIST MyGuests (
											user VARCHAR(20) PRIMARY KEY,
											password VARCHAR(15) NOT NULL,
											token VARCHAR(100) NULL,
											date_add DATETIME NOT NULL,
											date_up DATETIME NULL)'
										);

	}
	/**
     * @description Make HTTP-POST call
     * @param       $url
     * @param       array $params
     * @return      HTTP-Response body or an empty string if the request fails or is empty
     */
    public function HTTPPost($url, array $params) {
        $query = http_build_query($params);
        $ch    = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

	public function getContent()
	{
	    $output = null;
	    $acces_data = new AccessDTO();

	    if (Tools::isSubmit('submit'.$this->name)) {
	        $myModuleName = strval(Tools::getValue('BLUXEDROP_NAME'));

	        if (
	            !$myModuleName ||
	            empty($myModuleName) ||
	            !Validate::isGenericName($myModuleName)
	        ) {
	            $output .= $this->displayError($this->l('Invalid Configuration value'));
	        } else {
	            Configuration::updateValue('BLUXEDROP_NAME', $myModuleName);
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }
	    if (Tools::isSubmit('submit'.$this->name.'access')) {

	     	$user = Tools::getValue('BLUXEDROP_USER');
	     	$pass = Tools::getValue('BLUXEDROP_PASS');

	     	if(Validate::isString($user) && Validate::isString($pass))
	     	{

	     		$result = $this->HTTPPost('http://drop.novaengel.com/api/login', array('user' => $user, 'password'=>$pass ));
	     		var_dump($result);

	     		if($result)
	     		{
					$container = json_decode($result, true);
					$acces_data->user = $user;
					$acces_data->password = $pass;
					$acces_data->token = $container['Token'];
	     		}
	     	}
	     	else
     		{
     			 $output .= $this->displayError($this->l('Invalid Configuration value'));
     		}
	    }

	    return $output.$this->displayForm().$this->displayDataApiAccessForm($acces_data);
	}

	public function displayForm()
	{
	    // Get default language
	    $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

	    // Init Fields form array
	    $fieldsForm[0]['form'] = [
	        'legend' => [
	            'title' => $this->l('Settings'),
	        ],
	        'input' => [
	            [
	                'type' => 'text',
	                'label' => $this->l('Configuration value'),
	                'name' => 'BLUXEDROP_NAME',
	                'size' => 20,
	                'required' => true
	            ]
	        ],
	        'submit' => [
	            'title' => $this->l('Save'),
	            'class' => 'btn btn-default pull-right'
	        ]
	    ];

	    $helper = new HelperForm();

	    // Module, token and currentIndex
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
	    $helper->fields_value['BLUXEDROP_NAME'] = Configuration::get('BLUXEDROP_NAME');

	    return $helper->generateForm($fieldsForm);
	}

	public function displayDataApiAccessForm($accessdata)
	{
	    // Get default language
	    $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

	    // Init Fields form array
	    $fieldsForm[0]['form'] = [
	        'legend' => [
	            'title' => $this->l('Acceso a Beauty Dropshiping'),
	        ],
	        'input' => [
	            [
	                'type' => 'text',
	                'label' => $this->l('Usuario'),
	                'name' => 'BLUXEDROP_USER',
	                'size' => 50,
	                'required' => true
	            ],
	            [
	                'type' => 'password',
	                'label' => $this->l('Contraseña'),
	                'name' => 'BLUXEDROP_PASS',
	                'size' => 20,
	                'required' => true
	            ],
	             [
	                'type' => 'text',
	                'label' => $this->l('Autorización'),
	                'name' => 'BLUXEDROP_TOKEN',
	                'size' => 100,
	                'disabled' => true
	            ]
	        ],
	        'submit' => [
	            'title' => $this->l('Guardar'),
	            'class' => 'btn btn-default pull-right'
	        ]
	    ];

	    $helper = new HelperForm();

	    // Module, token and currentIndex
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
	    $helper->submit_action = 'submit'.$this->name.'access';
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
	    $helper->fields_value['BLUXEDROP_USER'] = $accessdata->user;
	    $helper->fields_value['BLUXEDROP_PASS'] = $accessdata->password;
	    $helper->fields_value['BLUXEDROP_TOKEN'] = $accessdata->token;

	    return $helper->generateForm($fieldsForm);
	}
}


