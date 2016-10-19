<?php

/**
 * 許可したHtmlタグだけエスケープせず出力する。他のタグはエスケープ.
 *
 * @param  string $sValue 変換する文字列.
 * @param  array $arrAllowTag 許可するHtmlタグを格納した配列.
 * @return string              変換した文字列.
 */
function smarty_modifier_htmlescape($sValue, $arrAllowTag = array())
{
    $sValue = htmlspecialchars($sValue);

    if (count($arrAllowTag) == 0) return $sValue;

    foreach ($arrAllowTag as $sTag) {
        if (strpos($sTag, '/') === false) {
            $sValue = preg_replace_callback("/&lt;\/?" . $sTag . "( .*?&gt;|\/?&gt;)/i", "htmlescape_unhtmlescape", $sValue);
        }
    }
    return $sValue;
}

/**
 * タグを変換する.
 *
 * @param  string $sValue 変換する文字列.
 * @return string         変換した文字列.
 */
function htmlescape_unhtmlescape($sValue)
{
    $sString = $sValue[0];
    $sString = str_replace("&lt;", "<", $sString);
    $sString = str_replace("&gt;", ">", $sString);
    $sString = str_replace("&quot;", "\"", $sString);
    return $sString;
}

?>