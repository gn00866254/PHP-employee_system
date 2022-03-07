<?php 
require_once("includes/config.php");

if (isset($_POST["registed"])){
    header("Location: manager.php");
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>shisei_company</title>
        <link rel="stylesheet" type="text/css" href="assets/style/style.css" />
    </head>
    <body>
        <form method="POST">
            <table class='tg' align="center">
            <tr>
                <th colspan=2 class="tg-span">
                    <b>完了</b>
                </th>
            <tr>
            <tr>
                <td class='tg-3 over'>
                    1 >操作はすでに完了しました<br>2>操作はすでに完了しました。しばらく待ちください.
                </td>
            <tr>
                <th colspan=2 class="tg-span">
                    <input type="submit" name="registed" value="完了">
                </th><tr>
            </table>
        </form>
</body>
</html>