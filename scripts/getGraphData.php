<?php
include_once('/var/www/motion/classes/helper.class.php');
header('Content-Type: application/json');

if (class_exists('Helper')) {
    $helper = new Helper();
    $helper->getJSONTemperatures();
}



// echo '[{"values": [{"x": 2, "y": 10}, {"x": 3, "y": 11}],"key": "Sine Wave"},
//     {"values": [{"x": 3, "y": 5}], "key": "Cosine Wave"}
//   ]
// ';



 