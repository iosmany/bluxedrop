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

    public function _save(ProductModel $modelo = null, $image_req = ""){
        if($modelo == null)
            return false;

        $categories_ids = $modelo->saveCategory($this->langId, $this->shopId);
        $manufacturer_id = $modelo->saveBrand($this->langId, $this->shopId);
        $attribute = $modelo->saveGama($this->langId, $this->shopId);

        $name = str_replace($modelo->contenido,'', $modelo->description);

        if(!Product::existsInDatabase($modelo->id)){
            $this->annadirProducto($modelo, $categories_ids, $manufacturer_id);
        }
        else
            StockAvailable::setQuantity($modelo->id, 0, $modelo->stock, $this->id_shop);
    }

    private function annadirProducto($modelo = null, array $categories_ids = [], $manufacturer_id = null)
    {
        $product = new Product(null, $this->langId, $this->shopId);
        $product->id = $modelo->id;
        $product->force_id = true;
        $product->name = $modelo->description;
        if(isset($modelo->setcontent)){
            $product->description = $modelo->setcontent;
        }
        else {
            $product->description = $modelo->description;
        }
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
        $product->available_date = date('Y-m-d H:i:s');
        $product->link_rewrite = Tools::link_rewrite($product->name);
        $product->id_manufacturer = $manufacturer_id;
        $product->quantity = $modelo->stock;

        //die(json_encode($product));
        $product->add();

        if(isset($image_req) && $image_req !== ""){
            $shops = array();
            $shops[] = $this->shopId;

            $img_http_response = HttpUtiles::http_get_request($image_req);
            $image = new Image(null, $this->langId);
            $image->id_product = (int)$product->id;
            $image->position = Image::getHighestPosition( $image->id_product) + 1;
            $image->cover = true; // or false;
            if (($image->validateFields(false, true)) === true &&
                ($image->validateFieldsLang(false, true)) === true && $image->add())
            {
                $image->associateTo($shops);
                /*if (!AdminImportControllerCore::copyImg( $image->id_product, $image->id, $img_http_response, 'products', false))
                {
                    $image->delete();
                }*/
            }
        }
        //die(json_encode($product));
        //product->addAttribute();
        StockAvailable::setQuantity($product->id, 0, $modelo->stock, $this->id_shop);

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

    public function fromJson($json_object = "", $class = __CLASS__){
        parent::fromJson($json_object, $class);
    }

    public function fromArray(array $array_data = null, $class = __CLASS__){
        parent::fromArray($array_data, $class);
    }

    public function saveCategory($langId = null, $idShop = null){
        $categories_ids = array();
        $root = Category::getRootCategory($langId);
        $inicio_id = $root ? $root->id : 0;
        if($inicio_id){
            $categories_ids[] = (int)$inicio_id;
        }

        if(!empty($this->families)){
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

    public function saveBrand($langId = null, $idShop = null)
    {
        //die(json_encode($this));
        $brand_id = Manufacturer::getIdByName($this->brandname);
        if(!$brand_id){
            $manufacturer = new Manufacturer(null,$langId);
            $manufacturer->id = $this->brandid;
            $manufacturer->force_id = true;
            $manufacturer->name = $this->brandname;
            $manufacturer->description = $this->brandname;
            $manufacturer->active = true;
            $manufacturer->id_shop_list[] = $idShop;
            $manufacturer->add();
            $brand_id = $manufacturer->id;
        }
        return $brand_id;
    }

    public function saveGama($langId = null, $idShop = null){

        $query = new DbQuery();
        $query->select('id_attribute_group');
        $query->from('attribute_group');
        $query->where('`name` = "Gama"');
        $query->limit(1);


        $result = Db::getInstance()->executeS($query);
        if(!$result) //si no existe agregar
        {
            $attribute_group = new AttributeGroup(null, $langId, $idShop);
            $attribute_group->name = "Gama";
            $attribute_group->position = 0;
            $attribute_group->group_type = 'select';
            $attribute_group->public_name = "Gama";
            $attribute_group->is_color_group = false;
            $attribute_group->add();

            $result = Db::getInstance()->executeS($query)[0];
        }

        $attribute = new Attribute($this->gama, $langId, $idShop);
        if(!Attribute::existsInDatabase($this->gama, 'attribute'))
        {
            $attribute->force_id = true;
            $attribute->name = $this->contenido;
            $attribute->position = 0;
            $attribute->id_attribute_group = (int)$result[0]['id_attribute_group'];
            $attribute->add();
        }
        return $attribute;
    }
}