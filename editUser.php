<?php
ob_start();
session_start();
require_once("Includes/db.php");
$loginIsEmpty = false;
$passIsEmpty = false;
$invalidEmail = false;
if (isset($_SESSION['userID'])) {
    $idUser = $_SESSION['userID'];
    $userType = $_SESSION['userType'];
    $fullname = $_SESSION['fullname'];
} else {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['idUser'])) {
        $userSelectedId = $_GET['idUser'];
        $source = $_GET['source'];
        $userItem = DBUtil::getInstance()->getUserDetail($userSelectedId);
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $userItem = array("idUser" => $_POST['idUser'],
        "login" => $_POST['login'],
        "pass" => $_POST['pass'],
        "fullname" => $_POST['fullname'],
        "phone" => $_POST['phone'],
        "address" => $_POST['address'],
        "birthday" => $_POST['birthday'],
        "email" => $_POST['email'],
        "avatar" => $_POST['avatar']);
    
    $source = $_POST['source'];
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
        <title>Thông tin nhân viên</title>
    </head>
    <body> 
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>THÔNG TIN CHI TIẾT NHÂN VIÊN</center></h2>
        <div style="width: 90%">
            <form class="form-horizontal" role="form" action="editUser.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="source" value="<?php echo $source; ?>" />
                <input type="hidden" name="idUser" value="<?php echo $userItem["idUser"]; ?>" />
                <div class="form-group">
                    <label class="control-label col-sm-2" for="avatar">Ảnh cá nhân:</label>
                    <div class="col-sm-10">
                        <input type="hidden" class="form-control" name="avatar" value="<?php echo $userItem["avatar"]; ?>" />
                        <img src="<?php echo $userItem["avatar"]; ?>" style="width:150px;height:110px;">
                        <input id="fileToUpload" type="file" name="fileToUpload" class="file">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="login">Tên Đăng Nhập<label style="color: red">(*)</label>: </label>
                    <div class="col-sm-10">
                        <input readonly="true" type="text" class="form-control" name="login" value="<?php echo $userItem["login"]; ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pass">Mật Khẩu<label style="color: red">(*)</label>:</label>
                    <div class="col-sm-10"> 
                        <input type="password" class="form-control" id="pass" name="pass" value="<?php echo $userItem["pass"]; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="fullname">Họ Tên: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="fullname" value="<?php echo $userItem["fullname"]; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="phone">Số điện thoại: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="phone" value="<?php echo $userItem["phone"]; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="address">Địa chỉ: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="address" value="<?php echo $userItem["address"]; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="birthday">Ngày sinh: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="datetimepicker" name="birthday" value="<?php echo date('d-m-Y', strtotime($userItem["birthday"])); ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="email">Email:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="email" value="<?php echo $userItem["email"]; ?>" />
                    </div>
                </div>
                
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-10">
                        <input class="btn btn-success" type="<?php if ($userType == 'Admin') echo 'submit'; else echo 'hidden'; ?>" value="Lưu chỉnh sửa" />     
                        <input type="button" class="btn btn-success" value="Trang chủ" onClick="document.location.href = 'mainPage.php'" />
                    </div>
                </div>
            </form>
        </div>
        <?php
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $mess = '';
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir);
            }
            $uploadOk = 1;
            if ($_FILES["fileToUpload"]["name"] != '') {
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $_POST["avatar"] = $target_file;
                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                if ($_FILES["fileToUpload"]["size"] > 1024000) {
                    $mess = "Dung lượng file quá lơn. File phải nhỏ hơn 1Mb.";
                    $uploadOk = 0;
                }
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $mess = "chỉ JPG, JPEG, PNG, GIF files được chấp nhận.";
                    $uploadOk = 0;
                }
            }
            
            if ($_POST['login'] == "") {
                $loginIsEmpty = true;
                $mess = $mess . "<br/>Tên đăng nhập bắt buộc nhập ";
            }
            if ($_POST['pass'] == "") {
                $passIsEmpty = true;
                $mess = $mess . "<br/>Mật khẩu bắt buộc nhập ";
            }
            if ($_POST['email'] != "") {
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $invalidEmail = true;
                    $mess = $mess . "<br/>Email không đúng định dạng. vd: abc@gmail.com ";
                }
            }

            if (!$loginIsEmpty && !$passIsEmpty && !$invalidEmail && $uploadOk == 1) {
                move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
                DBUtil::getInstance()->updateUser($_POST["idUser"], $_POST["login"], $_POST["pass"], $_POST["fullname"], $_POST["phone"], $_POST["address"], date('Y-m-d H:i:s', strtotime($_POST["birthday"])), $_POST["email"], $_POST["avatar"]);
                if ($_POST["source"] == 1)
                    header('Location: mainPage.php');
                if ($_POST["source"] == 2)
                    header('Location: listUser.php');
                exit;
            } else {
                echo("<div class='alert alert-danger'><strong>Lỗi!</strong> " . $mess . "</div>");
            }
        }
        ?>
    </body>
    <script>
        jQuery('#datetimepicker').datetimepicker({
            timepicker: false,
            format: 'd-m-Y'
        });
    </script>
</html>
