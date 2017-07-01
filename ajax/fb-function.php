<?php
/**
 * 打印一个变量
 * @param $var mixed 变量
 */
function E($var){
    if(is_bool($var)){
        var_dump($var);
    }elseif(is_null($var)){
        var_dump(NULL);
    }else{
        echo "<pre style='position:relative; z-Index: 1000; padding: 10px; border-radius: 5px;background: #F5F5F5;
                border: 1px solid #aaa; font-size: 20px; line-height: 18px; opacity: 0.9;'>"
            .print_r($var, true).'</pre>';
    }
}
?>