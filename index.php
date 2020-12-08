<?php 
include_once('http-auth.php');
include_once('classes/helper.class.php');
$helper = new Helper();
?>

<html>
<head>
<title>Webcam O²</title>
<script type="text/javascript" src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
<link rel="stylesheet" href="css/css.css?v=<?php echo rand(0,999999) ?>">
</head>
<body>
<div id="general-infos">
    <?php 
    $helper->getSkuFields(); 
    ?>
</div>

<div id="loader"></div>
<div class="container-fluid">
    <div id="imgContainer" class="row"></div>
</div>
<script src="js/js.js?v=<?php echo rand(0,999999) ?>"></script>
</body>
</html>