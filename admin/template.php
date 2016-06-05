<?
use Bitrix\Main\Localization\Loc;

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$result = Bitrix\Web38\Nestablemenupro\SettingsMenuTable::getList();
$menuID = $request->getPost("menu") ? $request->getPost("menu") : 1;
$arr = array();
$arMenu = array();
if ($arMenu = CNestablemenu::getArrTable()) {
    if ($menuID = $request->getQuery("menu")) {
        $serializeData = $arMenu[$menuID]['DATA'];
        $title = $arMenu[$menuID]['NAME'];
    } else {
        $menuID = reset($arMenu)['ID'];
        $serializeData = reset($arMenu)['DATA'];
        $title = reset($arMenu)['NAME'];
    }
    //защита от дурака, удаляющего меню в базе
    if(strlen($serializeData) < 5) $serializeData = CNestablemenu::getDefaultItem();

    $serializeData = mb_convert_encoding($serializeData, 'UTF-8', SITE_CHARSET);
    $menu = new CNestablemenu;
    $menu->constructMenu(json_decode($serializeData));
    $arFullMenu = $menu->arFullMenu;

    array_walk_recursive($arFullMenu, function (&$item) {
        $item = mb_convert_encoding($item, SITE_CHARSET, 'UTF-8');
    });
}
?>
<?
/*выгружает реальное меню, но...*/
/*$obParents = new CMenu('top');
$obFullmenu = new CNestablemenu;
$obParents->Init($APPLICATION->GetCurDir());
$arParentMenu = $obParents->arMenu;
$arFullMenu = $obFullmenu->getSiteMenuRecursive($arParentMenu);
*/

function template_li($id, $item = false)
{
    if (!$item) {
        $item = array(
            'TEXT' => Loc::GetMessage('FRIMKO_NESTABLEMENU_NEW_ELEMENT'),
            'LINK' => 'link',
            'ADDITIONAL_LINKS' => Array(),
            'PARAMS' => Array(),
            'PERMISSION' => ''
        );
    }
    if ($item['HIDE'])
        $hide['on'] = $item['HIDE'];
    else
        $hide['off'] = 'disabled';
    $title = $item['TEXT'] . " \"" . $item['LINK'] . "\"";

    return '
    <li class="dd-item"
data-id="' . $id . '"
data-text="' . $item['TEXT'] . '"
data-link="' . $item['LINK'] . '"
data-additional_links="' . $item['ADDITIONAL_LINKS'] . '"
data-params="' . $item['PARAMS'] . '"
data-permission="' . $item['PERMISSION'] . '"
data-hide="' . $item['HIDE'] . '"
>
<div title="' . $title . '" class="drag-btn dd-handle">
<div class="dd-dragel drag-btn text-group">
<div class="drag-btn name-item"><span>' . $item['TEXT'] . '</span></div>
<div class="drag-btn url-item"><span>' . $item['LINK'] . '</span></div>
</div>
<div class="noDrag-btn btn-group">
    <div class="btn-default" title="' . Loc::GetMessage("FRIMKO_NESTABLEMENU_ADD_ELEMENT") . '"><span class="glyphicon glyphicon-plus"></span></div>
    <div class="btn-default" title="' . Loc::GetMessage("FRIMKO_NESTABLEMENU_EDIT_ELEMENT") . '"><span class="glyphicon glyphicon-pencil"></span></div>
    <div class="btn-default ' . $hide['off'] . ' " title="' . Loc::GetMessage("FRIMKO_NESTABLEMENU_SHOW_ELEMENT") . '"><span class="glyphicon glyphicon-eye-open"></span></div>
    <div class="btn-default ' . $hide['on'] . ' " title="' . Loc::GetMessage("FRIMKO_NESTABLEMENU_HIDE_ELEMENT") . '"><span class="glyphicon glyphicon-eye-close"></span></div>
    <div class="btn-default" title="' . Loc::GetMessage("FRIMKO_NESTABLEMENU_DELETE_ELEMENT") . '"><span class="glyphicon glyphicon-trash"></span></div>
</div>
</div>';
}


?>
<header class="nestable">
    <div class="inline">

        <div
            class="selectMenu"><?= CNestablemenu::showSelect($menuID) ?></div>

        <div class="btn">
            <div title="Добавить меню" class="btn-default js-addMenuBtn"><span class="glyphicon glyphicon-plus"></span>
            </div>
            <div class="add_new_menu">
                <div class="css">
                    <label for="add_new_menu"><?=Loc::GetMessage("FRIMKO_NESTABLEMENU_NAME_NEW_MENU")?></label>
                    <input class="js-addNewMenu" id="add_new_menu" type="text" required>
                    <div style="" class="group-form">
                        <div class="btn-Form js-btn"><span class="glyphicon glyphicon-ok"></span></div>
                    </div>
                </div>
            </div>
            <div title="<?=Loc::GetMessage("FRIMKO_NESTABLEMENU_DELETE_MENU")?>" class="btn-default js-delMenuBtn"><span class="glyphicon glyphicon-trash"></span>
            </div>
        </div>
    </div>
    <br>

    <div>
        <label for="exp-collapser"></label>
        <input id="exp-collapser" type="checkbox" value="switchValue"/>
    </div>
