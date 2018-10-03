
<?php


class StockProductModel
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

class BluxeProductModel
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

	public $id_category;
    public $id_manufacturer;

	public function __construct()
	{
		
	}

    public function from_json($json_object = "")
    {
        $decoded = json_decode($json_object, true);
        foreach($decoded as $key => $val) {
        	$key = strtolower($key);
            if(property_exists(__CLASS__, $key)) {
                $this->$key = $val;
            }
        }
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
        if(count($this->families) > 0)
        {
            foreach ($this->families as $key => $value)
            {
                if($value)
                {
                    $category = Db::getInstance()->executeS('select `id_category` from `'._DB_PREFIX_.'category_lang` where `name` = "'.$value.'" limit 1');
                    if(!$category)
                    {
                        //add category
                        $category = new Category(null, 1, 1);
                        $category->name = $value;
                        $category->id_shop_default = 1;
                        $category->active = true;
                        $category->is_root_category = true;
                        //$category->position = Db::getInstance()->executeS('select max(`position`) from `'._DB_PREFIX_.'category`');
                        $category->link_rewrite = HttpUtiles::toAscii($value);
                        $category->add();
                    }
                    else
                        $this->id_category = $category[0]['id_category']; //primer valor devuelto
                }
            }
        }        
    }

    public function save_brand()
    {
        if($this->brandname)
        {
            $manufacturer = Manufacturer::getIdByName($this->brandname);
            if(!$manufacturer)
            {
                $manufacturer = new Manufacturer(null, 1, 1);
                $manufacturer->name = $this->brandname;
                $manufacturer->description = $this->brandname;
                $manufacturer->meta_title = $this->brandname;
                $manufacturer->active = true;
                $manufacturer->link_rewrite = HttpUtiles::toAscii($this->brandname);
                $manufacturer->add();
            }

            $manufacturer = Manufacturer::getIdByName($this->brandname);
            if($manufacturer)
            {
                $this->id_manufacturer = $manufacturer[0]['id_manufacturer'];
            }
        }
    }

    public function save()
    {
        if(!Product::existsInDatabase($this->id, 'product'))
        {
            $product = new Product($this->id, 1, 1);
            $product->id_category_default = $this->id_category;
            $product->id_tax_rules_group = 1;
            $product->name = $this->description;
            $product->on_sale = true;
            $product->online_only = 0;
            if(count($this->eans) > 0)
            {
                $product->ean13 = $this->eans[0];
            }
            $product->price = $this->price;
            $product->width = $this->ancho;
            $product->height = $this->alto;
            $product->depth = $this->fondo;
            $product->active = 1;
            $product->cache_is_pack = false;
            $product->show_condition = 0;
            $product->condition = 'new';
            $product->show_price = 1;
            $product->indexed = 1;
            $product->cache_has_attachments = 0;
            $product->is_virtual = 0;
            $product->pack_stock_type = 3;
            $product->state = 1;
            $product->available_date = date('Y-m-d H:i:s');
            $product->id_manufacturer = $this->id_manufacturer;
            //die(json_encode($product));
            $product->link_rewrite = HttpUtiles::toAscii($product->name);
            //stock configs
            $product->out_of_stock = $this->stock;
            $product->quantity = $this->stock;


            $product->add();
        }
    }
}