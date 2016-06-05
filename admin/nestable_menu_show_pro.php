<?
define("ADMIN_MODULE_NAME", "web38.nestablemenupro");


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . ADMIN_MODULE_NAME . "/include.php");

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Web\Uri;
use Bitrix\Web38\Nestablemenupro\SettingsMenuTable;

$APPLICATION->SetAdditionalCSS('/bitrix/css/' . ADMIN_MODULE_NAME . '/style.css');
$APPLICATION->SetAdditionalCSS('/bitrix/css/' . ADMIN_MODULE_NAME . '/jquery.selectbox.css');
$APPLICATION->SetAdditionalCSS('/bitrix/css/' . ADMIN_MODULE_NAME . '/switcher.css');
Asset::getInstance()->addJs('/bitrix/js/' . ADMIN_MODULE_NAME . '/jquery-2.1.4.min.js');
Asset::getInstance()->addJs('/bitrix/js/' . ADMIN_MODULE_NAME . '/jquery.nestable.js');
Asset::getInstance()->addJs('/bitrix/js/' . ADMIN_MODULE_NAME . '/jquery.selectbox-0.2.js');
//Asset::getInstance()->addJs('/bitrix/js/' . ADMIN_MODULE_NAME . '/underscore-min.js');
Asset::getInstance()->addJs('/bitrix/js/' . ADMIN_MODULE_NAME . '/switcher.js');
$request = Application::getInstance()->getContext()->getRequest();
Loc::loadMessages(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("subscribe");

// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
// ******************************************************************** //
//                ОБРАБОТКА ИЗМЕНЕНИЙ ФОРМЫ                             //
// ******************************************************************** //
$ID = intval($ID);
$message = null;
$bVarsFromForm = false;
$strError = null;
if (
    $REQUEST_METHOD == "POST" // проверка метода вызова страницы
    && $POST_RIGHT == "W"          // проверка наличия прав на запись для модуля
    && check_bitrix_sessid()     // проверка идентификатора сессии
) {
    $add_new = htmlspecialcharsEx($request->getPost("add_new"));
    $del_menu = htmlspecialcharsEx($request->getPost("del_menu"));
    if ($save != "" || $apply != "") { // проверка нажатия кнопок "Сохранить" и "Применить"
       $jsonMenu = $request->getPost("json-menu");
       $menuID = $request->getPost("menu");
        if (empty($jsonMenu)) $strError = Loc::GetMessage("FRIMKO_NESTABLEMENU_EMPTY");


        if (!$strError) {
            $result = SettingsMenuTable::update($menuID, array(
                'DATA' => $jsonMenu
            ));

            if ($result->isSuccess()) {
                $message = Loc::GetMessage("FRIMKO_NESTABLEMENU_SUCCESS");
            }
        }
    } elseif (!empty($add_new)) {
        $result = SettingsMenuTable::add(array(
            'NAME' => $add_new,
            'DATA' => CNestablemenu::getDefaultItem()
        ));
        if ($result->isSuccess()) {
            $id = $result->getId();
            $uriString = $request->getRequestUri();
            $uri = new Uri($uriString);
            $uri->deleteParams(array("menu", "add_menu"));
            $uri->addParams(
                array(
                    "menu" => $id,
                    "add_menu" => 1,
                )
            );

            echo json_encode(
                array(
                    'success' => true,
                    'url' => $uri->getUri()
                )
            );
            return true;
        } else {
            echo json_encode(
                array(
                    'error' => true,
                    'text' => 'database available'
                )
            );
            return true;
        }
    } elseif (!empty($del_menu)) {
        $result = SettingsMenuTable::delete($del_menu);
        if ($result->isSuccess()) {
            $uriString = $request->getRequestUri();
            $uri = new Uri($uriString);
            $result = SettingsMenuTable::getList();
            if ($arrSelect = $result->fetch()) {
                $id = $arrSelect['ID'];
                $uri->deleteParams(array("menu", "delete_menu"));
                $uri->addParams(
                    array(
                        "menu" => $id,
                        "delete_menu" => 1,
                    )
                );
                echo json_encode(
                    array(
                        'success' => true,
                        'url' => $uri->getUri()
                    )
                );
                return true;
            }
        } else {
            echo json_encode(
                array(
                    'error' => true,
                    'text' => 'database available'
                )
            );
            return true;
        }
    }

}
// ******************************************************************** //
//                КОНЕЦ ОБРАБОТКИ ИЗМЕНЕНИЙ ФОРМЫ                       //
// ******************************************************************** //

$APPLICATION->SetTitle(Loc::GetMessage("FRIMKO_NESTABLEMENU_TITLE"));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

// если есть сообщения об ошибках или об успешном сохранении - выведем их.

if ($strError) {
    $aMsg = array();
    $arrErr = explode("<br />", $strError);
    reset($arrErr);
    while (list(, $err) = each($arrErr)) $aMsg[]['text'] = $err;

    $e = new CAdminException($aMsg);
    $GLOBALS["APPLICATION"]->ThrowException($e);
    $messageErr = new CAdminMessage(Loc::GetMessage("FRIMKO_NESTABLEMENU_ERROR"), $e);
    echo $messageErr->Show();
}
if ($message) {
    CAdminMessage::ShowMessage(array("MESSAGE" => Loc::GetMessage("FRIMKO_NESTABLEMENU_SUCCESS"), "TYPE" => "OK"));
}
if ($request->getQuery("add_menu")) {
    CAdminMessage::ShowMessage(array("MESSAGE" => Loc::GetMessage("FRIMKO_NESTABLEMENU_ADD_MENU"), "TYPE" => "OK"));
}
if ($request->getQuery("delete_menu")) {
    CAdminMessage::ShowMessage(array("MESSAGE" => Loc::GetMessage("FRIMKO_NESTABLEMENU_DELETE_MENU"), "TYPE" => "OK"));
}
/*сформируем список закладок*/

$aTabs = array(
    array('DIV' => 'edit1', 'TAB' => Loc::GetMessage("FRIMKO_NESTABLEMENU_TAB"), 'TITLE' => Loc::GetMessage("FRIMKO_NESTABLEMENU_TAB_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>

<form novalidate method="POST" ENCTYPE="multipart/form-data" name="post_form">

<?
echo bitrix_sessid_post();
$tabControl->Begin();
$tabControl->BeginNextTab(); ?>
    <td>

        <?= BeginNote(); ?>

        <span class="required">*</span><?= Loc::GetMessage("FRIMKO_NESTABLEMENU_INFO") ?>

        <? echo EndNote(); ?>
        <? /*грузим внутренности */
        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/web38.nestablemenupro/admin/template.php");
        ?>
    </td>

<?
$back_url = $request->getQuery("back_url");
if(empty($back_url)) $back_url = "/bitrix/admin/";
$tabControl->Buttons(
    array(
        "disabled" => ($POST_RIGHT < "W"),
        "back_url" => $back_url,
    )
);

$tabControl->EndTab();


?>


<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>