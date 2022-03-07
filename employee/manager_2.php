<?php
require_once("includes/config.php");
require_once("includes/classes/Member.php");

$member = new Member($connection);
$result=$member->getEmployeeDetail();
unset( $_SESSION["emply_id"] );
unset( $_SESSION["id"] );

#もしセッションにログインしたキーがなければ
if (!isset($_SESSION["userLoggedIn"])){
	header("Location: login.php");
}

#検索ボタン
if (isset($_POST["emly_search"])){
  $query_str=$member->search($_POST);
  if ($query_str){
    $result=$member->takeData($query_str);
  }
}

#cancelボタン
if (isset($_POST["cancel"])){
  $_POST=array();
}

#ソートボタン
$sort_items=["imageAs","imageDes","nameAs","nameDes","genderAs","genderDes","departmentAs"
,"departmentDes","birthAs","birthDes","emailAs","emailDes","phone_numAs",
"phone_numDes","addressAs","addressDes","educationAs","educationDes","specialtyAs","specialtyDes"];
foreach($sort_items as $item){
  if (isset($_POST[$item])){
    #検索した状態なのかどうか
    $query_str=$member->search($_POST);
    #echo $query_str;
    $query_str=$member->item_sort($query_str,$item);
    $result = $member->takeData($query_str);
  }
}
#更新
if (isset($_POST["update"])){
  $_SESSION["emply_id"]=$_POST["emply_id"];
  header("Location:register_2.php");
}

#画像アップロード
if (isset($_POST["image_upload"])){
  $_SESSION["emply_id"]=$_POST["emply_id"];
  header("Location:register_2.php");
}

#削除
if(isset($_POST["delete"])){
  $member->deleteEmployee($_POST["emply_id"]);
  $result=$member->getEmployeeDetail();
  
}

function getInputValue($name){
  if(isset($_POST[$name])){
    echo ' value='.$_POST[$name];
  }
}
function getSelectValue($name,$value){
  if(isset($_POST[$name])&&$_POST[$name]==$value){
    echo "selected";
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>サンシセイ</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- BootstrapのCSS読み込み -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
      function delet_confirm() {
        var select = confirm("本当に削除しますか？");
        return select;
      }</script>
</script>
  </head>
  <body>
  <!--row-1-->
  <div class="container-fluid	manager_header">
  <div class="row">
    <div class="col-sm-3 header-text" ><a href="./register_2.php" >新規登録</a></div>
    <div class="col-sm-6" ><b>社員一覧</b></div>
    <div class="col-sm-3 header-text" ><a href="./login.php" >ログアウト</a></div>
  </div>
  </div>
  <!--row-2-->
  <form method="POST">
  <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3">
          <label>姓名<input type="text" name="emly_name" class="mana_serch" <?php getInputValue("emly_name");?>></label>
        </div>
        <div class="col-sm-3" >
          <label>性別<select name="gender" class="mana_serch">
            <option value=""></option>
            <option value="女" <?php getSelectValue("gender","女");?>>女</option>
            <option value="男" <?php getSelectValue("gender","男");?>>男</option>
          </select></label></div>
        <div class="col-sm-3" >
          <label>部署<select name="department" class="mana_serch">
            <option value=""></option>
            <option value="プロデューサー部" <?php getSelectValue("department","プロデューサー部");?>>プロデューサー部</option>
            <option value="アイドル部" <?php getSelectValue("department","アイドル部");?>>アイドル部</option>
            <option value="未定" <?php getSelectValue("department","未定");?>>未定</option>
          </select></label></div>
          <div class="col-sm-3" >
            <label>電話番号<input type="text" name="phone_number" class="mana_serch" <?php getInputValue("phone_number"); ?>></label>
          </div>
      </div>
  </div>
  <!--row-3-->
  <div class="container-fluid	search_header">
    <div class="row justify-content-end">
      <div class="col-sm-2" ><input type="submit" name="emly_search" value="検索" class="searchButtom"></div>
      <div class="col-sm-1" ><input type="submit" name="cancel" value="キャンセル" class="searchButtom"></div>
   </div>
  </div>
  <!--appearence of detail-->
  <table class="table table-striped">
    <thead>
      <tr>
        <th>
          <label>画像</label><br>
          <input type="submit" class="sortSymbol" name="imageAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="imageDes" value="&#9661"></th>
        <th>
          <label>姓名</label><br>
          <input type="submit" class="sortSymbol" name="nameAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="nameDes" value="&#9661">
        </th>
        <th>
          <label>性別</label><br>
          <input type="submit" class="sortSymbol" name="genderAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="genderDes" value="&#9661">
        </th>
        <th>
          <label>部署</label><br>
          <input type="submit" class="sortSymbol" name="departmentAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="departmentDes" value="&#9661">
        </th>
        <th>
          <label>生年月日</label><br>
          <input type="submit" class="sortSymbol" name="birthAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="birthDes" value="&#9661">
        </th>
        <th>
          <label>メールアドレス</label></br>
          <input type="submit" class="sortSymbol" name="emailAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="emailDes" value="&#9661">
        </th>
        <th>
          <label>電話番号</label></br>
          <input type="submit" class="sortSymbol" name="phone_numAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="phone_numDes" value="&#9661">
        </th>
        <th>
          <label>住所</label></br>
          <input type="submit" class="sortSymbol" name="addressAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="addressDes" value="&#9661">
        </th>
        <th>
          <label>最終学歴</label></br>
          <input type="submit" class="sortSymbol" name="educationAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="educationDes" value="&#9661">
        </th>
        <th>
          <label>特技</label></br>
          <input type="submit" class="sortSymbol" name="specialtyAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="specialtyDes" value="&#9661">
        </th>
        <th>
        <div></div>
        </th>
      </tr>
    </thead>
  </form>
    <tbody>
    <!--社員一覧：-->
    <?php 
    
    foreach($result as $emply_detail){
      if ($emply_detail["delet"]){
        continue;
      }
      echo "<form method='POST'>";
      echo "<tr>";
      echo "<td class='photo_area'><img class='emply_photo' src=".$emply_detail["image"]."></td>";
      echo "<td>".$emply_detail["kanji_name"]."</td>";
      echo "<td>".$emply_detail["gender"]."</td>";
      echo "<td>".$emply_detail["department"]."</td>";
      echo "<td>".$emply_detail["birth"]."</td>";
      echo "<td>".$emply_detail["email"]."</td>";
      echo "<td>".$emply_detail["phone_number"]."</td>";
      echo "<td>".$emply_detail["address"]."</td>";
      echo "<td>".$emply_detail["education"]."</td>";
      echo "<td>".$emply_detail["specialty"]."</td>";
      echo "<td class='funcArea'>".'<input type="hidden" name="emply_id" value='.$emply_detail["emply_id"].'>
      <input type="submit" class="funcButtom" name="update" value="更新"></form>';
      echo "<form method='POST' onsubmit='return delet_confirm()';>";
      echo '<input type="hidden" name="emply_id" value='.$emply_detail["emply_id"].
      '><input type="submit" class="funcButtom" name="delete" value="削除"><br></form>';
      echo '<form method="POST">'.'<input type="hidden" name="emply_id" value='.$emply_detail["emply_id"].
      '><input type="submit" class="funcButtom" name="image_upload" value="画像アップロード">'."</form></td>";
      echo "</tr>";
    }
    ?>
    </tbody>
  </table>
  <!--footer-->
  <div class="container-fluid	manager_header">
  <div class="row">
    <div class="col-sm-1" ><b>サンシセイ</b></div>
  </div>
  </div>

</body>
</html>