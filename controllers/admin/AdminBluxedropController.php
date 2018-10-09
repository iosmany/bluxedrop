<?php
/**
 * Created by PhpStorm.
 * User: iosmany
 * Date: 08/10/2018
 * Time: 10:53
 */

class AdminBluxedropController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }
}