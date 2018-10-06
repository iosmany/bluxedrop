<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 22:51
 */

include 'ModelExtensiones.php';

class ProductExtensiones extends Product
{
    public $langId;
    public $shopId;

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null)
    {
        $this->langId = $id_lang;
        $this->shopId = $id_shop;

        parent::__construct($id_product, $full, $id_lang, $id_shop, null);
    }

    public function _save(ProductModel $modelo = null, array $products_ids_indb = []){
        if($modelo == null)
            return false;

        $categories_ids = $modelo->save_category($this->langId, $this->shopId);
        $manufacturer = $modelo->save_brand($this->langId, $this->shopId);

        //$result = in_array($modelo->id, $products_ids_indb);
        //die(isset($this->id).', '.isset($result).', '.$modelo->id);

        if(!in_array($modelo->id, $products_ids_indb)){

            $this->id = $modelo->id;
            $this->name = $modelo->description;
            $this->description = $modelo->setcontent;
            $this->on_sale = true;
            if(count($modelo->eans) > 0){
                $this->ean13 = $modelo->eans[0];
            }
            $this->width = $modelo->ancho;
            $this->height = $modelo->alto;
            $this->depth = $modelo->fondo;
            $this->active = 1;
            $this->show_price = 1;
            $this->is_virtual = 0;
            $this->state = 1;
            $this->indexed = 1;
            $this->available_date = date('Y-m-d H:i:s');
            $this->link_rewrite = Tools::link_rewrite($this->name);
            $this->id_manufacturer = $manufacturer->id;

            die('Add: '.json_encode($this));
            $this->add();
        }

        $this->quantity = $modelo->stock;
        die("Upd: ".json_encode($this));
        $this->update();


        if(!empty($categories_ids)){
            $this->addToCategories($categories_ids);
        }

        $this->save_stock();
    }

    public function save_stock(){
        $id_stock = StockAvailable::getStockAvailableIdByProductId($this->id);
        if($id_stock){
            //die(json_encode($available));
            $stock = new StockExtensiones($id_stock, $this->langId, $this->shopId);

            $stock->id_product = $this->id;
            $stock->quantity = $this->quantity;

            if(!Validate::isLoadedObject($stock)){
                //die('Add: '.json_encode($stock));
                $stock->add();
            }
            else{
                $stock->update();
            }
            die(json_encode($stock));
        }
    }
}

class ProductModel extends ModelExtensiones
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

    public function __construct(){
    }

    public function from_json($json_object = "", $class = __CLASS__){
        parent::from_json($json_object, $class);
    }

    public function from_array(array $array_data = null, $class = __CLASS__){
        parent::from_array($array_data, $class);
    }

    public function save_category($langId = null, $idShop = null){
        $categories_ids = array();
        if(!empty($this->families)){
            $withoutparent = Category::getCategoriesWithoutParent();

            //die('Sin padre: '.json_encode($withoutparent));

            foreach ($this->families as $name){
                //insert category
                $category = new CategoryExtensiones(null, $langId, $idShop, $name, false );
                $category->id_parent = empty($withoutparent) ? 0 : $withoutparent[0]['id_category'];
                $category->_save();
                $categories_ids[] = $category->id;
            }
        }
        return $categories_ids;
    }

    public function save_brand($langId = null, $idShop = null)
    {
        //die(json_encode($this));
        $manufacturer = new ManufacturerExtensiones(null, $langId, $this->brandname, $idShop);
        $manufacturer->_save();
        return $manufacturer;
    }
}