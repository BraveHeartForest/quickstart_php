<?php

function dd()
{
    echo '<pre>';
    $args = func_get_args();
    //関数の引数リストを配列として返します。つまり、$argsは配列です。
    foreach ($args as $arg) {
        //配列$argsの値を取り出して、型と値を表示します。
        var_dump($arg);
    }
    echo '</pre>';
    exit;
}

function array_get($array, $key, $default = null)
{
    if (is_array($array) && isset($array[$key])) {
        //$arrayが配列である＆$array[$key]が存在する、の２条件を満たしているならば、
        //$array[$key]の値を返す。
        return $array[$key];
    }
    //配列でないか$array[$key]が存在しないかのどちらかならばデフォルト値のnullを返す。
    return $default;
}

function request_get($key, $default = null)
{
    if (isset($_POST[$key])) {
        //$_POST[$key]が存在するならば、
        return array_get($_POST, $key, $default);
        //$_POST[$key]を返す。
    }
    if (isset($_GET[$key])) {
        //POSTと同様の処理を行う。
        return array_get($_GET, $key, $default);
    }
    //$_POST[$key]も$_GET[$key]も存在しないならnullを返す。
    return $default;
}

function h($string)
{
    return htmlspecialchars($string, ENT_QUOTES);
}

function quote_sql($string)
{
    $quote_str = '`';
    return $quote_str . str_replace($quote_str, '', $string) . $quote_str;
    //$stringに「`」が含まれているならば消去してそのあとの文を
    //`$string`という形で返す。

}

//お手本ではstring $sとされていたが、タイプヒンティングはここでは使えないのでエラーが発生。
function camelize($s)
{
    return str_replace([' ', '-', '_'], '', ucwords($s, ' -_'));
    //$s内部の「半角スペース」、「ハイフン」、「アンダーバー」で区切られた部分を大文字に変換する。
    //「半角スペース」、「ハイフン」、「アンダーバー」を消去したものを返す。
}

function redirect($url)
{
    header('Location: ' . $url);
    exit();
}
