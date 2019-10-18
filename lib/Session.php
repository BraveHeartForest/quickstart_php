<?php
class Session
{
    protected $bag;
    //$bagはSessionとその継承クラスでのみアクセス可能な変数

    public function __construct($namespace = 'app')
    {
        if (!session_id()) {
            //session_id()が存在しないならばsessionを開始する。
            session_start();
        }

        $this->bag = &$_SESSION[$namespace];
        //$bagに$_SESSION[$namespace]の値を代入。

        if (!isset($this->bag)) {
            //$_SESSION[$namespace]が存在しないならば
            //---トークンの生成を以下で行う。---
            $this->bag[$this->getAppDataKey()]   = [];
            //$_SESSION[$namespace]['app_data']を空にする。
            if (!$this->getCsrfToken()) {
                //'csrf_token'が存在しないならば、
                $this->bag[$this->getCsrfTokenKey()] = $this->generateCsrfToken();
                //$_SESSION[$namespace]['__csrf_token']にsha1(uniqid(rand(), true))を代入する。
            }
        }
    }

    public function getAppDataKey()
    {
        return 'app_data';
    }

    public function getCsrfTokenKey()
    {
        return 'csrf_token';
    }

    public function getRequestCsrfTokenKey()
    {
        return '__csrf_token';
    }

    public function generateCsrfToken()
    {
        return sha1(uniqid(rand(), true));
        //ランダムな文字列を生成する。詳しくはhttps://ysklog.net/php/2103.html
    }

    public function getCsrfToken()
    {
        return array_get($this->bag, $this->getCsrfTokenKey());
        //$_SESSION[$namespace][]から$_SESSION[$namespace]['csrf_token']を取り出して返す。
    }

    //綴りに注意
    public function verifyCsrfToken()
    {
        $request_token = request_get($this->getRequestCsrfTokenKey());  //$_POSTで送られてきたトークン？
        $valid_token   = $this->getCsrfToken(); //$_SESSIONに保存されているトークン？
        return $request_token === $valid_token; //あっていればTrue、そうでなければFalse
    }

    public function get($key, $default = null)
    {
        return array_get($this->bag[$this->getAppDataKey()], $key, $default);
        //$_SESSION[$namespace]['app_data'][$key]を取り出して返す。
    }

    public function set($key, $value)
    {
        return $this->bag[$this->getAppDataKey()][$key] = $value;
        //$_SESSION[$namespace]['app_data'][$key]に$valueを代入する。
    }

    //unsetは既に存在するのでun
    public function unset_key($key)
    {
        unset($this->bag[$this->getAppDataKey()][$key]);
        //$_SESSION[$namespace]['app_data'][$key]を消去する。
    }

    public function unsetAll()
    {
        $this->bag[$this->getAppDataKey()] = [];
        //$_SESSION[$namespace]['app_data']の中身を全消去する。
    }

    public function flash($key, $default)
    {
        //$errors変数を破棄します。
        $value = $this->get($key, $default);
        $this->unset_key($key);
        return $value;
    }
}
