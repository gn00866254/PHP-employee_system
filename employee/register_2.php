<?php 
require_once("includes/classes/Account.php");
require_once("includes/classes/Register.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/config.php");
require_once("includes/classes/Constants.php");
date_default_timezone_set("Asia/Tokyo");

#もしセッションにログインしたキーがなければ
if (!isset($_SESSION["userLoggedIn"])){
	header("Location: login.php");
}


$account = new Account($connection);
#0:新規登録　1:更新
$formTitle=["新規登録","更新"];
if (isset($_POST["titleIndex"])){
    $titleIndex=$_POST["titleIndex"];
}else{
    $titleIndex=0;
}

#リセット
if (isset($_POST["reset"])){
    $_POST=array();
}

$flag=0;
#更新から来たのなら
if (isset($_SESSION["emply_id"])&&empty($_POST["submitButton"])){
    $titleIndex=1;
    $_POST=$account->getInfo($_SESSION["emply_id"])[0];
    $education=explode("\n",$_POST["education"]);
    #Array ( [0] => 静宜大学 [1] => 日本語学科 )
    $_POST["school"]=$education[0];
    $_POST["major"]=$education[1];
    $_POST["file_path"]=$_POST["image"];
    $_SESSION["id"]=$_SESSION["emply_id"];
    unset( $_SESSION["emply_id"] );
    
}

#アップロードを押したら
if (isset($_POST["upload"])){
    $titleIndex=$_POST["titleIndex"];
    $birth=$_POST["birth_year"]."年".$_POST["birth_month"]."月".$_POST["birth_day"]."日";
    $_POST["birth"]=$birth;
    if (isset($_SESSION["id"])){
        $id=$_SESSION["id"];
    }else{
        $id=$account->getId();
    }
    $file_path=$account->validateImage($_FILES,$id);
    $_POST["file_path"]=$file_path;
}

#画面１で送信ボタンを押したら
if (isset($_POST["submitButton"])){
    $titleIndex=$_POST["titleIndex"];
    $kanji_name=FormSanitizer::sanitizeFormName($_POST["kanji_name"]);
    $furigana_name=FormSanitizer::sanitizeFormName($_POST["furigana_name"]);
    $department=FormSanitizer::sanitizeFormString($_POST["department"]);
    $gender=FormSanitizer::sanitizeFormString($_POST["gender"]);
    $birth=$_POST["birth_year"]."年".$_POST["birth_month"]."月".$_POST["birth_day"]."日";
    $_POST["birth"]=$birth;
    $email=FormSanitizer::sanitizeFormString($_POST["email"]);
    $password=FormSanitizer::sanitizeFormString($_POST["password"]);
    $password2=FormSanitizer::sanitizeFormString($_POST["password2"]);
    $phone_number=FormSanitizer::sanitizeFormString($_POST["phone_number"]);
    $address=FormSanitizer::sanitizeFormString($_POST["address"]);
    $school=FormSanitizer::sanitizeFormSchool($_POST["school"]);
    $major=FormSanitizer::sanitizeFormSchool($_POST["major"]);
    $education=$school.$major;
    $specialty=FormSanitizer::sanitizeFormString($_POST["specialty"]);
    $account->validatePasswords($password,$password2,$titleIndex,$_POST["temp_pw"],$_POST["temp_pw2"]);
    $account->validateEmails($email,$titleIndex,$_SESSION);
    if ($account->error_check()){
        $flag = 1;
    }
#押されたのは、確認だったら
}elseif (isset($_POST["submitCofirm"])){
    $titleIndex=$_POST["titleIndex"];
    $kanji_name=$_POST["kanji_name"];
    $furigana_name=$_POST["furigana_name"];
    $department=$_POST["department"];
    $gender=$_POST["gender"];
    $birth=$_POST["birth"];
    $email=$_POST["email"];
    $password=$_POST["password"];
    $phone_number=$_POST["phone_number"];
    $address=$_POST["address"];
    $education=$_POST["school"]."\n".$_POST["major"];
    $specialty=$_POST["specialty"];
    $file_path=$_POST["file_path"];
    
    if ($_POST["titleIndex"]==0){
        $account->register($password,$birth,$kanji_name,$furigana_name,
        $gender,$department,$email,$phone_number,$address,$education,$specialty,$file_path);
    
    }elseif($_POST["titleIndex"]==1){
        $account->update($titleIndex,$_SESSION["id"],$password,$_POST["temp_pw"],$birth,$kanji_name,$furigana_name,$gender,
        $department,$email,$phone_number,$address,$education,$specialty,$file_path);
    }

    #完了画面へ切り替え
    if ($account){
        header("Location: finish_page2.php");
    }
}

