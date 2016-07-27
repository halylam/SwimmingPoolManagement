<?php
  require_once("Includes/db.php");
  
  DBUtil::getInstance()->deleteCardType($_GET['idCardType']);
  header('Location: listCardType.php' );
?>