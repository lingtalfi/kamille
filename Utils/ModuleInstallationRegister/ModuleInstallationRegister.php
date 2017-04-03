<?php


namespace Kamille\Utils\ModuleInstallationRegister;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

class ModuleInstallationRegister
{


    public static function isInstalled($module)
    {
        $list = self::getInstalled();
        return (in_array($module, $list, true));
    }

    public static function getInstalled()
    {
        $ret = [];
        $appDir = ApplicationParameters::get("app_dir", null, true);
        $modulesFile = $appDir . "/modules.txt";
        if (file_exists($modulesFile)) {
            $ret = file($modulesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $ret = array_filter($ret);
            $ret = array_unique($ret);
        }
        return $ret;
    }

    //--------------------------------------------
    //
    //--------------------------------------------

}
