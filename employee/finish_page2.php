<?php
require_once("includes/config.php");
#もしセッションにログインしたキーがなければ
if (!isset($_SESSION["userLoggedIn"])){
	header("Location: login.php");
}
if (isset($_POST["submitButton"])){
    header("Location: manager_2.php");
}
?>

<!DOCTYPE html>
<html><head>
        <title>Welcome to SanShisei</title>
        <link rel="stylesheet" type="text/css" href="assets/style/style.css">
    </head>
    <body class="finish_body">
    <form method="POST" class="finish_form">
        <h3>社員管理システム</h3>
        <div><b> すべてが完了しました</b></div>
        <input type="submit" name="submitButton" value="社員一覧に戻る" class="finishSubmit">  
    </form>
    
</body></html>