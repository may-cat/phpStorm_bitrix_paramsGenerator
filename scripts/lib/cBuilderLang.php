<?

/**
 * Class CBuilderLang
 * Класс взят "Как есть" из модуля bitrix:mpbuilder
 * За исключением строки 326!!!!!!!!!
 */

class CBuilderLang
{
    function __construct($m_dir, $file, $lang_file)
    {
        $this->m_dir= $m_dir;
        $this->file = $file;
        if (!$str = file_get_contents($m_dir.$this->file))
            return false;
        if (GetStringCharset($str) == 'utf8')
            $str = $GLOBALS['APPLICATION']->ConvertCharset($str, 'utf8', 'cp1251');
        $this->str = $str;
        $this->lang_file = $lang_file;
        $this->bSiteUTF = defined('BX_UTF') && BX_UTF;

        $this->InPhp = '';
        $this->InHtml = 'InText';
        $this->InJs = '';
        $this->strQuoted = '';
        $this->strResultScript = '';

        if (file_exists($m_dir.$lang_file))
        {
            $str = file_get_contents($m_dir.$lang_file);
            if (GetStringCharset($str) == 'utf8')
            {
                $str = $GLOBALS['APPLICATION']->ConvertCharset($str, 'utf8', 'cp1251');
                file_put_contents($m_dir.$lang_file, $str);
            }
            include($m_dir.$lang_file);
            $this->MESS = $MESS;
        }
        else
        {
            if (!defined('BX_DIR_PERMISSIONS'))
                define('BX_DIR_PERMISSIONS', 0755);
            if (!file_exists($dir = dirname($m_dir.$lang_file)))
                mkdir($dir, BX_DIR_PERMISSIONS, true);
            $this->MESS = array();
        }
    }

