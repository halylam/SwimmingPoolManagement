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
        <title>Danh sách nhân viên</title>
    </head>
    <body>
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>DANH SÁCH NHÂN VIÊN</center></h2>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <input id="filter" type="text" class="form-control" placeholder="Nhập tiêu chí lọc..." />
            </div>
            <table class="table">
               <tr>
                <th>Tên Đăng Nhập</th>
                <th>Họ Tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Ngày sinh</th>
                <th>Ảnh cá nhân</th>
                <th>Thao tác</th>
            </tr>
            <?php
                $listUser = DBUtil::getInstance()->getListUser();
            

            while ($row = mysqli_fetch_array($listUser)) {
                $userSelectedId = $row["idUser"];
                echo "<tr><td style='vertical-align: middle;'>" . htmlentities($row["login"]) . "</td>";
                echo "<td style='vertical-align: middle;'>" . htmlentities($row["fullname"], ENT_QUOTES, 'utf-8') . "</td>";
                echo "<td style='vertical-align: middle;'>" . htmlentities($row["email"]) . "</td>";
                echo "<td style='vertical-align: middle;'>" . htmlentities($row["phone"]) . "</td>";
                echo "<td style='vertical-align: middle;'>" . htmlentities($row["address"], ENT_QUOTES, 'utf-8') . "</td>";
                echo "<td style='vertical-align: middle;'>" . date('d-m-Y', strtotime(htmlentities($row["birthday"]))) . "</td>";
                echo "<td style='vertical-align: middle;'><img src='" . htmlentities($row["avatar"]) . "' style='width:100px;height:60px;'</td>";
                echo "<td style='vertical-align: middle;'><a href='editUser.php?idUser=$userSelectedId&source=2'><i class='glyphicon glyphicon-pencil'></i></a>"
                        . "<a href='deleteUser.php?idUser=" . $userSelectedId . "' onClick=\"javascript:return confirm('Bạn có chắc chắn xóa nhân viên ko?');\">"
                        . "<i style='margin-left: 15px; color: red;' class='glyphicon glyphicon-remove'></i></a></td><tr>";
            }
            mysqli_free_result($listUser);
            ?>
            </table>
        </div>
        <br/>
        <input class="btn btn-success"  type="button" value="Thêm mới nhân viên" onClick="document.location.href = 'newUser.php'" />     

        <input class="btn btn-success"  type="button" value="Trang chủ" onClick="document.location.href = 'mainPage.php'" />
    </body>
    <script>
        $(document).ready(function () {
            (function ($) {
                $('#filter').keyup(function () {
                    var rex = new RegExp($(this).val(), 'i');
                    $('.table tr').hide();
                    $('.table tr').filter(function () {
                        return rex.test($(this).text());
                    }).show();
                })
            }(jQuery));
        });
    </script>
</html>
