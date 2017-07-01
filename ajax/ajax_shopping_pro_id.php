<?php
if(isset($_GET['shopping_pro_id'])){

    $debug = false;
    require 'includes/application_top.php';

    switch ($_GET['shopping_pro_id']){
        case 'pro_id':
            $_SESSION['shopping_pro_id']= array($_POST['products_id']);
            echo 1;
            break;
        case 'delete_session':

            break;
    }
}
?>