    function Parse()
    {
        if (function_exists('mb_orig_strlen'))
            $l = mb_orig_strlen($this->str);
        elseif (function_exists('mb_strlen'))
            $l = mb_strlen($this->str, 'latin1');
        else
            $l = strlen($this->str);

        for($i=0;$i<$l;$i++)
        {
            $this->pos = $i;
            if (function_exists('mb_orig_substr'))
                $c = mb_orig_substr($this->str, $i, 1);
            elseif (function_exists('mb_substr'))
                $c = mb_substr($this->str, $i, 1, 'latin1');
            else
                $c = substr($this->str, $i, 1);

            if ($this->InPhp) // PHP
            {
                if ($Esc)
                    $Esc = 0;
                elseif ($this->InPhp == 'InDoubleQuotes' && $c == '"')
                {
                    $bSkipNext = $this->EndQuotedString();
                    $this->InPhp = 'InCode';
                }
                elseif ($this->InPhp == 'InSingleQuotes' && $c == "'")
                {
                    $bSkipNext = $this->EndQuotedString();
                    $this->InPhp = 'InCode';
                }
                elseif ($this->InPhp == 'InMultiLineComment')
                {
                    if ($prev_c.$c == '*/')
                        $this->InPhp = 'InCode';
                }
                elseif (($this->InPhp == 'InCode' || $this->InPhp == 'InLineComment') && $prev_c.$c == '?'.'>')
                    $this->InPhp = '';
                elseif ($this->InPhp == 'InLineComment')
                {
                    if ($c == "\n")
                        $this->InPhp = 'InCode';
                }
                elseif ($this->InPhp == 'InCode')
                {
                    if ($c == '#' || $prev_c.$c == '//')
                        $this->InPhp = 'InLineComment';
                    elseif ($prev_c.$c == '/*')
                        $this->InPhp = 'InMultiLineComment';
                    elseif ($c == '"')
                        $this->InPhp = 'InDoubleQuotes';
                    elseif ($c == "'")
                        $this->InPhp = 'InSingleQuotes';
                }
                elseif ($this->InPhp == 'InSingleQuotes' || $this->InPhp == 'InDoubleQuotes')
                {
                    if ($c == '\\')
                        $Esc = 1;
                }
            }
            else // HTML
            {
                if ($prev_c.$c == '<?')
                {
                    $this->InPhp = 'InCode';
                    $this->strResultScript .= $this->strLowPrefix;
                    $this->strLowPrefix = '';
                    $this->InHtml = $this->InHtmlLast;
                }
                elseif ($this->InJs) // JavaScript || CSS
                {
                    if ($this->InJs == 'InStyle')
                    {
                        if ($prev_c.$c == '</')
                            $this->InJs = '';
                    }
                    elseif ($this->InJs == 'InLineComment')
                    {
                        if ($c == "\n")
                            $this->InJs = 'InCode';
                    }
                    elseif ($this->InJs == 'InMultiLineComment')
                    {
                        if ($prev_c.$c == '*/')
                            $this->InJs = 'InCode';
                    }
                    elseif ($this->InJs == 'InCode')
                    {
                        if ($prev_c.$c == '</')
                            $this->InJs = '';
                        elseif ($c == '"')
                            $this->InJs = 'InDoubleQuotes';
                        elseif ($c == "'")
                            $this->InJs = 'InSingleQuotes';
                        elseif ($prev_c.$c == '//')
                            $this->InJs = 'InLineComment';
                        elseif ($prev_c.$c == '/*')
                            $this->InJs = 'InMultiLineComment';
                    }
                    else // InQuotes
                    {
                        if ($Esc)
                            $Esc = 0;
                        elseif ($c == '\\')
                            $Esc = 1;
                        elseif ($this->InJs == 'InSingleQuotes')
                        {
                            if ($c == "'")
                            {
                                $this->EndQuotedString();
                                $this->InJs = 'InCode';
                            }
                        }
                        elseif ($this->InJs == 'InDoubleQuotes')
                        {
                            if ($c == '"')
                            {
                                $this->EndQuotedString();
                                $this->InJs = 'InCode';
                            }
                        }
                    }
                }
                else // Pure HTML
                {
                    if ($this->InHtml == 'InTagName')
                    {
                        if ($c == ' ' || $c == "\t" || $c == '>')
                        {
                            if ($tag == 'script')
                            {
                                $this->InJs = 'InCode';
                                $this->InHtml = 'InText';
                            }
                            elseif ($tag == 'style')
                            {
                                $this->InJs = 'InStyle';
                                $this->InHtml = 'InText';
                            }
                            elseif ($c == '>')
                                $this->InHtml = 'InText';
                            else
                                $this->InHtml = 'InTag';
                        }
                        else
                            $tag .= strtolower($c);
                    }
                    elseif ($this->InHtml == 'InTag' && $c == '>')
                        $this->InHtml = 'InText';
                    elseif ($this->InHtml == 'InTag' && $c == "'")
                        $this->InHtml = 'InSingleQuotes';
                    elseif ($this->InHtml == 'InTag' && $c == '"')
                        $this->InHtml = 'InDoubleQuotes';
                    elseif ($this->InHtml == 'InSingleQuotes' && $c == "'")
                    {
                        $this->EndQuotedString();
                        $this->InHtml = 'InTag';
                    }
                    elseif ($this->InHtml == 'InDoubleQuotes' && $c == '"')
                    {
                        $this->EndQuotedString();
                        $this->InHtml = 'InTag';
                    }
                    elseif ($this->InHtml == 'InText' && $c == '<')
                    {
                        $this->EndQuotedString();
                        $this->InHtmlLast = $this->InHtml;
                        $this->InHtml = 'InTagName';
                        $tag = '';
                    }
                }
            }
            $prev_c = $c;

            if (!$bSkipNext && !$this->Collect($c))
                $this->strResultScript .= $c;
            $bSkipNext = 0;
        }
        $this->strResultScript .= $this->strLowPrefix;
    }

    function Collect($c)
    {
        $bCollect = strpos($this->InHtml.$this->InJs.$this->InPhp, 'Quotes') || ($this->InHtml == 'InText' && !$this->InJs && !$this->InPhp);
        if ($bCollect)
        {
            if (($o = ord($c)) > 127)
                $this->bTranslate = 1;
            if ($this->bTranslate)
            {
                if ($c == '<' || $o <= 127 && $this->strLow)
                {
                    $this->strLow .= $c;
                }
                else
                {
                    $this->strQuoted .= $this->strLow.$c;
                    $this->strLow = '';
                }
            }
            else
                $this->strLowPrefix .= $c;
            return true;
        }
        return false;
    }

