<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 03/10/2018
 * Time: 22:56
 */

class ModuleConfigs
{
    public static function installsql($tablename = "")
    {
        $sql = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_.$tablename."` (
              `user` varchar(50) NOT NULL PRIMARY KEY,
              `password` varchar(20) NOT NULL,
              `token` varchar(250) NULL,
              `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `date_upd` datetime NULL
            ) ENGINE = "._MYSQL_ENGINE_;

        return !Db::getInstance()->execute($sql);
    }

    public static function uninstallsql($tablename = "")
    {
        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_.$tablename."`";
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
        return true;
    }
}