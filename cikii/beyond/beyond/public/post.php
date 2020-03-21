<?php  
    // echo "php_input".file_get_contents("php://input");
    echo "<br>\n";
    // echo "php_raw_post_data".$HTTP_RAW_POST_DATA;
    // print_r($HTTP_RAW_POST_DATA);
    echo $_POST["user"];
    echo $GLOBALS['HTTP_RAW_POST_DATA'];
    print_r($_POST["user"]);
?>

