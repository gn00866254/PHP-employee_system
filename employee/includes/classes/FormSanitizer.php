<?php
class FormSanitizer{
    public static function sanitizeFormString($inputText){
        /*指定した文字列 (string) から全ての NULL バイトと HTML および PHP タグを取り除きます。*/
        $inputText = strip_tags($inputText);
        /*空白を消す */
        $inputText = trim($inputText);
        return $inputText;
    }

    public static function sanitizeFormSchool($inputText){
        $inputText = strip_tags($inputText);
        $inputText = trim($inputText);
        return $inputText;
    }

    public static function sanitizeFormName($inputText){
        $inputText = strip_tags($inputText);
        $inputText = trim($inputText);
        return $inputText;
    }
    
    public static function validateImage(){
        
    }
}
?>