<?php 
include_once('http-auth.php');
include_once('classes/helper.class.php');
$helper = new Helper();
?>
<html>
<head>
<title>Webcam O²</title>
<meta name="theme-color" content="#007bff" />
<link rel="stylesheet" href="css/css.css?v=<?php echo rand(0,999999) ?>">
<link rel="shortcut icon" type="image/ico" href="favicon.ico"/>
<style>

</style>
</head>
<body>
    
<div id="general-infos">
    <div id="loader" class="spinner-border text-primary"></div>
    <?php 
    $helper->getSkuFields(); 
    ?>
    <span id="timelapses"><a href="#timelapse">Timelapse</a></span>
</div>

<div class="container-fluid">
    <div id="imgContainer" class="row"></div>



    <div id="timelapse" class="embed-responsive embed-responsive-16by9">
        <iframe class="embed-responsive-item" src="http://82.65.203.4/motion/files/timelapse/timelapse/timelapse.mp4" allowfullscreen></iframe>
    </div>



</div>

<script src="js/jquery.js"></script>
<script src="js/js.js?v=<?php echo rand(0,999999) ?>"></script>
<script>

</script>


</body>
</html>