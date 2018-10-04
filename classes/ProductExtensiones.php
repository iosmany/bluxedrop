<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 22:51
 */

class ProductModel
{
    public $id;
    public $eans = array();
    public $description;
    public $setcontent;
    public $price;
    public $pvr;
    public $stock;
    public $brandid;
    public $brandname;
    public $gender;
    public $families = array();
    public $iva;
    public $kgs;
    public $alto;
    public $ancho;
    public $fondo;
    public $fecha;
    public $contenido;
    public $gama;

    public $categories_ids = array();

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

    public function save_category()
    {
        if(!empty($this->families))
        {
            foreach ($this->families as $name)
            {
                //insert category
                $category = new CategoryExtensiones(null, 1, 1, $name, true );
                $category->_save();
                $categories_ids[] = $category->id;
            }
        }
    }

    public function save_brand()
    {
        if($this->brandname && $this->brandid)
        {
            $manufacturer = new ManufacturerExtensiones($this->brandid, 1, $this->brandname);
            $manufacturer->_save();
        }
    }
}

class ProductExtensiones extends Product
{
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_product, $full, $id_lang, $id_shop, null);
    }

    public function _save(ProductModel $modelo = null)
    {
        if($modelo == null)
            return false;

        if(!Product::existsInDatabase($modelo->id, 'product'))
        {
            $this->id = 0;
            $this->name = $modelo->description;
            $this->on_sale = true;
            $this->online_only = 0;
            if(count($modelo->eans) > 0)
            {
                $this->ean13 = $modelo->eans[0];
            }
            $this->width = $modelo->ancho;
            $this->height = $modelo->alto;
            $this->depth = $modelo->fondo;
            $this->active = 1;
            $this->cache_is_pack = false;
            $this->show_condition = 0;
            $this->condition = 'new';
            $this->show_price = 1;
            $this->cache_has_attachments = 0;
            $this->is_virtual = 0;
            $this->pack_stock_type = 3;
            $this->state = 1;
            $this->available_date = date('Y-m-d H:i:s');
            //die(json_encode($product));
            $this->link_rewrite = Tools::link_rewrite($this->name);
            $this->quantity = $modelo->stock;
            $this->add();
        }

        $this->addToCategories($modelo->categories_ids);

    }
}