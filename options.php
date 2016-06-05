<?
/**
 * Company developer: frimko
 * Developer: samokhvalov
 * Site: NaN
 * E-mail: ccc-car@yandex.ru
 * @copyright (c) 2015-2015 frimko
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Web38\Nestablemenupro\SettingsMenuTable;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $mid . '/include.php');

Loc::loadMessages(__FILE__);
Asset::getInstance()->addJs('/bitrix/js/' . ADMIN_MODULE_NAME . '/jquery-2.1.4.min.js');
$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();

$menuID = $request->getPost("menu");
$menuData = $request->getPost("json-menu");
$update = $request->getPost("Update");

if (strlen($update) > 0 && $USER->IsAdmin() && check_bitrix_sessid()) {
    if ($menuID) {
        $result = SettingsMenuTable::update($menuID, array(
            'DATA' => $menuData
        ));
    }
}

list($serializeData, $title) = '';
if ($arMenu = CNestablemenu::getArrTable()) {
    if ($menuID) {
        $serializeData = $arMenu[$menuID]['DATA'];
        $title = $arMenu[$menuID]['NAME'];
    } else {
        $menuID = reset($arMenu)['ID'];
        $serializeData = reset($arMenu)['DATA'];
        $title = reset($arMenu)['NAME'];
    }
}
$aTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::GetMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::GetMessage('MAIN_TAB_TITLE_SET') . ' - "' . $title . '"'),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>

<form name="menuOptions" method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialchars($mid) ?>&lang=<?= LANGUAGE_ID ?>">
    <?
    echo bitrix_sessid_post();
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <?= CNestablemenu::showSelect($menuID, 'menuOptions'); ?>
    </tr>
    <tr>
        <td valign='top' width='40%' class='field-name'>
            <label for='nestable_output_change'><?= Loc::GetMessage("FRIMKO_NESTABLEMENU_CODE_MENU"); ?></label>
        </td>
        <td valign='middle' width='60%'>
            <textarea id="nestable-output-change" style="width: 60%;font-size: 15px;"
                      name="json-menu"><?= $serializeData ?></textarea>
        </td>
    </tr>
    <? $tabControl->Buttons(); ?>
</form>
<input type='submit' <? if (!$USER->IsAdmin()) echo ' disabled'; ?> name='Update'
       value='<? echo Loc::GetMessage('BUTTON_SAVE') ?>' class="adm-btn-save">
<input type='reset' <? if (!$USER->IsAdmin()) echo ' disabled'; ?> name='reset'
       value='<? echo Loc::GetMessage('BUTTON_RESET') ?>' onClick='window.location.reload()'>
<?
$tabControl->EndTab();
$tabControl->End();
?>
