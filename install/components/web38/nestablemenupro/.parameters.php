<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


CModule::IncludeModule('web38.nestablemenupro');
foreach(CNestablemenu::getArrTable() as $row){
    $arMenu[$row['ID']] = $row['NAME'];
}
$default = reset($arMenu)['ID'];

$arComponentParameters = array(
    "GROUPS" => array(
        "CACHE_SETTINGS" => array(
            "NAME" => GetMessage("COMP_GROUP_CACHE_SETTINGS"),
            "SORT" => 600
        ),
    ),
    "PARAMETERS" => array(

        "ROOT_MENU_TYPE" => Array(
            "NAME" => GetMessage("MAIN_MENU_TYPE_NAME"),
            "TYPE" => "LIST",
            "DEFAULT" => $default,
            "VALUES" => $arMenu,
            "PARENT" => "BASE",
            "COLS" => 45
        ),

        "MENU_CACHE_TYPE" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("COMP_PROP_CACHE_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => array(
                "A" => GetMessage("COMP_PROP_CACHE_TYPE_AUTO"),
                "Y" => GetMessage("COMP_PROP_CACHE_TYPE_YES"),
                "N" => GetMessage("COMP_PROP_CACHE_TYPE_NO"),
            ),
            "DEFAULT" => "Y",
            "ADDITIONAL_VALUES" => "N",
        ),

        "MENU_CACHE_TIME" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("COMP_PROP_CACHE_TIME"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => 3600000,
            "COLS" => 5,
        ),


    )
);
?>
