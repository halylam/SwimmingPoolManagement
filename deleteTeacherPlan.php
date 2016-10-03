<?php

ob_start();
session_start();
require_once("Includes/db.php");
if (isset($_SESSION['userID'])) {
    $idUser = $_SESSION['userID'];
    $userType = $_SESSION['userType'];
    $fullname = $_SESSION['fullname'];
} else {
    header('Location: index.php');
    exit;
}
if ($userType != 'Admin') {
	header('Location: index.php');
	exit;
}
if ($userType == 'Admin') {
    DBUtil::getInstance()->deleteTeacherPlan($_GET['id']);
}
header('Location: listTeacherPlan.php');
?>