<?php
/**
 * Created by PhpStorm.
 * User: frimko
 * Date: 11.10.2015
 * Time: 14:54
 */

namespace Bitrix\Web38\Nestablemenupro;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\TextField;


class SettingsMenuTable extends DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'nestable_menu_pro';
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID',array(
                'primary' => true,
                'autoincrement' => true,
                'autocomplete' => true
            )),
            new StringField('NAME'),
            new TextField('DATA'),
        );
    }

}