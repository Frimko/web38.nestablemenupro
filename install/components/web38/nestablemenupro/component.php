<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Web38\Nestablemenupro\SettingsMenuTable;

$id = $arParams['ROOT_MENU_TYPE'];
if ($USER->IsAuthorized()) {
    $buttonID = "nestablemenupro";
    $menu_edit_url = "/bitrix/admin/nestable_menu_show_pro.php?lang=" . LANGUAGE_ID .
        "&menu=" . $id . "&back_url=" . urlencode($_SERVER["REQUEST_URI"]);

    $this->AddIncludeAreaIcons(array(
        array(
            "URL" => $menu_edit_url,
            "ICON" => "bx-context-toolbar-edit-icon",
            "TITLE" => GetMessage("NESTABLE_MENU_EDIT"),
            "DEFAULT" => true,
        )
    ));

    $APPLICATION->AddPanelButton(array(
            "ID" => $buttonID,
            "ICON" => "bx-panel-menu-icon",
            "ALT" => GetMessage('NESTABLE_MENU_TOP_PANEL_BUTTON_ALT'),
            "TEXT" => GetMessage("NESTABLE_MENU_TOP_PANEL_BUTTON_TEXT"),
            "MAIN_SORT" => "300",
            "SORT" => 10,
            "RESORT_MENU" => true,
            "HINT" => array(
                "TITLE" => GetMessage('NESTABLE_MENU_TOP_PANEL_BUTTON_TEXT'),
                "TEXT" => GetMessage('NESTABLE_MENU_TOP_PANEL_BUTTON_HINT'),
            )
        )
    );
}
if ($this->StartResultCache() && CModule::IncludeModule('web38.nestablemenupro')) {
    $result = CNestablemenu::getArrTable();
    if ($USER->IsAuthorized()) {
        foreach ($result as $key => $menuItem) {
            $aMenuItem = array(
                "TEXT" => $menuItem['NAME'],
                "TITLE" => $menuItem['NAME'],
                "SORT" => $key,
                "ACTION" => 'location.href = "' . $menu_edit_url . '&menu=' . $menuItem['ID'] . '"',
            );
            $APPLICATION->AddPanelButtonMenu($buttonID, $aMenuItem);
        }
    }
    $code = $result[$id]['DATA'];
    $code = mb_convert_encoding($code, "UTF-8", SITE_CHARSET);
    $menu = new CNestablemenu;
    $menu->constructMenu(
        json_decode($code)
    );
    $arrMenuItems = $menu->arFullMenu;

    array_walk_recursive($arrMenuItems, function (&$item) {
        $item = mb_convert_encoding($item, SITE_CHARSET, "UTF-8");
    });


    $arNewMenuItems = array();
    foreach ($arrMenuItems as $item) {
        if ($idSection = $item['SECTION']) {
            $item['IS_PARENT'] = true;
            $arNewMenuItems[] = $item;

            $arrSection = array();
            $arElements = array();
            $arFilterSection = array('IBLOCK_ID' => $idSection, 'ACTIVE' => 'Y');
            $arSelect = array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL', 'SECTION_PAGE_URL', 'DETAIL_PICTURE');
            $obSection = CIBlockSection::GetList(Array("left_margin" => "asc"), $arFilterSection, false, $arSelect);
            while ($ar_fieldsn = $obSection->getNext()) {
                $ar_fieldsn['IS_PARENT'] = false;
                $arrSection[$ar_fieldsn['ID']] = $ar_fieldsn;
            }

            $element = new CIBlockElement();
            $arFilterElement = array(
                'IBLOCK_ID' => $idSection,
                'ACTIVE' => 'Y',
                "<=DATE_ACTIVE_FROM" => array(false, ConvertTimeStamp(false, "FULL")),
                ">=DATE_ACTIVE_TO" => array(false, ConvertTimeStamp(false, "FULL")),
            );
            $arSelect = array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'IBLOCK_SECTION_ID',
                'LID',
                'PREVIEW_TEXT',
                'DETAIL_PAGE_URL',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
            );
            $res = $element->GetList(Array("SORT" => "ASC"), $arFilterElement, false, false, $arSelect);
            while ($ar_fields = $res->GetNext()) {
                $arElements[] = $ar_fields;
            }

            foreach ($arrSection as $key => $value) {
                if ($arrSection[$value['IBLOCK_SECTION_ID']]) {
                    $arrSection[$value['IBLOCK_SECTION_ID']]['IS_PARENT'] = true;
                }
            }
            foreach ($arrSection as $section) {
                $arr = array(
                    'TEXT' => $section['NAME'],
                    'LINK' => $section['SECTION_PAGE_URL'],
                    'ADDITIONAL_LINKS' => $item['ADDITIONAL_LINKS'],
                    'PARAMS' => $item['PARAMS'],
                    'PERMISSION' => $item['PERMISSION'],
                    'IS_PARENT' => $section['IS_PARENT'],
                    'DEPTH_LEVEL' => $section['DEPTH_LEVEL'] + $item['DEPTH_LEVEL'],
                    'HIDE' => $item['HIDE'],
                    'DETAIL_PICTURE' => $section['DETAIL_PICTURE'],
                    'ID' => $section['ID'],
                    'FROM_IBLOCK' => $idSection,
                );
                foreach ($arElements as $arElement) {
                    if ($arElement['IBLOCK_SECTION_ID'] == $section['ID']) {
                        if (!$arr['IS_PARENT']) {
                            $arr['IS_PARENT'] = true;
                        }
                    }
                }
                $arNewMenuItems[] = $arr;
                foreach ($arElements as $arElement) {
                    if ($arElement['IBLOCK_SECTION_ID'] == $section['ID']) {
                        $arrChild = array(
                            'TEXT' => $arElement['NAME'],
                            'LINK' => $arElement['DETAIL_PAGE_URL'],
                            'ADDITIONAL_LINKS' => $item['ADDITIONAL_LINKS'],
                            'PARAMS' => $item['PARAMS'],
                            'PERMISSION' => $item['PERMISSION'],
                            'IS_PARENT' => false,
                            'DEPTH_LEVEL' => $arr['DEPTH_LEVEL'] + 1,
                            'HIDE' => $item['HIDE'],
                            'DETAIL_PICTURE' => $arElement['DETAIL_PICTURE'],
                            'PREVIEW_PICTURE' => $arElement['PREVIEW_PICTURE'],
                            'ID' => $arElement['ID'],
                            'FROM_IBLOCK' => $idSection,
                            'IS_ELEMENT' => true,
                            'IBLOCK_SECTION_ID' => $arElement['IBLOCK_SECTION_ID'],
                            'PREVIEW_TEXT' => $arElement['PREVIEW_TEXT']
                        );
                        $arNewMenuItems[] = $arrChild;

                    }
                }
            }
        } else {
            $arNewMenuItems[] = $item;
        }
    }
    $arResult = $arNewMenuItems;
    $this->IncludeComponentTemplate();
}
