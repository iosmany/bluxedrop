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

    public function __construct($id_product = null, $full = true, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_product, $full, $id_lang, $id_shop, null);
        $this->langId = $id_lang;
        $this->shopId = $id_shop;

        //die(json_encode($this));
    }

    public function _save(ProductModel $modelo = null){
        if($modelo == null)
            return false;

        $categories_ids = $modelo->save_category($this->langId, $this->shopId);
        $manufacturer_id = $modelo->save_brand($this->langId, $this->shopId);

        $product_id = Product::getIdByEan13($modelo->eans[0]);

        $product = new Product($product_id, $this->langId, $this->shopId);
        if(!$product_id){
            $product->id = $modelo->id;
            $product->name = $modelo->description;
            $product->description = $modelo->setcontent;
            $product->on_sale = true;
            if(count($modelo->eans) > 0){
                $product->ean13 = $modelo->eans[0];
            }
            $product->width = $modelo->ancho;
            $product->height = $modelo->alto;
            $product->depth = $modelo->fondo;
            $product->active = 1;
            $product->show_price = 1;
            $product->is_virtual = 0;
            $product->state = 1;
            $product->indexed = 1;
            $product->id_category_default = $categories_ids[1];

            $product->price = $modelo->price;
            $product->unit_price = $modelo->pvr;
            $product->unit_price_ratio = $modelo->pvr;

            $product->available_date = date('Y-m-d H:i:s');
            $product->link_rewrite = Tools::link_rewrite($this->name);
            $product->id_manufacturer = $manufacturer_id;
            $product->quantity = $modelo->stock;
            //die(json_encode($this));
            $product->supplier_name = "Beauty Luxe Distributions";
            $product->add();

            //product->addAttribute();
            StockAvailable::setQuantity($product->id, 0, $modelo->stock, $this->id_shop);
        }
        else
            StockAvailable::setQuantity($product_id, 0, $modelo->stock, $this->id_shop);

        if(!empty($categories_ids)){
            //die(json_encode($categories_ids));
            $product->addToCategories($categories_ids);
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
            $root = Category::getRootCategory($langId);

            $inicio_id = $root ? $root->id : 0;
            if($inicio_id){
                $categories_ids[] = (int)$inicio_id;
            }

            //die('Sin padre: '.json_encode($withoutparent));
            foreach ($this->families as $name){
                //insert category
                $category_id = CategoryExtensiones::getIdByName($name);
                if(!$category_id){

                    $category = new Category(null, $langId, $idShop);
                    $category->name = $name;
                    $category->description = $name;
                    $category->id_shop_default = 1;
                    $category->active = true;
                    $category->link_rewrite = Tools::link_rewrite($name);
                    $category->id_parent =$inicio_id;

                    $category->add();
                    $categories_ids[] = (int)$category->id;
                }
                else
                    $categories_ids[] = (int)$category_id;
            }
        }

        //gender
        if(isset($this->gender)){
            $category_id = CategoryExtensiones::getIdByName($this->gender);
            if(!$category_id){
                $category = new Category(null, $langId, $idShop);
                $category->name = $this->gender;
                $category->description = $this->gender;
                $category->id_shop_default = 1;
                $category->active = true;
                $category->link_rewrite = Tools::link_rewrite($this->gender);
                $category->id_parent = $inicio_id;
                $category->add();
                $categories_ids[] = (int)$category->id;
            }
            else
                $categories_ids[] = (int)$category_id;
        }

        //die(json_encode($categories_ids));
        return $categories_ids;
    }

    public function save_brand($langId = null, $idShop = null)
    {
        //die(json_encode($this));
        $brand_id = Manufacturer::getIdByName($this->brandname);
        if(!$brand_id){
            $manufacturer = new Manufacturer(null,$langId);
            $manufacturer->id = $this->brandid;
            $manufacturer->name = $this->brandname;
            $manufacturer->description = $this->brandname;
            $manufacturer->active = true;
            $manufacturer->id_shop_list[] = $idShop;
            $manufacturer->add();
            $brand_id = $manufacturer->id;
        }
        return $brand_id;
    }
}