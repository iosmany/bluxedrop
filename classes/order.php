<?php



public class ClientOrderLineModel
{
 	public $productId;
 	public $units;
}

class ClientOrderModel
{
	 public $ordernumber;
	 public $valoration;
	 public $carriernotes;
	 public $lines = array();
	 public $name;
	 public $secondname;
	 public $telephone;
	 public $mobile;
	 public $street;
	 public $city;
	 public $county;
	 public $postalcode;
	 public $country;
}