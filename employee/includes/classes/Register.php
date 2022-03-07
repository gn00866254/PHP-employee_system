<?php 
date_default_timezone_set("Asia/Tokyo");

class Register{
    #フォーム見出し
    public static $beforeConfirmHeading='<tr><th colspan=2 class="tg-span"><b>新規登録</b></th><tr>';
    public static $afterConfirmHeading='<tr><th colspan=2 class="tg-span"><b>確定</b></th><tr>';
    #名前
    public static $KanjiNameTitle="<tr><td class='tg-c1'>※お名前（全角）</td>";
    public static $afterConfirmKanjiName='<td class="tg-c2"><label>%s</label></td></tr>';
    #フリガナ
    public static $furiganaNameTitle="<tr><td class='tg-c1'>※ふりがな（全角）</td>";
    public static $afterConfirmFuriganaName='<td class="tg-c2"><label>%s</label></td></tr>';
    #部署
    public static $departmentTitle='<tr><td class="tg-c1">※部署</td>';
    public static $afterConfirmDepartment='<td class="tg-c2"><label>%s</label></td></tr>';
    public static function getDepartment($POST){
        $result="";
        $departMent=[" ","プロデューサー部","アイドル部","未定"];
        for ($i=0;$i<count($departMent);$i++){
            if ($POST==$departMent[$i]){
                $result.="<option value=".$departMent[$i]." selected>";
            }else{
                $result.="<option value=".$departMent[$i].">";
            }
            $result.=$departMent[$i].'</option>';
        }
        return $result;
    }
    #gender
    public static $genderTitle='<tr><td class="tg-c1">※性別</td>';
    public static $afterConfirmGender='<td class="tg-c2"><label>%s</label></td></tr>';

    #birthday
    public static $birthTitle='<tr><td class="tg-c1"><b>生年月日（半角）</b></td>';
    public static function selectBirthYear($POST){
        #slice
        $POST=mb_substr($POST,0,4);
        $result='';
        for($i=1900;$i<=date("Y");$i++){ 
            if ($POST==$i){
                $result.="<option value='$i' selected>$i</option>";
            }else {
                $result.="<option value='$i'>$i</option>";
            }
        }
        return $result;
    }
    public static function selectBirthMonth($POST){
        #slice -> array([0]->選択した数値 ,[1]-> 日)
        $POST=explode("月",mb_substr($POST,5));
        $result='';
        for ($i=1;$i<=12;$i++){
            #文字列から数値にする。
            if ((int)$POST[0]==$i){
                $result.="<option value='$i' selected>$i</option>";
            }else {
                $result.="<option value='$i'>$i</option>";
            }
        }
        return $result;
    }
    public static function selectBirthDay($POST){
        #slice -> array([0]->選択した数値 ,[1]-> 日)
        $POST=explode("月",$POST);
        $POST=str_replace("日","",$POST[1]);
        
        $result='';
        for ($i=1;$i<=31;$i++){
            if ((int)$POST==$i){
                $result.="<option value='$i' selected>$i</option>";
            }else {
                $result.="<option value='$i'>$i</option>";
            }
        }
        return $result;
    }
    public static $selectedBirth='<td class="tg-c2"><label>%s</label></td></tr>';

    #email　メール
    public static $emailTitle='<tr><td class="tg-c1"><b>連絡可能なメールアドレス（半角）</b></td>';
    public static $afterConfirmEmail='<td class="tg-c2"><label>%s</label></td></tr>';
    #パスワード
    public static $passwordTitle='<tr><td class="tg-c1"><b>パスワード</b></td>';
    public static $afterConfirmPassword='<td class="tg-c2"><label>%s</label></td></tr>';

    #phone number
    public static $phoneTitle='<tr><td class="tg-c1"><b>連絡可能な電話番号</b></td>';
    public static $afterConfirmPhone='<td class="tg-c2"><label>%s</label></td></tr>';

    #address
    public static $addressTitle='<tr><td class="tg-c1"><b>住所</b></td>';
    public static $afterConfirmAddress='<td class="tg-c2"><label>%s</label></td></tr>';

    #学歴
    public static $educationTitle='<tr><td class="tg-c1"><b>最終学歴</b></td>';
    public static $beforeConfirmEducation='<td class="tg-c2"><p>学校名</p><input type="text" name="school" required>
                                            <p>学部・学科・科名</p><input type="text" name="major" required></td></tr>';
    public static $afterConfirmEducation='<td class="tg-c2"><label>%s</label></td></tr>';

    #特技
    public static $specialtyTitle='<tr><td class="tg-c1"><b>※特技</b></td>';
    public static $afterConfirmSpecialty='<td class="tg-c2"><label>%s</label></td></tr>';

    #画像

    #footer
    public static $beforeConfirmFooter='<td colspan=2 class="tg-span"><input type="submit" name="submitButton" value="送信">';
    public static $afterConfirmFooter='<td colspan=2 class="tg-span"><input type="submit" name="submitCofirm" value="確定"><input type="submit" name="cancel" value="キャンセル"></td>';

 }
?>