<?php
  require_once("Includes/db.php");
  
  DBUtil::getInstance()->deleteUser($_GET['idUser']);
  header('Location: listUser.php' );
?>