<?php 
require_once("includes/classes/Account.php");
require_once("includes/classes/Register.php");
require_once("includes/classes/FormSanitizer.php");
require_once("includes/config.php");
require_once("includes/classes/Constants.php");

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
#管理画面から来たのなら
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
    $phone_number=FormSanitizer::sanitizeFormString($_POST["phone_number"]);
    $address=FormSanitizer::sanitizeFormString($_POST["address"]);
    $school=FormSanitizer::sanitizeFormSchool($_POST["school"]);
    $major=FormSanitizer::sanitizeFormSchool($_POST["major"]);
    $education=$school.$major;
    $specialty=FormSanitizer::sanitizeFormString($_POST["specialty"]);
    $account->validatePasswords($password);
    $account->validateEmails($email,$titleIndex);
    if ($account->error_check()){
        $flag = 1;
    }
#押されたのは、確認画面だったら
}elseif (isset($_POST["submitCofirm"])){
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
        $account->update($_SESSION["id"],$password,$birth,$kanji_name,$furigana_name,$gender,
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
        <title>shisei_company</title>
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
                <?php
                #before confirm
                if ($flag==0){
                    echo "<tr><th colspan=2 class='tg-span'><b>".$formTitle[$titleIndex]."</b></th><tr>";
                    echo "<input type='hidden' name='titleIndex' value=$titleIndex>";
                    echo Register::$KanjiNameTitle;
                    echo '<td class="tg-c2"><input type="text" name="kanji_name"'.getInputValue('kanji_name').' required>例: 田中  太郎</td></tr>';
                    echo Register::$furiganaNameTitle;
                    echo '<td class="tg-c2"><input type="text" name="furigana_name"'.getInputValue('furigana_name').' required>例:たなか たろう</td></tr>';
                    echo Register::$departmentTitle;
                    echo '<td class="tg-c2"><select name="department" required>';
                    echo Register::getDepartment($_POST["department"]);
                    echo '</select></td></tr>';
                    echo Register::$genderTitle;
                    echo '<td class="tg-c2">';
                    echo '<label><input type="radio" name="gender" value="女"'.selectGender("gender","女").' required>女性</label>';
                    echo '<label><input type="radio" name="gender" value="男"'.selectGender("gender","男").' required>男性</label></td></tr>';
                    echo Register::$birthTitle;
                    echo '<td class="tg-c2"><label><select name="birth_year" required><option value=""></option>';
                    echo Register::selectBirthYear($_POST["birth"]);
                    echo '</select>年</label><label>';
                    echo '<label><select name="birth_month" required><option value=""></option>';
                    echo Register::selectBirthMonth($_POST["birth"]);
                    echo '</select>月</label>';
                    echo '<label><select name="birth_day" required><option value=""></option>';
                    echo Register::selectBirthDay($_POST["birth"]);
                    echo '</select>日</label></td></tr>';
                    echo Register::$emailTitle;
                    echo '<td class="tg-c2"><label><input type="email" name="email"'.getInputValue("email").' required> 例:xxxxx@xxxx.xxx</label>';
                    echo $account->getError(Constants::$emailInvalid).'</td></tr>';
                    echo Register::$passwordTitle;
                    echo '<td class="tg-c2"><input type="password" name="password"'.getInputValue("password")." onkeyup=validatePassword(value,'inputPw') required>";
                    echo '<p id="inputPw"></p>';
                    echo $account->getError(Constants::$passwordLength).'</td></tr>';
                    echo Register::$phoneTitle;
                    echo '<td class="tg-c2">
                    <label><input type="tel" name="phone_number" pattern="[0-9]{2}-[0-9]{4}-[0-9]{4}"'.getInputValue("phone_number").' required>例:03-0000-0000</label></td></tr>';
                    echo Register::$addressTitle;
                    echo '<td class="tg-c2"><input type="text" name="address"'.getInputValue("address").' required></td></tr>';
                    echo Register::$educationTitle;
                    echo '<td class="tg-c2"><p>学校名</p>
                    <input type="text" name="school"'.getInputValue("school").' required>
                    <p>学部・学科・科名
                    </p><input type="text" name="major"'.getInputValue("major").' required></td></tr>';
                    echo Register::$specialtyTitle;
                    echo '<td class="tg-c2"><textarea id="story" name="specialty" 
                    rows="5" cols="33">'.getInputValue("specialty").'</textarea></td></tr>';
                    echo "<tr><td><b>写真</b></td>
                        <td><input type='file' name='image'>";
                    echo "<input class='funcButtom' type='submit' name='upload' value='upload'>";
                    echo uploadImage("file_path");
                    echo "<input type='hidden' name='file_path' ".getInputValue("file_path").">";
                    echo "</td></tr>";
                    echo Register::$beforeConfirmFooter;
                }elseif($flag==1){
                    echo "<tr><th colspan=2 class='tg-span'><b>---確定---</b></th><tr>";
                    echo "<input type='hidden' name='titleIndex' value=$titleIndex>";
                    echo Register::$KanjiNameTitle;
                    printf(Register::$afterConfirmKanjiName,$kanji_name);
                    echo "<input type='hidden' name='kanji_name' value=$kanji_name>";
                    echo Register::$furiganaNameTitle;
                    printf(Register::$afterConfirmFuriganaName,$furigana_name);
                    echo "<input type='hidden' name='furigana_name' value=$furigana_name>";
                    echo Register::$departmentTitle;
                    printf(Register::$afterConfirmDepartment,$department);
                    echo "<input type='hidden' name='department' value=$department>";
                    echo Register::$genderTitle;
                    printf(Register::$afterConfirmGender,$gender);
                    echo "<input type='hidden' name='gender' value=$gender checked>";
                    echo Register::$birthTitle;
                    printf(Register::$selectedBirth,$birth);
                    echo "<input type='hidden' name='birth' value=$birth>";
                    echo Register::$emailTitle;
                    printf(Register::$afterConfirmEmail,$email);
                    echo "<input type='hidden' name='email' value=$email>";
                    echo Register::$passwordTitle;
                    printf(Register::$afterConfirmPassword,$password);
                    echo "<input type='hidden' name='password' value=$password>";
                    echo Register::$phoneTitle;
                    printf(Register::$afterConfirmPhone,$phone_number);
                    echo "<input type='hidden' name='phone_number' value=$phone_number>";
                    echo Register::$addressTitle;
                    printf(Register::$afterConfirmAddress,$address);
                    echo "<input type='hidden' name='address' value=$address>";
                    echo Register::$educationTitle;
                    printf(Register::$afterConfirmEducation,$education);
                    echo "<input type='hidden' name='education' value=$education>";
                    echo "<input type='hidden' name='school' value=$school>";
                    echo "<input type='hidden' name='major' value=$major>";
                    echo Register::$specialtyTitle;
                    printf(Register::$afterConfirmSpecialty,$specialty);
                    echo "<input type='hidden' name='specialty' value=$specialty>";
                    echo "<tr><td><b>写真</b></td><td>";
                    echo uploadImage("file_path");
                    $file_path=$_POST["file_path"];
                    echo "<input type='hidden' name='file_path' value=$file_path>";
                    echo "</td></tr>";
                    echo Register::$afterConfirmFooter;
                }
                ?> 
            </table>
        </form>
        <div class="container-fluid	manager_header">
         <div class="row">
             <div class="col-sm-1" ><b>サンシセイ</b></div>
         </div>
        </div>
</body>
</html>