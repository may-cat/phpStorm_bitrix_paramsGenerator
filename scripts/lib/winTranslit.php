<?
/**
 * �������, ������� ������������� �����.
 * ����� �����: ���� ���� ������ ���� � ��������� win-1251
 * @param $str
 * @param int $length
 * @return string
 */
function NotaImTranslite($str, $length=15)
{
    static $tbl= array(
        '�'=>'a', '�'=>'b', '�'=>'v', '�'=>'g', '�'=>'d', '�'=>'e', '�'=>'g', '�'=>'z',
        '�'=>'i', '�'=>'y', '�'=>'k', '�'=>'l', '�'=>'m', '�'=>'n', '�'=>'o', '�'=>'p',
        '�'=>'r', '�'=>'s', '�'=>'t', '�'=>'u', '�'=>'f', '�'=>'y', '�'=>'e', '�'=>'A',
        '�'=>'B', '�'=>'V', '�'=>'G', '�'=>'D', '�'=>'E', '�'=>'G', '�'=>'Z', '�'=>'I',
        '�'=>'Y', '�'=>'K', '�'=>'L', '�'=>'M', '�'=>'N', '�'=>'O', '�'=>'P', '�'=>'R',
        '�'=>'S', '�'=>'T', '�'=>'U', '�'=>'F', '�'=>'Y', '�'=>'E', '�'=>"yo", '�'=>"h",
        '�'=>"ts", '�'=>"ch", '�'=>"sh", '�'=>"shch", '�'=>"", '�'=>"", '�'=>"yu", '�'=>"ya",
        '�'=>"YO", '�'=>"H", '�'=>"TS", '�'=>"CH", '�'=>"SH", '�'=>"SHCH", '�'=>"", '�'=>"",
        '�'=>"YU", '�'=>"YA", '.'=>"", '&'=>"i", '"'=>"", ' '=>"_", '�'=>"", '�'=>"", '�'=>"",
        '('=>"", ')'=>"", '�'=>"-", ','=>"", '?'=>'', '!'=>''
    );
    $str=substr(trim(strtr($str, $tbl),'-_'),0,$length);
    return $str;
}