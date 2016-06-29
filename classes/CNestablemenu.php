<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Web38\nestablemenupro\SettingsMenuTable;

/**
 * Class CNestablemenu
 */
class CNestablemenu extends CBitrixComponent
{
    /**
     * @var array
     */
    public $arFullMenu = array();
    public $arFullMenu2 = array();

    /**
     * @return string
     */
    public static function getDefaultItem(){
        return '[{"id":1,"text":"'.getMessage("FRIMKO_NESTABLEMENU_NEW_ELEMENT").'","link":"link","additional_links":"","params":"","permission":"R","hide":""}]';
    }


    /**
     * создание массива для меню из оригинального меню (нерабочее)
     *
     * @param array $arChildMenu
     * @param bool $arFullMenu
     * @param bool $copy
     * @param int $level
     * @return array|bool
     */
    public function _getSiteMenuRecursive($arChildMenu = array(), $level = 1)
    {
        global $arFullMenu2;
        foreach ($arChildMenu as $row) {
            $obChildren = new CMenu("left");
            $obChildren->MenuDir = '/site_rk/';
            $obChildren->Init('/site_rk/'.$row[1], false, false, true);
            $arr = $obChildren->arMenu;
            if ($arr) {
                $r = array(
                    'TEXT' => $row[0],
                    'LINK' => $row[1],
                    'ADDITIONAL_LINKS' => $row[2],
                    'PARAMS' => $row[3],
                    'PERMISSION' => $row[4],
                    'IS_PARENT' => true,
                    'DEPTH_LEVEL' => $level,
                );
                $arFullMenu2[] = $r;
                   // $this->constructMenu($arr, $level + 1);
            } else {
                $r = array(
                    'TEXT' => $row[0],
                    'LINK' => $row[1],
                    'ADDITIONAL_LINKS' => $row[2],
                    'PARAMS' => $row[3],
                    'PERMISSION' => $row[4],
                    'IS_PARENT' => false,
                    'DEPTH_LEVEL' => $level
                );
                $arFullMenu2[] = $r;
            }
        }
        return $arFullMenu2;
    }

    /**
     * конструктор массива меню
     *
     * @param array $arChildMenu
     * @param array $arFullMenu
     * @param int $level
     */
    public function constructMenu($arChildMenu = array(), $level = 1)
    {
        foreach ($arChildMenu as $row) {
            if ($row->children) {
                $this->arFullMenu[] = array(
                    'TEXT' => $row->text,
                    'LINK' => $row->link,
                    'ADDITIONAL_LINKS' => $row->additional_links,
                    'PARAMS' => $row->params,
                    'PERMISSION' => "R",
                    'IS_PARENT' => true,
                    'DEPTH_LEVEL' => $level,
                    'HIDE' => $row->hide,
                    'SECTION' => $row->section,
                );
                $this->constructMenu($row->children, $level + 1);
            } else {
                $this->arFullMenu[] = array(
                    'TEXT' => $row->text,
                    'LINK' => $row->link,
                    'ADDITIONAL_LINKS' => $row->additional_links,
                    'PARAMS' => $row->params,
                    'PERMISSION' => "R",
                    'IS_PARENT' => false,
                    'DEPTH_LEVEL' => $level,
                    'HIDE' => $row->hide,
                    'SECTION' => $row->section,
                );
            }
        }
    }

    /**
     * список всех меню
     *
     * @return array|bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getArrTable()
    {
        $result = SettingsMenuTable::getList();
        if ($arrSelect = $result->fetchAll()) {
            $arMenu = array();
            foreach ($arrSelect as $key => $row) {
                $arMenu[$row['ID']] = $row;
            }
            return $arMenu;
        } else {
            return false;
        }
    }

    /**
     * покажет нам селект
     *
     * @param bool $menuID - идентификатор
     * @param bool $formName - имя формы для ее использования
     * @return string
     */
    public static function showSelect($menuID = false, $formName = false)
    {
        $arrTable = self::getArrTable();
        if(!$menuID) $menuID = reset($arrTable)['ID'];

        list($arNameMenu, $arIDMenu) = array();
        foreach($arrTable as $row){
            $arNameMenu[] = $row['NAME'];
            $arIDMenu[] = $row['ID'];
        }
        $arr = array(
            'REFERENCE' => $arNameMenu,
            'REFERENCE_ID' => $arIDMenu
        );
        if($formName){
            return SelectBoxFromArray('menu', $arr, $menuID, '', 'class="select_menu"', true, $formName);
        }else{
            return SelectBoxFromArray('menu', $arr, $menuID, '', 'class="select_menu"', false, '');
        }
    }


}

