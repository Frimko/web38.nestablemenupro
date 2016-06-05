<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

/**
 * Company developer: Darneo Studio
 * Developer: Samokhvalov Sergey
 * Site: http://www.darneo.ru/
 * E-mail: ccc-car@yandex.ru
 * @copyright (c) 2015-2016 frimko
 *
 */

use Bitrix\Main\Loader;
use Bitrix\Web38\Nestablemenupro\SettingsMenuTable;


Loader::registerAutoLoadClasses("web38.nestablemenupro", array(
    'Bitrix\Web38\Nestablemenupro\SettingsMenuTable' => 'lib/SettingsMenuTable.php',
    "CNestablemenu" => "classes/CNestablemenu.php",
));
?>