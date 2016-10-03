<?php
ob_start();
session_start();
require_once("Includes/db.php");
$nameIsEmpty = false;
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

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['idTeacher'])) {
        $teacherSelectedId = $_GET['idTeacher'];
        $teacherItem = DBUtil::getInstance()->getTeacherDetail($teacherSelectedId);
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $teacherItem = array("idTeacher" => $_POST['idTeacher'],
        "name" => $_POST['name']);
    
}
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Thông tin giáo viên</title>
    </head>
    <body> 
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>THÔNG TIN CHI TIẾT GIÁO VIÊN</center></h2>
        <div style="width: 90%">
            <form class="form-horizontal" role="form" action="editTeacher.php" method="POST" >
                <input type="hidden" name="idTeacher" value="<?php echo $teacherItem["idTeacher"]; ?>" />
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Tên Giáo Viên<label style="color: red">(*)</label>: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="name" value="<?php echo $teacherItem["name"]; ?>" />
                    </div>
                </div>

                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-10">
                        <input class="btn btn-success" type="<?php if ($userType == 'Admin') echo 'submit'; else echo 'hidden'; ?>" value="Lưu chỉnh sửa" />     
                        <input type="button" class="btn btn-success" value="Trở lại" onClick="history.back();" />
                    </div>
                </div>
            </form>
        </div>
        <?php
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $mess = '';
            if ($_POST['name'] == "") {
                $nameIsEmpty = true;
                $mess = $mess . "<br/>Tên giáo viên bắt buộc nhập ";
            } 

            if (!$nameIsEmpty) {
                DBUtil::getInstance()->updateTeacher($_POST["idTeacher"], $_POST["name"]);
                header('Location: listTeacher.php');
                exit;
            } else {
                echo("<div class='alert alert-danger'><strong>Lỗi!</strong> " . $mess . "</div>");
            }
        }
        ?>
    </body>
</html>
