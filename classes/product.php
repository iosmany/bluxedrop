
<?php


class BluxeProduct
{
	public $id;
	public $eans = array();
	public $descripcion;
	public $setContent;
	public $price;
	public $pvr;
	public $stock;
	public $brandId;
	public $brandName;
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

	public function __construct()
	{
		
	}

    public function from_json($json_data)
    {
        $decoded = json_decode($json_data, true);
        foreach($decoded as $key => $val) {
            if(property_exists(__CLASS__, $key)) {
                $this->$key = $val;
            }
        }
    }

} 