<?php
class Validate
{
    public static function test($rules, $params, $messages = [])
    {
        $errors = [];
        //配列$errorsを空にする。
        foreach ($rules as $key => $rule_string) {
            //上記から$rulesは連想配列あるいは多次元配列であることが分かる
            $rule_list = explode('|', $rule_string);
            //「｜」で区切られている部分で$rule_stringを配列に変換。
            //$rule_stringは多分"a|b|c|d|e|..."みたいな形をしているのではないか？
            //$rule_listは一次元配列
            $value     = array_get($params, $key);
            //$params[$key]を順番に代入していく(foreachのため$valueは配列になる)
            $message_templates = array_get($messages, $key);
            //$messages[$key]を順番に代入していく。
            $error_messages = [];
            //配列$error_messagesを空にする。
            foreach ($rule_list as $rule) {
                $rule_parts  = explode(':', $rule);
                //$ruleを「：」で分割して配列に変換。
                $rule_name   = array_shift($rule_parts);
                //$rule_partsの最初の値を取り出して返します。$rule_partsは要素一つ分短くなり全要素は前にずれます。
                $rule_params = $rule_parts;
                //$rule_paramsは最初の値が無くなった$rule_partsです。
                $method = 'validate' . camelize('' . $rule_name);
                //$method="validate「''.$rule_nameを大文字にしたもの」"
                if (!static::$method($value, $rule_params)) {
                    $error_messages[$rule_name] = array_get($message_templates, $rule_name, $rule_name . 'のエラーが発生しました');
                }
            }
            if ($error_messages) {
                $errors[$key] = implode('/', $error_messages);
            }
        }
        return $errors;
    }
    public static function validateRequired($value)
    {
        return !!strlen($value);
    }
    public static function validateNotNumberOnly($value)
    {
        return !preg_match('/^[0-9０-９]+$/', strval($value));
    }
    public static function validateMax($value, $params)
    {
        if (!isset($params[0]) || !intval($params[0])) {
            throw new Exception('Validateのrule maxにはmax:255 のように文字数を指定してください！');
        }
        $max_length = $params[0];
        return mb_strlen($value) <= $max_length;
    }
}
