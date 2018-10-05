<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 22:50
 */

class StockModel extends ModelExtensiones
{
    public $id;
    public $stock;
    public $price;

    public function __construct()
    {
    }

    public function from_json($json_object = "", $class = __CLASS__)
    {
        parent::from_json($json_object, $class);
    }

    public function from_array(array $array_data = null, $class = __CLASS__)
    {
        parent::from_array($array_data, $class);
    }
}

class StockExtensiones extends StockAvailable
{
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
    }
}