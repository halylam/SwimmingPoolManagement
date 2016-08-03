<?php
  require_once("Includes/db.php");
  
  DBUtil::getInstance()->deleteTeacherPlan($_GET['id']);
  header('Location: listTeacherPlan.php' );
?>