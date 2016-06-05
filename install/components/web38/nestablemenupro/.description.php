<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("FRIMKO_NESTABLEMENU_MENU_ITEMS_NAME"),
	"DESCRIPTION" => GetMessage("FRIMKO_NESTABLEMENU_MENU_ITEMS_DESC"),
	"PATH" => array(
		"ID" => "Web38",
		"CHILD" => array(
			"ID" => "Menu",
			"NAME" => GetMessage("FRIMKO_NESTABLEMENU_NAVIGATION_SERVICE")
		)
	),
);

?>