function getInputValue($name) {
    #textareaはvalueで設定するものではない
    if ($name=="specialty"&isset($_POST[$name])){
        return $_POST[$name];
    }
    if(isset($_POST[$name])) {
        return "value="."'$_POST[$name]'";
    }
}

function selectGender($name,$value){
    if(isset($_POST[$name])){
        if ( $_POST[$name]==$value){
            return 'checked';
        }
    }
}

function uploadImage($key){
    if(isset($_POST[$key])){
        $file_path=$_POST[$key];
        return "<img class='show_photo' src=$file_path>";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>サンシセイ</title>
        <link rel="stylesheet" type="text/css" href="assets/style/style.css" />
        <link rel="stylesheet" type="text/css" href="assets/style/style.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- BootstrapのCSS読み込み -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="validation.js"></script>
    </head>
    <body>
        <div class="container-fluid	manager_header">
            <div class="row">
                <div class="col-sm-1" ><b>サンシセイ</b></div>
                <div class="col-sm-1 header-text" ><a href="manager_2.php">戻る</b></a></div>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <table class='table table-striped w-50' align="center">
                <tr>
                     <?php
                    if($flag==0){
                        echo "<th colspan=2 class='tg-span'><b>";
                        echo $formTitle[$titleIndex];
                        echo "</b></th>";
                        echo "<input type='hidden' name='titleIndex' value=$titleIndex >";
                    }elseif($flag==1){
                        echo "<th colspan=2 class='tg-span'><b>---確定---</b></th>";
                        echo "<input type='hidden' name='titleIndex' value=$titleIndex >";
                    }
                    ?>
                </tr>
                <tr>
                    <td class='tg-c1' style="width: 40%;">※お名前（全角）</td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><input type="text" name="kanji_name"'.getInputValue('kanji_name').' placeholder="例: 田中  太郎" required></td></tr>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$kanji_name);
                        echo "<input type='hidden' name='kanji_name' value='".$kanji_name."'>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class='tg-c1'>※ふりがな（全角）</td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><input type="text" name="furigana_name"'.getInputValue('furigana_name').' placeholder="例:たなか たろう" required></td></tr>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$furigana_name);
                        echo "<input type='hidden' name='furigana_name' value='".$furigana_name."'>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1">※部署</td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><select name="department" required>';
                        echo Register::getDepartment($_POST["department"]);
                        echo '</select></td></tr>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$department);
                        echo "<input type='hidden' name='department' value=$department>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1">※性別</td>
                    <td class="tg-c2">
                    <?php
                    if($flag==0){
                        echo '<label><input type="radio" name="gender" value="女"'.selectGender("gender","女").' required>女性</label>';
                        echo '<label><input type="radio" name="gender" value="男"'.selectGender("gender","男").' required>男性</label>';
                    }elseif($flag==1){
                        printf('<label>%s</label></td>',$gender);
                        echo "<input type='hidden' name='gender' value=$gender checked>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1"><b>生年月日（半角）</b></td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><label><select name="birth_year" required><option value=""></option>';
                        echo Register::selectBirthYear($_POST["birth"]);
                        echo '</select>年</label><label>';
                        echo '<label><select name="birth_month" required><option value=""></option>';
                        echo Register::selectBirthMonth($_POST["birth"]);
                        echo '</select>月</label>';
                        echo '<label><select name="birth_day" required><option value=""></option>';
                        echo Register::selectBirthDay($_POST["birth"]);
                        echo '</select>日</label></td></tr>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$birth);
                        echo "<input type='hidden' name='birth' value=$birth>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1"><b>連絡可能なメールアドレス（半角）</b></td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><label><input type="email" name="email"'.getInputValue("email").' placeholder="例:xxxxx@xxxx.xxx" required></label>';
                        echo $account->getError(Constants::$emailInvalid);
                        echo $account->getError(Constants::$emailTaken).'</td>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$email);
                        echo "<input type='hidden' name='email' value=$email>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1"><b>パスワード</b></td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><input type="password" name="password"'.getInputValue("password")." onkeyup=validatePassword(value,'inputPw') required>";
                        echo "<input type='hidden' name='temp_pw' ".getInputValue("password").">";
                        echo '<p id="inputPw"></p>';
                        echo $account->getError(Constants::$passwordLength);
                        echo $account->getError(Constants::$passwordInvalid);
                        echo $account->getError(Constants::$passwordsDontMatch);
                        echo '</td>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>'," ");
                        echo "<input type='hidden' name='temp_pw' ".getInputValue("temp_pw").">";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1"><b>パスワード(再確認)</b></td>
                    <?php
                    #もし更新画面だったら
                    if($flag==0&&$titleIndex==1){
                        echo '<td class="tg-c2"><input type="password" name="password2"'.getInputValue("password")." required>";
                        echo "<input type='hidden' name='temp_pw2' ".getInputValue("password").">";
                    #新規画面だったら
                    }elseif($flag==0){
                        echo '<td class="tg-c2"><input type="password" name="password2"'.getInputValue("password2")." required>";
                        echo "<input type='hidden' name='temp_pw2' ".getInputValue("password").">";
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>'," ");
                        echo "<input type='hidden' name='password' value=$password>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1"><b>連絡可能な電話番号</b></td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><label><input type="tel" name="phone_number" pattern="[0-9]{2}-[0-9]{4}-[0-9]{4}"'.getInputValue("phone_number").' required>例:03-0000-0000</label></td>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$phone_number);
                        echo "<input type='hidden' name='phone_number' value=$phone_number>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1"><b>住所</b></td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><input type="text" name="address"'.getInputValue("address").' required></td>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$address);
                        echo "<input type='hidden' name='address' value=$address>";
                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1"><b>最終学歴</b></td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><p>学校名</p>';
                        echo '<input type="text" name="school"'.getInputValue("school").' >';
                        echo '<p>学部・学科・科名</p>';
                        echo '<input type="text" name="major"'.getInputValue("major").' ></td>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$education);
                        echo "<input type='hidden' name='education' value=$education>";
                        echo "<input type='hidden' name='school' value=$school>";
                        echo "<input type='hidden' name='major' value=$major>";                    }
                    ?>
                </tr>
                <tr>
                    <td class="tg-c1"><b>※特技</b></td>
                    <?php
                    if($flag==0){
                        echo '<td class="tg-c2"><textarea id="story" name="specialty" 
                        rows="5" cols="33">'.getInputValue("specialty").'</textarea></td>';
                    }elseif($flag==1){
                        printf('<td class="tg-c2"><label>%s</label></td>',$specialty);
                        echo "<input type='hidden' name='specialty' value=$specialty>";
                    }
                    ?>
                </tr>
                <tr>
                    <td><b>写真</b></td>
                    <?php
                    if($flag==0){
                        echo "<td><input type='file' name='image'>";
                        echo "<input class='funcButtom' type='submit' name='upload' value='upload'>";
                        #画像を表示
                        echo uploadImage("file_path");
                        echo "<input type='hidden' name='file_path' ".getInputValue("file_path")."></td>";
                    }elseif($flag==1){
                        #画像を表示
                        echo "<td>";
                        echo uploadImage("file_path");
                        $file_path=$_POST["file_path"];
                        echo "<input type='hidden' name='file_path' value=$file_path>";
                        echo "</td>";
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    if($flag==0){
                        echo Register::$beforeConfirmFooter;
                        echo '<input type="submit" name="reset" value="reset"></td>';
                    }elseif($flag==1){
                        echo Register::$afterConfirmFooter;
                    }
                    ?>
                </tr>
            </table>
        <div class="container-fluid	manager_header">
         <div class="row">
             <div class="col-sm-1" ><b>サンシセイ</b></div>
         </div>
        </div>
</body>
</html>