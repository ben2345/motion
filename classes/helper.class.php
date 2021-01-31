<?php
Class Helper{
    public $id;
    public $action;
    public $skus = ['lastUpdateTime','nbFiles','temperatureCpu','temperatureGpu','loadCpu','bitcoinRate','temperatureBalcon','temperatureSalon','temperatureSalleDeBain'];
    public $filepath;

    function __construct()
    {
        $this->id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        $this->action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        $this->filepath = dirname(dirname(__FILE__)) . '/files/events';
    }

    public function getSkuFields(){
        foreach ($this->skus as $sku){
            $this->getSku($sku);
            echo '<span id='.$sku.' data-delay='.$this->skus[$sku]['delay'].'>'.$this->skus[$sku]['value'].'</span>';
         }
    }
  
    public function getSku($sku){
        $value = '0';
        $delay = false;

        $conn = mysqli_connect('localhost','ben','newton22','temperatures');

        switch ($sku) {
            case 'lastUpdateTime':
                $value = '0'; // todo
                $delay = false; // milisecondes
            break;
            case 'nbFiles':
                $value = '0'; // todo
            break;
            case 'temperatureCpu':
                $value = 'Cpu : ' . exec('echo $((`cat /sys/class/thermal/thermal_zone0/temp|cut -c1-2`)).$((`cat /sys/class/thermal/thermal_zone0/temp|cut -c4-5`))') . '°';
                $delay = 120000;
            break;
            case 'temperatureGpu':
                $value = 'Gpu : ' . exec('sudo vcgencmd measure_temp | cut -c6-9') . '°';
                $delay = 120000;
            break;
            case 'loadCpu':
                $value = 'Cpu Load : ' . exec("cut -f 1 -d ' ' /proc/loadavg") * 100 . '%';
                $delay = 110000;
            break;
            case 'bitcoinRate':
                $value = 'Bitcoin : ' .json_decode(file_get_contents("https://bitpay.com/api/rates/BTC/EUR"))->rate.' €';
                $delay = 120000;
            break;
            case 'temperatureBalcon':
                $result = $conn->query('SELECT * FROM `temperatures_log` WHERE id_sensor = 1 ORDER BY `date` DESC LIMIT 1');
                $temperature_data = $result->fetch_row();
                $value = 'Balcon : '.$temperature_data[2].'° | '.$temperature_data[3].' %';
                $delay = 90000;
            break;
            case 'temperatureSalon':
                $result = $conn->query('SELECT * FROM `temperatures_log` WHERE id_sensor = 2 ORDER BY `date` DESC LIMIT 1');
                $temperature_data = $result->fetch_row();
                $value = 'Salon : '.$temperature_data[2].'° | '.$temperature_data[3].' %';
                $delay = 115000;
            break;
            case 'temperatureSalleDeBain':
                $result = $conn->query('SELECT * FROM `temperatures_log` WHERE id_sensor = 3 ORDER BY `date` DESC LIMIT 1');
                $temperature_data = $result->fetch_row();
                $value = 'Salle de bain : '.$temperature_data[2].'° | '.$temperature_data[3].' %';
                $delay = 110000;
            break;
        }
        $this->skus[$sku] = ['value' => $value, 'delay' => $delay];
        return $this->skus[$sku]['value'];        
    }

    public function deleteFIle(){
        if(@unlink($this->id)){
            return 'supprimé';
        }
    }

    public function getFiles(){
           
        if ($this->id == 'all') {
            unset($_SESSION['lastMTime']);
            }

            if ($folders = glob($this->filepath . '/*', GLOB_ONLYDIR)) {

            foreach ($folders as $folder) {
         
                $event_files_path = glob($folder . '/*.gif');
        
                // fichiers du + récent au + vieux
                usort($event_files_path, function ($a, $b) {
                    return filemtime($a) - filemtime($b) ;
                });
         
                foreach ($event_files_path as $event_file_path) {
            
                $filemtime = filemtime($event_file_path);
                if (isset($_SESSION['lastMTime'])) {
                    if ($_SESSION['lastMTime'] >= $filemtime) {
                    continue;
                    }
                }
                $pop = explode('.', pathinfo($event_file_path)['basename']);
                $hour = reset($pop);
                $event_files_infos[] = array(
                    'date' => date('d/m/Y', $filemtime),
                    'heure' => str_replace('-', ':', $hour),
                    'filemtime' => $filemtime,
                    'fileduration' => $this->getGIFDuration($event_file_path),
                    'filesize' => $this->human_filesize($event_file_path),
                    'url' => str_replace(dirname(__DIR__), 'http://' . $_SERVER['HTTP_HOST'] . '/motion', $event_file_path),
                    'path' => $event_file_path,
                );
                }
        
                if (isset($event_files_infos)) {
                $_SESSION['lastMTime'] = end($event_files_infos)['filemtime'];
                $data['event_files_infos'] = $event_files_infos;
                $data['nb_files'] = count($event_files_path) . ' Fichiers';
                $data['last_update_time'] = 'Update : ' . date('H:i');
                //exec("sudo python /var/www/motion/scripts/bot.py");
                }
            }
            return isset($data) ? $data : '';
            }
    }

    public function human_filesize($image_path, $decimals = 2){
        $sz = 'BKMGTP';
        $bytes = filesize($image_path);
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public function getGIFDuration($image_path){
        $gif_graphic_control_extension = "/21f904[0-9a-f]{2}([0-9a-f]{4})[0-9a-f]{2}00/";
        $file = file_get_contents($image_path);
        $file = bin2hex($file);
        $total_delay = 0;
        preg_match_all($gif_graphic_control_extension, $file, $matches);
        foreach ($matches[1] as $match) {
            $delay = hexdec(substr($match, -2) . substr($match, 0, 2));
            if ($delay == 0) $delay = 1;
            $total_delay += $delay;
        }

        // delays converted to seconds
        $total_delay /= 100;
        return $total_delay;
    }


    public function getSensorData($sensor_id){
        $attempt = 0;
        $max_attemps = 5;
        $values = false;
        while(!$values){
            $attempt++;
            if($attempt <= $max_attemps){
                $values = exec("sudo sh /var/www/motion/scripts/thermometre_".$sensor_id.".sh");
            }else{
                break;
            }
        }
        return $values;
    }

    public function logSensorData($values_ok){
        $conn = mysqli_connect('localhost','ben','newton22','temperatures');
        foreach($values_ok as $thermometer_id => $value){
            list($temperature, $humidity) = explode('|',$value);
            if($temperature !== '' && $humidity !== ''){
                $req = "INSERT INTO `temperatures_log` (`id_sensor`, `temperature`, `humidity`) VALUES ('".$thermometer_id."', '".$temperature."', '".$humidity."');";
                $conn->query($req);
            }
        }
    }


    public function getJSONTemperatures(){

        $conn = mysqli_connect('localhost','ben','newton22','temperatures');
        $result = $conn->query('SELECT * FROM `temperatures_log` ORDER BY `date`');
        $datas = $result->fetch_all(MYSQLI_ASSOC);

        $sensor_names = [
            "1" => "Balcon",
            "2" => "Salon",
            "3" => "Salle de bain"
        ];

        $sensor_conf = [
            "1" => ["Balcon", "#222"],
            "2" => ["Salon", "#000"],
            "3" => ["Salle de bain", "#111"]
        ];

        foreach ($datas as $data){
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $data['date']);
            $mts = $date->getTimestamp().'000'; // millisecond timestamp
            $tempData['Température '.$sensor_conf[$data['id_sensor']][0]][] = ['x' => (float)$mts, 'y' => (float)$data['temperature']];
            $tempData['Humidité '.$sensor_conf[$data['id_sensor']][0]][] = ['x' => (float)$mts, 'y' => (float)$data['humidity']];
            //$tempData['Humidité '.$sensor_names[$data['id_sensor']]][] = [(float)$mts, (float)$data['humidity'], '#451244'];
        }

        foreach ($tempData as $key => $values){

            foreach($sensor_conf as $conf){
                if ('Température '.$conf[0] == $key){
                    $color = $conf[1];
                }
            }

         

           
            
            $tempData2[] = array("key" => $key, "values" => $values, "color" => $color);
        }
        echo json_encode($tempData2);
    }

}