<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$arTemplateParameters = array(
	"INCLUDE_JQUERY" => Array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("INCLUDE_JQUERY"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => 'Y'
	),
/*	"THEME" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("THEME"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"red" => GetMessage("THEME_RED"),
			"ice" => GetMessage("THEME_ICE"),
			"green" => GetMessage("THEME_GREEN"),
			"yellow" => GetMessage("THEME_YELLOW"),
			"pink" => GetMessage("THEME_PINK"),
			"metal" => GetMessage("THEME_METAL"),
		),
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => '={$ys_options["color_scheme"]}',
	),*/
);
?>