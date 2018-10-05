<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 23:02
 */

class ManufacturerExtensiones extends Manufacturer
{
    public function __construct($id = null, $idLang = null, $brandname = "", $idShop = null){
        if(!isset($id)){
            $id = (int)Manufacturer::getIdByName($brandname);
        }

        $this->name = $brandname;
        $this->description = brandname;
        $this->active = true;
        $this->id_shop_list[] = $idShop;

        parent::__construct($id, $idLang);
        //die('id: '.$id.' lang: '.$idLang.' shop: '.$idShop);
    }

    public function _save(){

        if(!Validate::isLoadedObject($this)){
            //die('Add: '.json_encode($this));
            $this->add();
        }
        //die(json_encode($this));
    }
}