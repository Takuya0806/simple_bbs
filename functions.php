<?php

// エスケープ処理の関数作成
function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// CSRF対策
function setToken() {
    $token = sha1(uniqid(mt_rand(), true));
    $_SESSION['token'] = $token;
}

function checkToken() {
    if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
        echo "不正な処理です。";
        exit;
    }
}