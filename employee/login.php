<?php
require_once("includes/config.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/classes/Account.php");

  unset( $_SESSION["userLoggedIn"] );
  $account = new Account($connection);

  if(isset($_POST["submitButton"])){
    $username = FormSanitizer::sanitizeFormString($_POST["username"]);
    $password = FormSanitizer::sanitizeFormString($_POST["password"]);
    $success = $account -> login($username,$password);

    if($success) {
       //セッションはconfig.phpを読み込むことで使える
       $_SESSION["userLoggedIn"] = $username;
       header("Location: manager_2.php");
    }
    else {
      // ①$alertにjavascriptのalert関数を代入する。
      $alert = "<script>alert('IDまたはパスワードが間違っています。');</script>";
      // ②echoで①を表示する
      echo $alert;
    }
  }

  function getInputValue($name) {
    if(isset($_POST[$name])) {
        echo $_POST[$name];
    }
 }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Welcome to SanShisei</title>
        <link rel="stylesheet" type="text/css" href="assets/style/style.css" />
    </head>
    <body class="login_body">
    <form method="POST" class="login_form">
            <h3>ログイン</h3>
            <label><input type="text" name="username" placeholder="ID"  class="login-item" value="<?php getInputValue("username"); ?>" required></label>
            <label><input type="password" name="password" placeholder="Password" class="login-item" required></label>
            <input type="submit" name="submitButton" value="ログイン" class="loginSubmit">
    </form>
    </body>
</html>