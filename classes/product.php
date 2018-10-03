
<?php


class ProductDTO extends ObjectModel
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
} 