    function EndQuotedString()
    {
        $bCutRight = strlen($this->strLow);

        if ($strMess = $this->strQuoted.($bCutRight ? '' : $this->strLow))
        {
            $key = $this->GetLangKey($strMess);
            $this->MESS[$key] = $strMess;
            $prefix = '<'.'?=';
            $postfix = '?'.'>';
            if ($this->InPhp)
            {
                $quote = $this->InPhp == 'InSingleQuotes' ? "'" : '"';
                if ($this->strLowPrefix == "'" || $this->strLowPrefix == '"') // delete quotes
                {
                    $prefix = '';
                    $this->strLowPrefix = '';
                }
                else
                    $prefix = $quote.'.';
                $postfix = $bCutRight ? ".".$quote : "";
            }
            $this->strResultScript .= $this->strLowPrefix.$prefix.'GetMessage'.($this->InJs ? 'JS' : '').'("'.$key.'")'.$postfix.($bCutRight ? $this->strLow : '');

            $this->bTranslate = 0;
            $this->strQuoted = '';
            $this->strLow = '';
            $this->strLowPrefix = '';

            return !$bCutRight; // true => skip next quote
        }

        $this->strResultScript .= $this->strLowPrefix;
        $this->strLowPrefix = '';
    }

    function GetLangKey($strMess)
    {
        if (is_array($this->MESS))
            foreach($this->MESS as $key => $val)
                if ($val == $strMess)
                    return $key;

        if (function_exists('mb_orig_substr'))
            $key = mb_orig_substr($strMess,0,20);
        elseif (function_exists('mb_substr'))
            $key = mb_substr($strMess,0,20,'latin1');
        else
            $key = substr($strMess,0,20);

        $key = preg_replace("/[^\xa8\xb8\xc0-\xdf\xe0-\xff]/",' ',$key);
        $key = trim($key);

        $from_u	= GetMessage("BITRIX_MPBUILDER_YCUKENGSSZHQFYVAPROL");
        $to 	= 'YCUKENGSSZHQFYVAPROLDJEACSMITQBUEEYCUKENGSSZHQFYVAPROLDJEACSMITQBU';

        static $from;
        if (!$from)
        {
            if ($this->bSiteUTF)
                $from = $GLOBALS['APPLICATION']->ConvertCharset($from_u, 'utf8', 'cp1251');
            else
                $from = $from_u;
        }

        $key = strtr($key,$from,$to);
        $key = preg_replace('/ +/','_',$key);

        $key = NotaImTranslite($key); ///////////////////////////////////////////////////// @notice: CHANGE ///////////////////////////////////////

        $new_key = $this->strLangPrefix.$key;

        while($this->MESS[$new_key] && $this->MESS[$new_key] != $strMess)
            $new_key = $this->strLangPrefix.$key.(++$i);

        return $new_key;
    }

    function Save()
    {
        $str = "<"."?\n";
        foreach($this->MESS as $key=>$val)
            $str .= '$MESS["'.$key.'"] = "'.str_replace('"','\\"',str_replace('\\','\\\\',$val)).'";'."\n";
        $str .= "?".">";

        if ($this->bSiteUTF)
            $str = $GLOBALS['APPLICATION']->ConvertCharset($str, 'cp1251', 'utf8');

        if (!file_put_contents($this->m_dir.$this->lang_file, $str))
            return false;

        $prefix = '';
        if (preg_match('#^/admin#', $this->file) && !preg_match('/(require|include).+prolog_admin/', $this->strResultScript))
            $prefix = '<'.'?php'."\n".
                'require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");'."\n".
                'IncludeModuleLangFile(__FILE__);'."\n".
                '?'.'>';

        if ($this->bSiteUTF)
            $this->strResultScript = $GLOBALS['APPLICATION']->ConvertCharset($this->strResultScript, 'cp1251', 'utf8');

        if (!file_put_contents($this->m_dir.$this->file, $prefix.$this->strResultScript))
            return false;

        return true;
    }
}
?>