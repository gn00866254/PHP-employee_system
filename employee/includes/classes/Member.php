<?php

class Member{
    private $con;

    public function __construct($con){
        $this->con=$con;
    }

    public function getEmployeeDetail(){
        $query_str = "select * from member;";
        //echo $query_str ;
        $result= $this->takeData($query_str);
        return $result;
    }
    
    public function takeData($query_str){
        $result = $this->con->query($query_str);
        $result = $result->fetchAll();
        return $result;
    }

    #検索のセレクト文を用意する。
    public function search($POST){
        $search_items=[];
        #例：SELECT * FROM member WHERE kanji_name LIKE "%田中%";
        #もし名前があったら
        if (isset($POST["emly_name"])&&trim($POST["emly_name"])!=""){
            $search_item=$POST['emly_name'];
            $search_name=" kanji_name LIKE '%".$search_item."%'";
            array_push($search_items,$search_name);
        }
        #もし性別が選択されたら
        if (isset($POST["gender"])&&trim($POST["gender"])!=""){
            $search_item=$POST['gender'];
            $search_gender=" gender LIKE '%".$search_item."%'";
            array_push($search_items,$search_gender);
        }
        #もし部署が選択されたら
        if (isset($POST["department"])&&trim($POST["department"])!=""){
            $search_item=$POST['department'];
            $search_department=" department LIKE '%".$search_item."%'";
            array_push($search_items,$search_department);
        }
        #もし電話を入力したら
        if (isset($POST["phone_number"])&&trim($POST["phone_number"])!=""){
            $search_item=$POST['phone_number'];
            $search_phone_number=" phone_number LIKE '%".$search_item."%'";
            array_push($search_items,$search_phone_number);
        }
        
        #特に何も指定検索していない
        if (empty($search_items)){
            return false;
        }
        #指定してあるなら以下の処理を行い、SQL文を用意する。
        $query_str = "SELECT * FROM member WHERE";
        for ($i=0;$i<count($search_items);$i++){
            #最後の要素だったら、and はいらないので処理をskipする
            if ($i==count($search_items)-1){
                $query_str.=$search_items[$i];
                continue;
            }
            $query_str.=$search_items[$i];
            $query_str.=" AND ";
        }
        return $query_str;
    }
    
    #ソート文を用意
    public function item_sort($query_str,$key){
        #検索の文があるかどうか
        if ($query_str){
            $query_str .=" ORDER BY ";
        }else {
            $query_str = "SELECT * FROM member ORDER BY ";
        }
        #押されたボタンはどれなのか
        $order_col="";
        if($key=="nameAs" or $key=="nameDes"){
            $order_col="furigana_name";
        }elseif ($key=="genderAs" or $key=="genderDes") {
            $order_col="gender";
        }elseif ($key=="departmentAs" or $key=="departmentDes"){
            $order_col="department";
        }elseif($key=="birthAs" or $key=="birthDes"){
            $order_col="birth";
        }elseif($key=="emailAs" or $key=="emailDes"){
            $order_col="email";
        }elseif($key=="phone_numAs" or $key=="phone_numDes"){
            $order_col="phone_number";
        }elseif($key=="addressAs" or $key=="addressDes"){
            $order_col="address";
        }elseif($key=="educationAs" or $key=="educationDes"){
            $order_col="education";
        }elseif($key=="specialtyAs" or $key=="specialtyDes"){
            $order_col="specialty";
        }elseif($key=="imageAs" or $key=="imageDes"){
            $order_col="emply_id";
        }
        $sort="";
        switch(mb_substr($key,-2)){
            case "As":
                $sort=" ASC";
                break;
            case "es";
                $sort=" DESC";
                break;
        }
        $query_str.=$order_col.$sort;
        return $query_str;
        
    }

    #削除
    public function deleteEmployee($id){
        #UPDATE `member` SET `delet` = '1' WHERE `member`.`emply_id` = 't000001';
        $query_str="UPDATE member SET delet = '1' WHERE emply_id = '".$id."'";
        #echo $query_str;
        return $this->con->query($query_str);
    }
}
?>