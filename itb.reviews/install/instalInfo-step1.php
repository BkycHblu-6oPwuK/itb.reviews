<?
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// проверка идентификатора сессии
if (!check_bitrix_sessid()) {
    return;
}
?>

<!-- выводим кнопку для перехода на страницу модулей, мы и так находимся на этой странице но с выведенным файлом, значит просто получаем текущую директорию для перенаправления -->
<form action="<?= $APPLICATION->GetCurPage() ?>">
    <!-- обязательное получение сессии -->
    <?= bitrix_sessid_post() ?>
    <!-- в форме обязательно должно быть поле lang, с айди языка, чтобы язык не сбросился -->
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <!-- айди модуля для установки -->
    <input type="hidden" name="id" value="itb.reviews">
    <!-- обязательно указывать поле install со значением Y, иначе просто перейдем на страницу модулей -->
    <input type="hidden" name="install" value="Y">
    <!-- определение шага установки модуля -->
    <input type="hidden" name="step" value="2">
    <!-- чекбокс для определния параметра -->
    <p>Внените ид инфоблоков</p>
    <p><input type="text" name="catalog" id="catalog" value="0" checked><label for="catalog">Каталог ид</label></p>
    <p><input type="text" name="offer" id="offer" value="0" checked><label for="offer">Каталог оффер ид</label></p>
    <!-- MOD_INSTALL - системная языковая переменная для кнопки установки -->
    <input type="submit" name="" value="<?= Loc::getMessage("MOD_INSTALL") ?>">
</form>