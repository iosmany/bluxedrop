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
    protected $table_name = 'bluxedrop_access';
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

	    if (!parent::install() || !$this->installsql() ||
	        !$this->registerHook('leftColumn') ||
	        !$this->registerHook('header') ||
	        !Configuration::updateValue('BLUXEDROP_NAME', 'Bluxe Dropshiping'))
	    {
	        return false;
	    }
	    return true;
	}

	public function uninstall()
	{
	    if (!$this->uninstallsql() || !parent::uninstall() || !Configuration::deleteByName('BLUXEDROP_NAME'))
	    {
	        return false;
	    }
	    return true;
	}

	public function installsql()
	{
        $sql = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_.$this->table_name."` (
              `user` varchar(50) NOT NULL PRIMARY KEY,
              `password` varchar(20) NOT NULL,
              `token` varchar(250) NULL,
              `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `date_upd` datetime NULL
            ) ENGINE = "._MYSQL_ENGINE_;

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
        return true;
	}

    public function uninstallsql()
    {
        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_.$this->table_name."`";
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
        return true;
    }

	/**
     * @description Make HTTP-POST call
     * @param       $url
     * @param       array $params
     * @return      HTTP-Response body or an empty string if the request fails or is empty
     */
    public function http_post_request($url, array $params) {
        $query = http_build_query($params);
        $ch    = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $response = curl_exec($ch); //json result expected
        curl_close($ch);
        return $response;
    }

    /**
     * Chequea si existe un registro de credenciales
     * @param null $user
     * @return array|false
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function check_registry($user = null)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->limit('1');
        $query->from($this->table_name);
        if(isset($user))
        {
            $query->where('`user` = "'.$user.'"');
        }
        return Db::getInstance()->executeS($query->build());
    }

	public function getContent()
	{
	    $output = null;
	    $acces_data = false;

	    if (Tools::isSubmit('submit'.$this->name)) {

	     	$user = Tools::getValue('BLUXEDROP_USER');
	     	$password = Tools::getValue('BLUXEDROP_PASS');

	     	if(Validate::isString($user) && Validate::isString($password))
	     	{
	     		$http_json_result = $this->http_post_request('http://drop.novaengel.com/api/login', array('user' => $user, 'password' => $password ));
	     		if($http_json_result)
	     		{
	     		    //decode http result
					$http_array_result = json_decode($http_json_result, true);
					//existe un registro en Db?
					$existe_registro = $this->check_registry($user);
                    //obtener token de la respuesta api
                    $bltoken = $http_array_result['Token'];
                    //check if exist and validate result access-token
                    if($existe_registro && isset($bltoken) && Validate::isString($bltoken))
                    {
                        $acces_data = new BluxeAccess($user);
                        $acces_data->user = $user;
                        $acces_data->password = $password;
                        $acces_data->user = $bltoken;
                        $acces_data->update();
                        $output .= $this->displayConfirmation($this->l('Access credentials updated successfuly'));
                    }
                    else if(isset($bltoken) && Validate::isString($bltoken))
                    {
                        $bltoken = $http_array_result['Token'];
                        $acces_data = new BluxeAccess($user);
                        $acces_data->user = $user;
                        $acces_data->password = $password;
                        $acces_data->token = $bltoken;
                        $acces_data->add();
                        $output .= $this->displayConfirmation($this->l('Access credentials stored successfuly'));
                    }
	     		}
	     		else
                    $output .= $this->displayError($this->l('Please, check for data access in. Don´t match. Upsssss!!'));
	     	}
	     	else
     		{
     			 $output .= $this->displayError($this->l('Invalid Configuration value'));
     		}
	    }

	    if(!$acces_data)
        {
            $acces_data = new BluxeAccess();
            $credentials = $this->check_registry();
            if($credentials)
            {
                $acces_data->user = $credentials[0]['user'];
                $acces_data->password = $credentials[0]['password'];
                $acces_data->token = $credentials[0]['token'];
                $output .= $this->displayConfirmation($this->l('Access credentials loaded'));
            }
            else
            {
                $output .= $this->displayWarning($this->l('Access credentials are needed'));
            }
        }
        $append = $this->display(__FILE__, '\views\templates\admin\admin.tpl');
	    return $output.$this->displayForm($acces_data).$append;
	}

	public function displayForm(BluxeAccess $accessdata)
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
                    'label' => $this->l('User'),
                    'name' => 'BLUXEDROP_USER',
                    'size' => 50,
                    'required' => true
                ],
                [
                    'type' => 'password',
                    'label' => $this->l('Password'),
                    'name' => 'BLUXEDROP_PASS',
                    'size' => 20,
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Authorization'),
                    'name' => 'BLUXEDROP_TOKEN',
                    'size' => 100,
                    'disabled' => true
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
        $helper->fields_value['BLUXEDROP_USER'] = $accessdata->user;
        $helper->fields_value['BLUXEDROP_PASS'] = $accessdata->password;
        $helper->fields_value['BLUXEDROP_TOKEN'] = $accessdata->token;

        return $helper->generateForm($fieldsForm);
	}
}


