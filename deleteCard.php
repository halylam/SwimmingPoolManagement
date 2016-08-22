<?php
  require_once("Includes/db.php");
  
  DBUtil::getInstance()->deleteCard($_GET['idCard']);
  header('Location: listCard.php' );
?>