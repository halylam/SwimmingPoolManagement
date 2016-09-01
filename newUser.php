<?php
ob_start();
session_start();
require_once("Includes/db.php");
$nameIsEmpty = false;
$phoneIsEmpty = false;
$invalidEmail = false;
if (isset($_SESSION['userID'])) {
    $idUser = $_SESSION['userID'];
    $userType = $_SESSION['userType'];
    $fullname = $_SESSION['fullname'];
} else {
    header('Location: index.php');
    exit;
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
        <title>Thêm mới nhân viên</title>
    </head>
    <body>
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>THÔNG TIN CÁ NHÂN</center></h2>

        <div style="width: 90%">
            <form class="form-horizontal" role="form" action="newUser.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="login">Tên Đăng Nhập<label style="color: red">(*)</label>: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="login" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pass">Mật Khẩu<label style="color: red">(*)</label>:</label>
                    <div class="col-sm-10"> 
                        <input type="password" class="form-control" id="pass" name="pass" value="" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="fullname">Họ Tên: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="fullname" value="" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="phone">Số điện thoại: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="phone" value="" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="address">Địa chỉ: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="address" value="" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="birthday">Ngày sinh: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="datetimepicker" name="birthday" value="" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="email">Email:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="email" value="" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="avatar">Ảnh cá nhân:</label>
                    <div class="col-sm-10">
                        <input type="hidden" class="form-control" name="avatar" value="" />
                        <input id="fileToUpload" type="file" name="fileToUpload" class="file">
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
            } else {
                $userExisted = DBUtil::getInstance()->checkUserExisted($_POST['login']);
                if ($userExisted != null) {
                    $loginIsEmpty = true;
                    $mess = $mess . "<br/>Tên đăng nhập đã tồn tại ";
                }
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
                DBUtil::getInstance()->insertUser($_POST["login"], $_POST["pass"], $_POST["fullname"], $_POST["phone"], $_POST["address"], date('Y-m-d H:i:s', strtotime($_POST["birthday"])), $_POST["email"], $_POST["avatar"]);
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
