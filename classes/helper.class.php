<?php
Class Helper{
    public $id;
    public $action;
    public $skus = ['lastUpdateTime','nbFiles','temperatureCpu','temperatureGpu','loadCpu','bitcoinRate'];
    public $filepath;

    function __construct()
    {
        $this->id = $_REQUEST['id'];
        $this->action = $_REQUEST['action'];
        $this->filepath = dirname(dirname(__FILE__)) . '/files/gif';
    }

    public function getSkuFields(){
        foreach ($this->skus as $sku){
            $this->getSku($sku);
            echo '<span id='.$sku.' data-delay='.$this->skus[$sku]['delay'].'>'.$this->skus[$sku]['value'].'</span>';
         }
    }
  
    public function getSku($sku){
        $value = '0';
        switch ($sku) {
            case 'lastUpdateTime':
                $value = '0'; // todo
                $delay = 10000; // milisecondes
            break;
            case 'nbFiles':
                $value = '0'; // todo
                $delay = 11000;
            break;
            case 'temperatureCpu':
                $value = 'Cpu : ' . exec('echo $((`cat /sys/class/thermal/thermal_zone0/temp|cut -c1-2`)).$((`cat /sys/class/thermal/thermal_zone0/temp|cut -c4-5`))') . '°';
                $delay = 12000;
            break;
            case 'temperatureGpu':
                $value = 'Gpu : ' . exec('sudo vcgencmd measure_temp | cut -c6-9') . '°';
                $delay = 13000;
            break;
            case 'loadCpu':
                $value = 'Cpu Load : ' . exec("cut -f 1 -d ' ' /proc/loadavg") . '%';
                $delay = 14000;
            break;
            case 'bitcoinRate':
                $value = 'Bitcoin : ' .json_decode(file_get_contents("https://bitpay.com/api/rates/BTC/EUR"))->rate.' €';
                $delay = 60000;
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
        
                $gif_files_path = glob($folder . '/*.gif');
        
                // fichiers du + récent au + vieux
                usort($gif_files_path, function ($a, $b) {
                return filemtime($a) - filemtime($b) ;
                });
        
                foreach ($gif_files_path as $gif_file_path) {
            
                $filemtime = filemtime($gif_file_path);
                if (isset($_SESSION['lastMTime'])) {
                    if ($_SESSION['lastMTime'] >= $filemtime) {
                    continue;
                    }
                }
                //todo heure format
                $gif_files_infos[] = array(
                    'date' => date('d/m/Y', $filemtime),
                    'heure' => str_replace('-', ':', reset(explode('.', pathinfo($gif_file_path)['basename']))),
                    'filemtime' => $filemtime,
                    'fileduration' => $this->getGIFDuration($gif_file_path),
                    'filesize' => $this->human_filesize($gif_file_path),
                    'url' => str_replace(dirname(__DIR__), 'http://' . $_SERVER['HTTP_HOST'] . '/motion', $gif_file_path),
                    'path' => $gif_file_path,
                );
                }
        
                if (isset($gif_files_infos)) {
                $_SESSION['lastMTime'] = end($gif_files_infos)['filemtime'];
                $data['gif_files_infos'] = $gif_files_infos;
                $data['nb_files'] = count($gif_files_path) . ' Fichiers';
                $data['last_update_time'] = 'Update : ' . date('H:i');
                }
            }
            return $data;
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

}