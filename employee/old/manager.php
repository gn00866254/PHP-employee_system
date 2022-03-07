<?php
require_once("includes/config.php");
require_once("includes/classes/Member.php");

$member = new Member($connection);
$result=$member->getEmployeeDetail();

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
  header("Location:register.php");
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
    <title>shisei_company</title>
    <link rel="stylesheet" type="text/css" href="assets/style/style.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- BootstrapのCSS読み込み -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </head>
  <body>
  <!--row-1-->
  <div class="container-sm">
    <div class="row">
          <div class="col-sm-3" ><a href="./register.php" >新規登録</a></div>
          <div class="col-sm-6" ><b>社員一覧</b></div>
          <div class="col-sm-3" ><a href="./login.php" >ログアウト</a></div>
    </div>
  </div>
  <!--row-2-->
  <div class="container-sm">
    <form method="POST">
      <div class="row">
        <div class="col-sm-3" ><label></label></div>
        <div class="col-sm-2" ><label>性別:<select name="gender" class="login-item">
                          <option value=""></option>
                          <option value="女" <?php getSelectValue("gender","女");?>>女</option>
                          <option value="男" <?php getSelectValue("gender","男");?>>男</option>
                          </select></label></div>
        <div class="col-sm-3" ><label>部署:<select name="department" class="login-item">
                          <option value=""></option>
                          <option value="プロデューサー部" <?php getSelectValue("department","プロデューサー部");?>>プロデューサー部</option>
                          <option value="アイドル部" <?php getSelectValue("department","アイドル部");?>>アイドル部</option>
                          <option value="未定" <?php getSelectValue("department","未定");?>>未定</option></select>
                        </label></div>
        <div class="col-sm-4" >
        <label>電話番号:<input type="text" name="phone_number" class="login-item" <?php getInputValue("phone_number"); ?>></label></div>
        <div class="row justify-content-end search">
          <div class="col-sm-1" ><input type="submit" name="emly_search" value="search"></div>
          <div class="col-sm-1" ><input type="submit" name="cancel" value="cancel"></div>
        </div>
      </div> 
  </div>
  <!--row-3-->
  <div class="container-sm itemName">
      <div class="row">
      <div class="col-sm-1 item" >
          <label>画像</label><br>
          <input type="submit" class="sortSymbol" name="imageAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="imageDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" >
          <label>姓名</label><br>
          <input type="submit" class="sortSymbol" name="nameAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="nameDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" >
          <label>性別</label><br>
          <input type="submit" class="sortSymbol" name="genderAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="genderDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" >
          <label>部署</label><br>
          <input type="submit" class="sortSymbol" name="departmentAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="departmentDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" >
          <label>生年月日</label><br>
          <input type="submit" class="sortSymbol" name="birthAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="birthDes" value="&#9661">
        </div>
        <div class="col-sm-2 item" >
          <label>メールアドレス</label></br>
          <input type="submit" class="sortSymbol" name="emailAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="emailDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" >
          <label>電話番号</label></br>
          <input type="submit" class="sortSymbol" name="phone_numAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="phone_numDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" >
          <label>住所</label></br>
          <input type="submit" class="sortSymbol" name="addressAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="addressDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" >
          <label>最終学歴</label></br>
          <input type="submit" class="sortSymbol" name="educationAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="educationDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" >
          <label>特技</label></br>
          <input type="submit" class="sortSymbol" name="specialtyAs" value="&#9651">
          <input type="submit" class="sortSymbol" name="specialtyDes" value="&#9661">
        </div>
        <div class="col-sm-1 item" ><label></label></div>
      </div>
    </form>
  </div>
  <!--appearence of detail-->
  <?php
  
  foreach($result as $emply_detail){
    if ($emply_detail["delet"]){
      continue;
    }
    echo '<div class="container-sm emply_detail">';
    echo '<form method="POST" class="show_form"><div class="row">';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["image"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["kanji_name"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["gender"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["department"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["birth"].'</label></br></div>';
    echo '<div class="col-sm-2 show_emply"><label>'.$emply_detail["email"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["phone_number"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["address"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["education"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply"><label>'.$emply_detail["specialty"].'</label></br></div>';
    echo '<div class="col-sm-1 show_emply">
    <input type="hidden" name="emply_id" value='.$emply_detail["emply_id"].'>
    <input type="submit" name="update" value="更新">
    <input type="submit" name="delete" value="削除"><br>
    <input type="submit" name="image_upload" value="画像アップロード"></div>';
    echo '</div></form></div>';
  }
  ?>
  <div class="container-sm">
    <div class="row">
      <div class="col-sm-1">end</br></div>
    </div>

  </div>
</body>
</html>