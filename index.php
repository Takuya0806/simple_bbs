<?php
session_start();

// 取得ファイルの指定
$dataFile = './bbs.dat';

require_once(__DIR__ . '/config.php');

$posts = "";

// 投稿されたらbbs.datに書き込む
if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['message']) &&
    isset($_POST['user'])) {

    checkToken();

    // フォームデータの取得
    $message = trim($_POST['message']); // trimを使い余分な空白を削除
    $user = trim($_POST['user']);

    // $messageが空でなければ書き込み
    if ($message !== '') {

        // $userが空なら'名無しさん'を代入
        $user = ($user === '') ? '名無しさん' : $user; // 三項演算子

        // 投稿内容にタブが入ってきたら、半角スペースに変換
        $message = str_replace("\t", ' ', $message);
        $user = str_replace("\t", ' ', $user);

        $postDate = date('Y-m-d H:i:s'); // 投稿日時を入力

        $newData = $message."\t".$user."\t".$postDate."\n"; // 投稿内容の代入

        // ファイルのオープン
        $fp = fopen($dataFile, 'a'); // 'a'で追記モード
        fwrite($fp, $newData); // bbs.datに内容の書き込み
        fclose($fp); // オープンしたファイルのクローズ

    } else {        
        setToken();
    }
    
    // 投稿内容を表示
    // ファイル全体を読み込み、配列に格納（第2引数は一番最後の改行を省略）
    $posts = file($dataFile, FILE_IGNORE_NEW_LINES);
    $posts = array_reverse($posts); // 投稿を新しい順に表示

}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>簡易掲示板</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <header>
        <h1>Simple BBS</h1>
    </header>
    <div id="container">
        <div class="form">
            <form action="" method="post">
                message：<input type="text" name="message">
                user：<input type="text" name="user">
                <input type="button" value="投稿" onclick="submit();">
                <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
            </form>
        </div>
        <div class="comment">
            <h2>投稿一覧（
                <?php if (is_array($posts)) : ?>
                    <?= count($posts); ?>
                <?php else : ?>
                    <?= $posts = 0; ?>
                <?php endif; ?>
                件）
            </h2>
            <ul>
                <?php if (is_array($posts)) : ?>
                    <?php foreach ($posts as $post) : ?>
                    <?php list($message, $user, $postDate) = explode("\t", $post); ?>
                        <li><?= h($message); ?> (<?= h($user); ?>) - <?= h($postDate); ?></li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li>投稿はまだありません。</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>