
<?php

/**
 * Class BluxeAccess
 */
class BluxeAccess extends ObjectModel
{
	public $user;
	public $password;
	public $token;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'bluxedrop_access',
        'primary' => 'user',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'user'     =>                [ 'type' => self::TYPE_STRING ],
            'password' =>                [ 'type' => self::TYPE_STRING ],
            'token'    =>                [ 'type' => self::TYPE_STRING ],
            'date_add' =>                array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'),
            'date_upd' =>                array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'),
        )];

	public function __construct($user = null, $id_lang= null, $id_shop = null)
	{
        parent::__construct($user, $id_lang, $id_shop);
        $this->user = $user;
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
    public function from_array($array_data)
    {
        foreach($array_data as $key => $val) {
            if(property_exists(__CLASS__, $key)) {
                $this->$key = $val;
            }
        }
    }
} 