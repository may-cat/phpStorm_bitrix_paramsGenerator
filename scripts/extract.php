<?
include __DIR__.'/lib/winTranslit.php';
include __DIR__.'/lib/cLoc.php';
include __DIR__.'/lib/cBuilderLang.php';
include __DIR__.'/lib/CharsetConverter.php';
include __DIR__.'/lib/Emulation.php';

/**
 * Вопросы
 * 1. Обход файлов
 *  1.а. Получение дерева файлов
 *  1.б. Формирование путей к ланг-файлам на основе путей к обычным
 */


// $argv[1] - folder to search
ini_set("mbstring.func_overload", 2);
ini_set("mbstring.internal_encoding", 'UTF-8');
$m_dir = $argv[1].'/';


// @todo: Сделать обработку нескольких файлов!
$file = 'class.php';
$lang_file = 'lang/class.php';
$module_id = 'ETM'; // @todo: нужно брать всё-таки извне

// Формируем пути
/*
$arPaths = array();

$Directory = new RecursiveDirectoryIterator($m_dir);
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
foreach ($Regex as $info) {
    $fullpath = $info[0];
    $shortpath = substr($fullpath,strlen($m_dir),strlen($fullpath)-strlen($m_dir));
    $langpath = 'lang/'.$shortpath;
    $arPaths[] = array(
        'FILE' => $shortpath,
        'LANG' => $langpath
    );
}
print_r($arPaths);
die('x');
*/


if ($CBL = new CBuilderLang($m_dir, $file, $lang_file))
{
    $CBL->strLangPrefix = strtoupper(str_replace('.','_',$module_id)).'_';
    $CBL->Parse();
    if (!$CBL->Save())
        echo "FAIL1";
}
else
    echo "FAIL";

