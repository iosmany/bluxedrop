<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 23:02
 */

class ManufacturerExtensiones extends Manufacturer
{
    public function __construct($id = null, $idLang = null, $brandname = "")
    {
        parent::__construct($id, $idLang);

        $this->name = $brandname;
        $this->description = $this->brandname;
        $this->active = true;
    }

    public function _save()
    {
        $entity = Manufacturer::getByName($this->name);
        if(!$entity)
        {
            $this->add();
        }
        $this->id = $entity['id'];
    }
}