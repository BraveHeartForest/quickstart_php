<?php
// パスの結合ヘルパ
function join_paths(array $paths)
{
    //DIRECTORY_SEPARATORはファイルパスの区切り文字を返す定数です。
    return implode(DIRECTORY_SEPARATOR, $paths); //配列$pathsの全ての配列要素をファイルパスの区切り文字で連結したものを返します。
}

define('APP_ROOT', dirname(__FILE__));   //APP_ROOTを現在のファイル名として定義する。
define('LIB_ROOT', join_paths([APP_ROOT, 'lib']));    //LIB_ROOTを現在のファイルからlibファイルまでのパスとして定義する。
define('MODELS_ROOT', join_paths([APP_ROOT, 'models']));  //MODELS_ROOTを現在のファイルからmodelsファイルまでのパスとして定義する。

require_once join_paths([APP_ROOT, 'config', 'env.php']); //現在のファイルからconfigディレクトリのenv.phpを読み込む
require_once join_paths([LIB_ROOT, 'functions.php']);    //libディレクトリのfunctions.phpを読み込む。

//DB

function db()
{   //データベースに接続します。
    static $conn;

    if (!isset($conn)) {
        $db         = DB_DBNAME; //env.phpで定められていた定数。
        $host       = DB_HOSTNAME; //env.phpで定められていた定数。
        $username   = DB_USERNAME; //env.phpで定められていた定数。
        $password   = DB_PASSWORD; //env.phpで定められていた定数。

        try {
            $conn = new PDO("mysql:dbname=$db;host=$host", $username, $password);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            print 'データベースに接続できません！　アプリの設定を確認してください。';
            exit;
        }
    }

    return $conn;
}

//Session
require_once join_paths([LIB_ROOT, 'Session.php']);  //libディレクトリのSession.phpを読み込む。

function session($namespace = 'app')
{
    static $sessions;

    if (!isset($sessions[$namespace])) {
        $sessions[$namespace] = new Session($namespace);    //class Session['app']を作成
    }

    return $sessions[$namespace];
}

function csrf_field(Session $session)
{   //トークンの設置
    $name   =     $session->getRequestCsrfTokenKey();
    $token  =     $session->getCsrfToken();
    print '<input type="hidden" name="' . $name . '" value="' . h($token) . '">';
}
