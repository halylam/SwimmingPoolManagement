<?php
  require_once("Includes/db.php");
  
  DBUtil::getInstance()->deleteTeacher($_GET['idTeacher']);
  header('Location: listTeacher.php' );
?>