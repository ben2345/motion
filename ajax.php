<?php
session_start();
include_once('classes/helper.class.php');
$helper = new Helper();

$action = $_POST['action'];
$id = $_POST['id'];
//todo
//$data = array('error', 'Erreur réponse ajax');

switch ($helper->action) {
  case 'getSku':
      $data[$helper->id] = $helper->getSku($id);
    break;
  case 'deleteFile':
      // todo delete by path date/files.gif
      $data[$helper->action] = $helper->deleteFIle();
    break;
  case 'getFiles':
    $data = $helper->getFiles();
    break;
}

die(json_encode($data));
