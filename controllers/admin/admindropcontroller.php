<?php
/**
*Creado por Iosmany Rodriguez
*01-10-2018
*/
//namespace bluxedrop\controller\admin;

class AdminDropController extends AdminController
{
	protected $split_count = 20;
    protected $url_base = 'http://drop.novaengel.com';
    
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function loadAction($access_token = "")
    {
        $lang = Tools::getValue('id_lang');
        $http_response = HttpUtiles::http_get_request($this->url_base, 'api/products/availables/'.$access_token.'/'.$lang);
        //http response json
        $data_array = json_decode($http_response, true); //array asociativo
        //analizar por partes
        foreach (array_chunk($data_array, $this->split_count) as $parte => $elementos)
        {
            foreach ($elementos as $index => $value)
            {
                $current = new BluxeProductModel();
                $current->from_array($value);
                $current->save();                
            }
        }
    }
}