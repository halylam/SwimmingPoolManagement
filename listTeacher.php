<?php
ob_start();
session_start();
require_once("Includes/db.php");
if (isset($_SESSION['userID'])) {
    $userId = $_SESSION['userID'];
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
        <title>Danh sách giáo viên</title>
    </head>
    <body>
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>DANH SÁCH GIÁO VIÊN</center></h2>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <input id="filter" type="text" class="form-control" placeholder="Nhập tiêu chí lọc..." />
            </div>
            <table class="table">
                <tr>
                <th>Mã giáo viên</th>
                <th>Tên Giáo Viên</th>
                <th>Thao tác</th>

            </tr>
            <?php
            $listTeacher = DBUtil::getInstance()->getListTeacher();

            while ($row = mysqli_fetch_array($listTeacher)) {
                $idTeacher = $row["idTeacher"];
                echo "<tr><td>" . htmlentities($idTeacher) . "</td>";
                echo "<td>" . htmlentities($row["name"]) . "</td>";
                echo "<td><a href='editTeacher.php?idTeacher=$idTeacher'><i class='glyphicon glyphicon-pencil'></i></a>"
                    . "<a href='deleteTeacher.php?idTeacher=" . $idTeacher . "' onClick=\"javascript:return confirm('Bạn có chắc chắn xóa giáo viên ko?');\">"
                        . "<i style='margin-left: 15px; color: red;' class='glyphicon glyphicon-remove'></i></a></td><tr>";
            }
            mysqli_free_result($listTeacher);
            ?>
            </table>
        </div>
        
        <br/>
        <input class="btn btn-success" type="button" value="Thêm mới giáo viên" onClick="document.location.href = 'newTeacher.php'" />     

        <input class="btn btn-success" type="button" value="Trang chủ" onClick="document.location.href = 'mainPage.php'" />

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