</header>
<main>

    <div class="cf nestable-lists">
        <div class="dd" id="nestable">
            <ol class="dd-list">
                <?
                $flagParent = true;
                $previousLevel = 0;
                $id_li = 0;
                ?>

                <? foreach ($arFullMenu as $arItem): ?>
                <? $id_li++; ?>

                <? if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel): ?>
                    <?= str_repeat("</ol></li>", ($previousLevel - $arItem["DEPTH_LEVEL"])); ?>
                <? endif ?>

                <? if ($arItem["IS_PARENT"]): ?>
                <?= template_li($id_li, $arItem); ?>
                <ol class="dd-list">
                    <? else: ?>
                        <?= template_li($id_li, $arItem); ?>
                        </li>
                    <? endif ?>
                    <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>

                    <? endforeach ?>

                    <? if ($previousLevel > 1): ?>
                        <?= str_repeat("</ol></li>", ($previousLevel - 1)); ?>
                    <? endif ?>
                </ol>
        </div>
    </div>
    <textarea id="nestable-output-change" name="json-menu"></textarea>
</main>

<script type="application/javascript">
    $(document).ready(function () {
        var nameItem, urlItem;
        var textConfirm = '<?=Loc::GetMessage("FRIMKO_NESTABLEMENU_UDALITQ");?>';
        var textConfirmDelMenu = '<?=Loc::GetMessage("FRIMKO_NESTABLEMENU_DEL_MENU");?>';
        var newElement = '<?= str_replace(array("\r", "\n"), '', template_li(1)) ?>';
        var expand = '<?=Loc::GetMessage("FRIMKO_NESTABLEMENU_EXPAND_ALL");?>';
        var collapse = '<?=Loc::GetMessage("FRIMKO_NESTABLEMENU_COLLAPSE_ALL");?>';


        $('input.js-addNewMenu').each(function () {
            $(this).on('focus', function () {
                $(this).parent('.css').addClass('active');
            });
            $(this).on('blur', function () {
                if ($(this).val().length == 0) {
                    $(this).parent('.css').removeClass('active');
                }
            });
            if ($(this).val() != '') $(this).parent('.css').addClass('active');
        });

        $('div.add_new_menu div.js-btn').on('click', function () {
            var $add_new_menu = $('.js-addNewMenu');
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    add_new: $add_new_menu.val()
                },
                success: function (json) {
                    if (json.success) {
                        location.href = json.url;
                    } else if (json.error) {
                        alert(json.text);
                    }
                }
            });

        });
        $('.js-addMenuBtn').on('click', function () {
            var $this = $(this),
                $add_new_menu = $('.add_new_menu');
            $add_new_menu.find('input').val('');
            $this.addClass('active');
            $add_new_menu.animate({
                "width": "toggle", "opacity": "toggle"
            }, "fast");
        });
        $('.js-delMenuBtn').on('click', function () {
            if (confirm(textConfirmDelMenu)) {
                var idMenu = $('.select_menu').val();
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        sessid: BX.bitrix_sessid(),
                        del_menu: idMenu
                    },
                    success: function (json) {
                        if (json.success) {
                            location.href = json.url;
                        } else if (json.error) {
                            alert(json.text);
                        }
                    }
                });
            }
        });

        $('select.select_menu').on('change', function () {
            location.search = '?menu=' + $(this).val();
        });
        $(function () {
            $("select.select_menu").selectbox();
        });
        $('#nestable').on('click', '.btn-default, .btn-Form', function () {
            if ($(this).children().hasClass('glyphicon-plus')) {
                $(this).parents('li.dd-item:first').after(newElement);
            }

            /*delete buton*/
            if (($(this).children().hasClass('glyphicon-trash')) && (confirm(textConfirm))) {
                $(this).parents('li.dd-item:first').remove();
            }
            /*show buton*/
            if ($(this).children().hasClass('glyphicon-eye-open')) {
                if (!$(this).hasClass('disabled')) {
                    $(this).addClass('disabled');
                    $(this).parents('li.dd-item:first').find('.glyphicon-eye-close').parent().removeClass('disabled');
                    $(this).parents('li.dd-item:first').attr("data-hide", "").data("hide", "");
                }
            }
            /*hide buton*/
            if ($(this).children().hasClass('glyphicon-eye-close')) {
                if (!$(this).hasClass('disabled')) {
                    $(this).addClass('disabled');
                    $(this).parents('li.dd-item:first').find('.glyphicon-eye-open').parent().removeClass('disabled');
                    $(this).parents('li.dd-item:first').attr("data-hide", "disabled").data("hide", "disabled");
                }
            }
            /*button edit*/
            if ($(this).children().hasClass('glyphicon-pencil')) {
                nameItem = $(this).parents('li.dd-item:first').find('.text-group div.name-item span:first').text();
                urlItem = $(this).parents('li.dd-item:first').find('.text-group div.url-item span:first').text();
                var EditorForm =
                    '<div class="noDrag-btn input-Form-group">' +
                    '<input type="text" class="text-input form-control" placeholder="Name" value="' + nameItem.trim() + '"> ' +
                    '<input type="text" class="url-input form-control" placeholder="URL" value="' + urlItem.trim() + '"> ' +
                    '<div class="btn-group-form" style=""> ' +
                    '<div class="btn-Form" ><span class="glyphicon glyphicon-ok"></span></div> ' +
                    '<div class="btn-Form" ><span class="glyphicon glyphicon-remove"></span></div> ' +
                    '</div> ' +
                    '</div>' +
                    '<div style="background: rgba(248,80,50,1);' +
                    'border-radius: 4px;position: absolute;left:0px; top:0px; width: 100%;height: 34px;opacity: 0.5; z-index: 3;"></div>';
                $(this).parents('li.dd-item:first').find('.text-group:first').removeClass('dd-dragel').html(EditorForm);
            }

            /*form 'ok'*/
            if ($(this).children().hasClass('glyphicon-ok')) {
                var NameItem2 = $(this).parents('li.dd-item:first').find('.text-group div .text-input').val();
                var UrlItem2 = $(this).parents('li.dd-item:first').find('.text-group div .url-input').val();
                var TextItems =
                    '<div class="drag-btn name-item"><span>' + NameItem2.trim() + '</span></div> ' +
                    '<div class="drag-btn url-item"><span>' + UrlItem2.trim() + '</span></div>';
                $(this).parents('li.dd-item:first')
                    .find('.text-group:first')
                    .addClass('dd-dragel')
                    .html(TextItems)
                    .parents('li.dd-item:first')
                    .attr("data-text", NameItem2.trim())
                    .data("text", NameItem2.trim())
                    .attr("data-link", UrlItem2.trim())
                    .data("link", UrlItem2.trim());
            }

            /*form 'cancel'*/
            if ($(this).children().hasClass('glyphicon-remove')) {
                var TextItemsNot =
                    '<div class="drag-btn name-item"><span>' + nameItem.trim() + '</span></div> ' +
                    '<div class="drag-btn url-item"><span>' + urlItem.trim() + '</span></div>';
                $(this).parents('li.dd-item:first').find('.text-group:first').addClass('dd-dragel').html(TextItemsNot);
            }
            updateOutput($('#nestable').data('output', $('#nestable-output-change')));
        });


        var updateOutput = function (e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');
            if (window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize')));
            } else {
                output.val('JSON browser support required for this script.');
            }
        };

        // activate Nestable for list 1
        $('#nestable').nestable({
                listNodeName: 'ol',
                itemNodeName: 'li',
                rootClass: 'dd',
                listClass: 'dd-list',
                itemClass: 'dd-item',
                dragClass: 'dd-dragel',
                handleClass: 'drag-btn',
                collapsedClass: 'dd-collapsed',
                placeClass: 'dd-placeholder',
                noDragClass: 'noDrag-btn',
                emptyClass: 'dd-empty',
                expandBtnHTML: '<button data-action="expand" type="button">Expand</button>',
                collapseBtnHTML: '<button data-action="collapse" type="button">Collapse</button>',
                group: 1,
                maxDepth: 5,
                threshold: 10,
                dragStartEvent: function(e){
                    if($(e).parents('li.dd-item:first').find('div.input-Form-group').length > 0){
                        var TextItemsNot =
                         '<div class="drag-btn name-item"><span>' + nameItem.trim() + '</span></div> ' +
                         '<div class="drag-btn url-item"><span>' + urlItem.trim() + '</span></div>';
                         $(e).parents('li.dd-item:first').find('.text-group:first').addClass('dd-dragel').html(TextItemsNot);
                    }
                }
            })
            .on('change', updateOutput);

        // output initial serialised data


        updateOutput($('#nestable').data('output', $('#nestable-output-change')));


        $('#exp-collapser').on('change', function (e) {
            var checked = e.target.checked;
            if (checked === false) {
                $('.dd').nestable('expandAll');
            } else {
                $('.dd').nestable('collapseAll');
            }
        });

        $(function () {
            var switcherEl = $('input#exp-collapser').switcher({
                style: "nestable",
                selected: false,
                language: "ru",
                disabled: false,
                copy: {
                    ru: {
                        yes: expand,
                        no: collapse
                    }
                }
            });
        });
    });
</script>