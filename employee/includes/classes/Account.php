<?php 
date_default_timezone_set("Asia/Tokyo");

class Account{
    
    private $con;
    private $errorArray=array();

    public function __construct($con){
        $this ->con = $con;
        
    }

    public function register($password,$birth,$kanji_name,$furigana_name,$gender,
                    $department,$email,$phone_number,$address,$education,$specialty,$file_path){
        $id    = $this->getId();
        if (isset($error_array)){
            return False ;
        }
        $this->insertEmplyDetails($id,$password,$birth,$kanji_name,$furigana_name,$gender,
        $department,$email,$phone_number,$address,$education,$specialty,$file_path);
        return True ;
    }

    private function insertEmplyDetails($id,$password,$birth,$kanji_name,$furigana_name,$gender,
    $department,$email,$phone_number,$address,$education,$specialty,$file_path){
        $password=hash("sha512",$password);
        $query=$this->con->prepare("INSERT INTO member (emply_id,password,birth,kanji_name,furigana_name,gender,department,email,phone_number,address,education,specialty,delet,image,updateTime) 
                                            VALUES (:id,:password,:birth,:kanji_name,:furigana_name,:gender,:department,:email,:phone_number,:address,:education,:specialty,:delet,:file_path,:updateTime)");
        $query->bindValue(":id", $id);
        $query->bindValue(":password", $password);
        $query->bindValue(":birth", $birth);
        $query->bindValue(":kanji_name", $kanji_name);
        $query->bindValue(":furigana_name", $furigana_name);
        $query->bindValue(":gender", $gender);
        $query->bindValue(":department", $department);
        $query->bindValue(":email", $email);
        $query->bindValue(":phone_number", $phone_number);
        $query->bindValue(":address", $address);
        $query->bindValue(":education", $education);
        $query->bindValue(":specialty", $specialty);
        $query->bindValue(":delet", 0);
        $query->bindValue(":file_path", $file_path);
        $query->bindValue(":updateTime", date("Y-m-d H:i:s"));
        #return bool
        return $query->execute();
    }


    public function login($em,$pw){
        //データを抽出し、あれば通る。なければFalse。
        $pw=hash("sha512",$pw);
        $query=$this->con->prepare("SELECT * FROM member WHERE emply_id=:em AND password=:pw");
        $query->bindValue(":em", $em);
        $query->bindValue(":pw", $pw);
        
        $query->execute();
        if ($query->rowCount()==1){
            return true;
        }else {
            return false;
        }
    }

    public function update($titleIndex,$id,$password,$temp_pw,$birth,$kanji_name,$furigana_name,$gender,
                            $department,$email,$phone_number,$address,$education,$specialty,$file_path){
        #更新画面でパスワードが変更されたら
        if($titleIndex==1&&$password!=$temp_pw){
            $password=hash("sha512",$password);
        #新規画面だったら、ハッシュ化必須
        }elseif($titleIndex==0){
            $password=hash("sha512",$password);
        }
        
        #エラーの検証するスペース
        if (isset($error_array)){
            return False;
        }
        $this->updateEmplyDetails($id,$password,$birth,$kanji_name,$furigana_name,$gender,
                            $department,$email,$phone_number,$address,$education,$specialty,$file_path);
        return True;
    }

    private function updateEmplyDetails($id,$password,$birth,$kanji_name,$furigana_name,$gender,
                        $department,$email,$phone_number,$address,$education,$specialty,$file_path){
        #UPDATE `member` SET `password` = 'A12345678', `address` = '東京都杉並区', `emply_id` = 't000005' WHERE `member`.`emply_id` = 't000005';
        $query_str="UPDATE member SET 
        password = :password, 
        birth = :birth, 
        kanji_name = :kanji_name, 
        furigana_name = :furigana_name, 
        gender = :gender, 
        department = :department, 
        email = :email, 
        phone_number = :phone_number,
        address = :address, 
        education = :education, 
        image = :file_path,
        specialty = :specialty,
        updateTime = :updateTime WHERE member.emply_id = :id";
        $query=$this->con->prepare($query_str);
        $query->bindValue(":id", $id);
        $query->bindValue(":password", $password);
        $query->bindValue(":birth", $birth);
        $query->bindValue(":kanji_name", $kanji_name);
        $query->bindValue(":furigana_name", $furigana_name);
        $query->bindValue(":gender", $gender);
        $query->bindValue(":department", $department);
        $query->bindValue(":email", $email);
        $query->bindValue(":phone_number", $phone_number);
        $query->bindValue(":address", $address);
        $query->bindValue(":education", $education);
        $query->bindValue(":file_path", $file_path);
        $query->bindValue(":specialty", $specialty);
        $query->bindValue(":updateTime", date("Y-m-d H:i:s"));
        return $query->execute();
        
    }


    public function getInfo($emply_id){
        $query=$this->con->prepare("SELECT * FROM member WHERE emply_id=:emply_id");
        $query->bindValue(":emply_id", $emply_id);
        $query->execute();
        $result=$query->fetchAll();
        #var_dump($result);
        return $result;
    }

    #idの番号を決める
    public function getId(){
        $stmt = $this->con->query("SELECT emply_id FROM member");
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        #$index = count($result)-1;
        $max_id=0;
        foreach($result as $_id){
            $_id=(int)mb_substr($_id,1);
            if ($_id>$max_id){
                $max_id=$_id;
            }
        }
        $id = strval($max_id+1);
        while(mb_strlen($id)<6){
            $id="0".$id;

        }
        return "t".$id;
    }
    
    #password　検証
    public function validatePasswords($pw,$pw2,$titleIndex,$temp_pw,$temp_pw2){
        if($titleIndex==1 && $pw==$temp_pw && $pw2==$temp_pw2){
            return;
        }
        if($pw!=$pw2){
            array_push($this->errorArray, Constants::$passwordsDontMatch);
            return;
        }
        if ((strlen($pw)<8) || (strlen($pw) > 20)) {
            array_push($this->errorArray , Constants::$passwordLength);
            return;
        }
        #&&の優先順位高い
        if((preg_match('/[a-z]/', $pw)||preg_match('/[A-Z]/', $pw))&&preg_match('/[0-9]/', $pw)){
            return;
        }else{
            array_push($this->errorArray , Constants::$passwordInvalid);
        }

    }

    #mail　検証　
    public function validateEmails($em,$titleIndex,$SESSION){
        if(!filter_var($em,FILTER_VALIDATE_EMAIL)){
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }
        #新規登録のページだったら
        if($titleIndex==0){
            $query=$this->con->prepare("SELECT * FROM member WHERE email=:em");
            $query->bindValue(":em",$em);
            $query->execute();
            if ($query->rowCount() != 0){
                array_push($this->errorArray , Constants::$emailTaken);
            }
        }
        #更新のページだったら
        if($titleIndex==1){
            $query=$this->con->prepare("SELECT * FROM member WHERE email=:em");
            $query->bindValue(":em",$em);
            $query->execute();
            $result=$query->fetchAll();
            if(isset($result[0])){
                $temp_id=$result[0]["emply_id"];
                if ($SESSION["id"]!=$temp_id && ($query->rowCount() >=1) ){
                    array_push($this->errorArray , Constants::$emailTaken);
                }
            }
        }
        
    }

    public function validateImage($file,$id){
        $image=$id;
        #拡張子を取得
        $extension=strrchr($file['image']['name'], '.');
        $image.=$extension;
        $file_path= "assets/images/$image";
        if(!empty($file["image"]["tmp_name"])){
            if (exif_imagetype($file["image"]["tmp_name"])){
                move_uploaded_file($_FILES['image']['tmp_name'], $file_path);
                return $file_path;
            }
        }
        
        
    }

    public function getError($error){
        if(in_array($error,$this->errorArray)){
            return "<p class='errorMessage'>$error</p>";
        }
    }

    public function error_check(){
        #print_r($this->errorArray);
        return empty($this->errorArray);
    }
    
}

?>