<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 22:49
 */

class CategoryExtensiones extends Category
{
    public function __construct($id_category = null, $idlang = 1, $idshop = 1, $name = "", $isrootcategory = false){

        if(!isset($id_category) && $name != ""){
            $id_category = (int)CategoryExtensiones::getIdByName($name);
        }

        $this->id_category = $id_category;
        $this->name = $name;
        $this->id_shop_default = 1;
        $this->active = true;
        $this->is_root_category = $isrootcategory;
        $this->link_rewrite = HttpUtiles::toAscii($name);

        parent::__construct($id_category, $idlang, $idshop);
    }

    public function _save(){
        if(!Validate::isLoadedObject($this)){
            //die(json_encode($this));
            $this->add();
        }
    }

    public static function getIdByName($name = ""){

        $query = new DbQuery();
        $query->from('category_lang');
        $query->where('`name` = "'.$name.'"');
        $query->select('id_category');
        $result = Db::getInstance()->executeS($query->build());
        if(!$result)
            return $result;
        return $result[0]['id_category'];
    }
}