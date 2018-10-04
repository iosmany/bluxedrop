<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 22:49
 */

class CategoryExtensiones extends Category
{
    public function __construct($idcategory = null, $idlang = null, $idshop = 1, $name = "", $isrootcategory = false)
    {
        parent::__construct($idcategory, $idlang, $idshop);

        $this->name = $name;
        $this->id_shop_default = 1;
        $this->active = true;
        $this->is_root_category = $isrootcategory;
        $this->link_rewrite = HttpUtiles::toAscii($name);
    }

    public function _save()
    {
        $entity = CategoryExtensiones::getByName($this->name);
        if(!$entity)
        {
            $this->add();
        }
        $this->id = $entity['id'];
    }

    public static function getByName($name = "")
    {
        $query = new DbQuery();
        $query->from('category');
        $query->where('`name` = "'.$name.'"');
        $query->select('*');
        return Db::getInstance()->executeS($query->build());
    }
}