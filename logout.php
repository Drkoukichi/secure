<?php
session_start();

// セッションを破棄
session_destroy();

// ログインページにリダイレクト
header("Location: index.php");
exit;
?>
