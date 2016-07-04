<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
if($arParams['INCLUDE_JQUERY'] == 'Y') CJSCore::Init(array("jquery"));
if ($this->__folder)
    $pathToTemplateFolder = $this->__folder;
else
    $pathToTemplateFolder = str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '', dirname(__FILE__));
?>
<style>
    @font-face {
        font-family: 'PT Sans Narrow';
        font-style: normal;
        font-weight: 400;
        src: local('PT Sans Narrow'), local('PTSans-Narrow'), url(<?=$pathToTemplateFolder?>/font/font1.woff2) format('woff2');
    }
    @font-face {
        font-family: 'PT Sans Narrow';
        font-style: normal;
        font-weight: 700;
        src: local('PT Sans Narrow Bold'), local('PTSans-NarrowBold'), url(<?=$pathToTemplateFolder?>/font/font2.woff2) format('woff2');
    }
</style>

<?
$APPLICATION->SetAdditionalCSS("{$pathToTemplateFolder}/sm-blue.css");
$APPLICATION->AddHeadScript("{$pathToTemplateFolder}/jquery.smartmenus.js");
?>



<? if (!empty($arResult)): ?>
    <nav>
        <div class="">
            <input id="main-menu-state" type="checkbox"/>
            <label class="main-menu-btn" for="main-menu-state">
                <span class="main-menu-btn-icon"></span> Toggle main menu visibility
            </label>
            <ul id="main-menu" class="sm sm-blue">

                <?
                $previousLevel = 0;
                foreach ($arResult as $arItem): ?>

                <? if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                    <?= str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"])); ?>
                <? endif ?>

                <? if ($arItem["IS_PARENT"]): ?>

                <? if ($arItem["DEPTH_LEVEL"] == 1): ?>
                <li><a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
                    <ul>
                        <? else: ?>
                        <li><a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
                            <ul>
                                <? endif ?>

                                <? else:?>

                                    <? if ($arItem["PERMISSION"] > "D"):?>

                                        <? if ($arItem["DEPTH_LEVEL"] == 1):?>
                                            <li><a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a></li>
                                        <? else:?>
                                            <li><a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
                                                <? if ($arItem['IS_ELEMENT'] && $arItem['DETAIL_PICTURE']): ?>
                                                    <ul class="mega-menu">
                                                        <li>
                                                            <div style="width:400px;max-width:320px">
                                                                <div style="padding:5px 24px;">
                                                                    <p><?= CFile::ShowImage($arItem['DETAIL_PICTURE']) ?></p>
                                                                    <p><strong><?= $arItem['TEXT'] ?></strong></p>
                                                                    <p><?= $arItem['PREVIEW_TEXT'] ?></p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                <? endif ?>
                                            </li>
                                        <? endif ?>

                                    <? else:?>

                                        <? if ($arItem["DEPTH_LEVEL"] == 1):?>
                                            <li><a href=""
                                                   title="<?= GetMessage("MENU_ITEM_ACCESS_DENIED") ?>"><?= $arItem["TEXT"] ?></a>
                                            </li>
                                        <? else:?>
                                            <li><a href=""
                                                   title="<?= GetMessage("MENU_ITEM_ACCESS_DENIED") ?>"><?= $arItem["TEXT"] ?></a>
                                            </li>
                                        <? endif ?>

                                    <? endif ?>

                                <? endif ?>

                                <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>

                                <? endforeach ?>

                                <? if ($previousLevel > 1)://close last item tags?>
                                    <?= str_repeat("</ul></li>", ($previousLevel - 1)); ?>
                                <? endif ?>

                            </ul>
        </div>
    </nav>
<?endif?>
                <style>
                    /*мусору однако...*/
                    ul#smartmenus > a:before{
                        color: #FFF!important;
                    }

                    #smartmenus ul {
                        width: 12em;
                    }
                    div.smartnavmenu{
                        width: 19em;
                    }
                    .sm-blue a {
                        color: #FFF;
                    }

                    .main-menu-btn {
                        position: relative;
                        display: inline-block;
                        width: 28px;
                        height: 28px;
                        text-indent: 28px;
                        white-space: nowrap;
                        overflow: hidden;
                        cursor: pointer;
                        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
                    }

                    /* hamburger icon */
                    .main-menu-btn-icon, .main-menu-btn-icon:before, .main-menu-btn-icon:after {
                        position: absolute;
                        top: 50%;
                        left: 2px;
                        height: 2px;
                        width: 24px;
                        background: #bbb;
                        -webkit-transition: all 0.25s;
                        transition: all 0.25s;
                    }

                    .main-menu-btn-icon:before {
                        content: '';
                        top: -7px;
                        left: 0;
                    }

                    .main-menu-btn-icon:after {
                        content: '';
                        top: 7px;
                        left: 0;
                    }

                    /* x icon */
                    #main-menu-state:checked ~ .main-menu-btn .main-menu-btn-icon {
                        height: 0;
                        background: transparent;
                    }

                    #main-menu-state:checked ~ .main-menu-btn .main-menu-btn-icon:before {
                        top: 0;
                        -webkit-transform: rotate(-45deg);
                        transform: rotate(-45deg);
                    }

                    #main-menu-state:checked ~ .main-menu-btn .main-menu-btn-icon:after {
                        top: 0;
                        -webkit-transform: rotate(45deg);
                        transform: rotate(45deg);
                    }

                    /* hide menu state checkbox (keep it visible to screen readers) */
                    #main-menu-state {
                        position: absolute;
                        width: 1px;
                        height: 1px;
                        margin: -1px;
                        border: 0;
                        padding: 0;
                        overflow: hidden;
                        clip: rect(1px, 1px, 1px, 1px);
                    }

                    /* hide the menu in mobile view */
                    #main-menu-state:not(:checked) ~ #main-menu {
                        display: none;
                    }

                    #main-menu-state:checked ~ #main-menu {
                        display: block;
                    }

                    @media (min-width: 768px) {
                        /* hide the button in desktop view */
                        .main-menu-btn {
                            position: absolute;
                            top: -99999px;
                        }

                        /* always show the menu in desktop view */
                        #main-menu-state:not(:checked) ~ #main-menu {
                            display: block;
                        }
                    }
                </style>
                <script type="text/javascript">
                    $(function () {
                        $('#main-menu').smartmenus({
                            mainMenuSubOffsetX: 1,
                            mainMenuSubOffsetY: -8,
                            subMenusSubOffsetX: 1,
                            subMenusSubOffsetY: -8
                        });
                        var $mainMenuState = $('#main-menu-state');
                        if ($mainMenuState.length) {
                            // animate mobile menu
                            $mainMenuState.change(function (e) {
                                var $menu = $('#main-menu');
                                if (this.checked) {
                                    $menu.hide().slideDown(250, function () {
                                        $menu.css('display', '');
                                    });
                                } else {
                                    $menu.show().slideUp(250, function () {
                                        $menu.css('display', '');
                                    });
                                }
                            });
                            // hide mobile menu beforeunload
                            $(window).bind('beforeunload unload', function () {
                                if ($mainMenuState[0].checked) {
                                    $mainMenuState[0].click();
                                }
                            });
                        }
                    });
                </script>