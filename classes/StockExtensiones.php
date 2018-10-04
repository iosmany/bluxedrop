<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 22:50
 */

class StockModel
{
    public $id;
    public $stock;
    public $price;

    public function __construct()
    {
    }

    public function from_json($json_object = "")
    {
        $decoded = json_decode($json_object, true);
        $this->from_array($decoded);
    }

    public function from_array($array_data)
    {
        foreach($array_data as $key => $val) {
            $key = strtolower($key);
            if(property_exists(__CLASS__, $key)) {
                $this->$key = $val;
            }
        }
    }
}

class StockExtensiones extends StockAvailable
{
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
    }
}