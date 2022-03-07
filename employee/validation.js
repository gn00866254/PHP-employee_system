function validatePassword( str, resultid ) {
    const pattern1=/[a-z]/;
    const pattern3=/[A-Z]/;
    const pattern2=/[0-9]/;
    //^([a-zA-Z0-9]{8,})$/;
    if (str.length<8){
        document.getElementById(resultid).innerHTML = "8文字以上で入力してください。";
    }else if(str.length>20){
        document.getElementById(resultid).innerHTML = "20文字以内で入力してください。";
    }else if(!(pattern3.test(str)|pattern1.test(str)&&pattern2.test(str))){
        document.getElementById(resultid).innerHTML = "半角英数字の組み合わせで入力してください";
    }else{
        document.getElementById(resultid).innerHTML = "";
    }
    
 }