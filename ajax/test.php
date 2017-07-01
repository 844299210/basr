<?php
$str ='%7B"34959%3Aa3636cafdf658ae674b16b5943e9cb8f"%3A%7B"qty"%3A"1"%2C"attributes"%3A%7B"64"%3A"2481"%2C"65"%3A"2482"%2C"66"%3A"4331"%2C"63"%3A"4483"%2C"87"%3A"3364"%2C"15"%3A"2484"%2C"length"%3A"38761"%7D%2C"fiber_count"%3A%5B%5D%7D%2C"36157"%3A%7B"qty"%3A"7"%2C"fiber_count"%3A%5B%5D%7D%2C"40192"%3A%7B"qty"%3A"1"%2C"fiber_count"%3A%5B%5D%7D%2C"32487%3A33b80ec84cd8bc4efe1eacb2f82810e4"%3A%7B"qty"%3A"1"%2C"attributes"%3A%7B"14"%3A"3008"%2C"length"%3A"24971"%7D%2C"fiber_count"%3A%5B%5D%7D%2C"33485"%3A%7B"qty"%3A"1"%2C"fiber_count"%3A%5B%5D%7D%2C"29755%3A807c67d1b83e1bd778b33005856a7b7b"%3A%7B"qty"%3A"1"%2C"attributes"%3A%7B"47"%3A"5026"%7D%2C"fiber_count"%3A%5B%5D%7D%2C"11552"%3A%7B"qty"%3A"1"%2C"fiber_count"%3A%5B%5D%7D%7D';
$str = urldecode($str);
$str = json_decode(stripslashes($str),true);
var_dump($str);
?>