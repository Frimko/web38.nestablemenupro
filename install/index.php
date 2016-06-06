<?php
/**
 * Created by PhpStorm.
 * User: Ranerg
 * Date: 27.08.2015
 * Time: 14:52
 */

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);
if (class_exists('web38_nestablemenupro')) return;

class web38_nestablemenupro extends CModule
{
    var $MODULE_ID = "web38.nestablemenupro";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public $PATH_FROM;
    public $PATH_TO;

    public function __construct()
    {
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");


        $this->MODULE_ID = "web38.nestablemenupro";
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage("FRIMKO_NESTABLEMENU_PRO_REG_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("FRIMKO_NESTABLEMENU_PRO_REG_MODULE_DESCRIPTION");
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage("FRIMKO_NESTABLEMENU_PRO_PARTNER");
        $this->PARTNER_URI = "mailto:ccc-car@yandex.ru";
        $this->PATH_FROM = $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/' . $this->MODULE_ID;
        $this->PATH_TO = $_SERVER["DOCUMENT_ROOT"] . "/bitrix";
    }

    public function DoInstall()
    {
        $this->InstallFiles();
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallDB();
    }

    public function DoUninstall()
    {
        $this->uninstallDB();
        $this->UnInstallFiles();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    function InstallFiles()
    {
        CopyDirFiles($this->PATH_FROM . "/install/components", $this->PATH_TO . "/components", true, true);
        CopyDirFiles($this->PATH_FROM . "/install/css", $this->PATH_TO . "/css/" . $this->MODULE_ID, true, true);
        CopyDirFiles($this->PATH_FROM . "/install/js", $this->PATH_TO . "/js/" . $this->MODULE_ID, true, true);
        CopyDirFiles($this->PATH_FROM . "/install/admin", $this->PATH_TO . "/admin/", true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles($this->PATH_FROM . "/install/admin", $this->PATH_TO . "/admin");
        DeleteDirFilesEx("/bitrix/components/web38");
        DeleteDirFilesEx("/bitrix/css/" . $this->MODULE_ID);
        DeleteDirFilesEx("/bitrix/js/" . $this->MODULE_ID);
        return true;
    }

    public function installDB()
    {
       include $this->PATH_FROM . "/lib/SettingsMenuTable.php";
            Bitrix\Web38\Nestablemenupro\SettingsMenuTable::getEntity()->createDbTable();
            $name = 'new_menu';
            $data = '[{"id":1,"text":"' . Loc::getMessage("FRIMKO_NESTABLEMENU_PRO_NEW_ELEMENT") . '","link":"link","additional_links":"","params":"","permission":"R","hide":"","children":[{"id":2,"text":"' . Loc::getMessage("FRIMKO_NESTABLEMENU_PRO_NEW_ELEMENT") . '","link":"link","additional_links":"","params":"","permission":"R","hide":"","children":[{"id":1,"text":"' . Loc::getMessage("FRIMKO_NESTABLEMENU_PRO_NEW_ELEMENT") . '","link":"link","additional_links":"","params":"","permission":"","hide":""}]}]}]';
            Bitrix\Web38\Nestablemenupro\SettingsMenuTable::add(array(
                'NAME' => $name,
                'DATA' => $data,
            ));
        
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            $connection = Application::getInstance()->getConnection();
            if (Bitrix\Web38\Nestablemenupro\SettingsMenuTable::getList()) {
                $connection->dropTable(Bitrix\Web38\Nestablemenupro\SettingsMenuTable::getTableName());
            }
        }
    }
}
?>