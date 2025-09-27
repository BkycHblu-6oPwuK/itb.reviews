<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()) {
    return;
}

if ($errorException = $APPLICATION->getException()) {
    CAdminMessage::showMessage(
        Loc::getMessage('REVIEWS_INSTALL_FAILED') . ': '.$errorException->GetString()
    );
} else {
    CAdminMessage::showNote(
        Loc::getMessage('REVIEWS_INSTALL_SUCCESS')
    );
}
?>

<!-- выводим кнопку для перехода на страницу модулей, мы и так находимся на этой странице но с выведенным файлом, значит просто получаем текущую директорию для перенаправления -->
<form action="<?= $APPLICATION->GetCurPage() ?>">
    <!-- в форме обязательно должно быть поле lang, с айди языка, чтобы язык не сбросился -->
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <!-- MOD_BACK - системная языковая переменная для возврата -->
    <input type="submit" name="" value="<?= Loc::getMessage("MOD_BACK") ?>">
</form>