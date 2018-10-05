<?php
/**
 * Created by PhpStorm.
 * User: iosmany
 * Date: 05/10/2018
 * Time: 11:45
 */

class ModelExtensiones
{
    public function from_json($json_object = "", $class = __CLASS__)
    {
        $decoded = json_decode($json_object, true);
        $this->from_array($decoded, $class);
    }

    public function from_array(array $array_data = null, $class = __CLASS__)
    {
        foreach($array_data as $key => $val){
            $key = strtolower($key);
            if(property_exists($class, $key)) {
                $this->$key = $val;
            }
        }
    }
}