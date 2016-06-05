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
        foreach($result as $key => $menuItem){
            $aMenuItem =  array(
                "TEXT" => $menuItem['NAME'],
                "TITLE" => $menuItem['NAME'],
                "SORT" => $key,
                "ACTION" => 'location.href = "'.$menu_edit_url.'&menu='.$menuItem['ID'].'"',
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
    $arResult = $menu->arFullMenu;

    array_walk_recursive($arResult, function (&$item) {
        $item = mb_convert_encoding($item, SITE_CHARSET, "UTF-8");
    });
    $this->IncludeComponentTemplate();
}
