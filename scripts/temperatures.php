<?php
include_once('/var/www/motion/classes/helper.class.php');

if (class_exists('Helper')) {
    $helper = new Helper();
    for($id = 1; $id < 4; $id++){
        $values = $helper->getSensorData($id);
        if($values){
            $values_ok[$id] = $values;
        }
        $values = false;
    }

    if(isset($values_ok)){
        if(is_array($values_ok)){
            if(count($values_ok) == 3){
                $helper->logSensorData($values_ok);
            }
        }
    }
}
