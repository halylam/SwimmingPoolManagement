<?php
ob_start();
session_start();
require_once("Includes/db.php");
if (isset($_SESSION['userID'])) {
    $idUser = $_SESSION['userID'];
    $fullname = $_SESSION['fullname'];
} else {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $loginFilter = '';
    $typeNameFilter = '';
    $fromFilter = date('d-m-Y 00:00');
    $toFilter = date('d-m-Y 23:59');
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $loginFilter = $_POST["login"];
    $typeNameFilter = $_POST["typeName"];
    $fromFilter = $_POST["fromDate"];
    $toFilter = $_POST["toDate"];
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
        <title>Thống Kê</title>
    </head>
    <body>
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>THỐNG KÊ BÁO CÁO</center></h2>

        <div style="width: 100%"><center>
            <form class="form-horizontal" role="form" action="thongke.php" method="POST" >
                <div class="form-group">
                    <label class="control-label col-sm-2" for="login">Mã nhân viên: </label>
                    <div class="col-sm-10" style="width: 15%">
                        <select class="selectpicker"  name='login' id='login' value='<?php echo $loginFilter ?>'>
                            <option value=''>Chọn</option>
                            <?php
                            $listUser = DBUtil::getInstance()->getListUser();
                            while ($row = mysqli_fetch_array($listUser)) {
                                if ($loginFilter == $row["login"]) {
                                    $selected = ' selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option value='" . htmlentities($row["login"]) . "'".$selected.">" . htmlentities($row["login"]) . "</option>\n";
                            }
                            mysqli_free_result($listCardType);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pass">Loại thẻ:</label>
                    <div class="col-sm-10" style="width: 15%"> 
                        <select class="selectpicker"  name='typeName' id='typeName' value='<?php echo $typeNameFilter ?>'>
                            <option value=''>Chọn</option>
                            <?php
                            $listCardType = DBUtil::getInstance()->getListCardType();
                            while ($row = mysqli_fetch_array($listCardType)) {
                                if ($typeNameFilter == $row["typeName"]) {
                                    $selected = ' selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option value='" . htmlentities($row["typeName"]) ."'".$selected.">" . htmlentities($row["typeName"]) . "</option>\n";
                            }
                            mysqli_free_result($listCardType);
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="fromDate">Từ Ngày: </label>
                    <div class="col-sm-10" style="width: 75%">
                        <input type="text" class="form-control" id="datetimepicker" name="fromDate" value="<?php echo $fromFilter ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="toDate">Đến Ngày: </label>
                    <div class="col-sm-10" style="width: 75%">
                        <input type="text" class="form-control" id="datetimepicker1" name="toDate" value="<?php echo $toFilter ?>" />
                    </div>
                </div>

                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-10" style="width: 55%">
                        <button type="submit" class="btn btn-success">Tìm Kiếm</button> 
                        <input type="button" class="btn btn-success" value="Trang chủ" onClick="document.location.href = 'mainPage.php'" />
                    </div>
                </div>
                
                 <div style="width: 65%">
                     <?php
                            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                                $loginFilter = $_POST["login"];
                                $typeNameFilter = $_POST["typeName"];
                                if ($_POST["fromDate"] == '') {
                                    $fromFilter = '';
                                } else {
                                    $fromFilter = date('Y-m-d H:i:s', strtotime($_POST["fromDate"]));
                                }
                                if ($_POST["toDate"] == '') {
                                    $toFilter = '';
                                } else {
                                    $toFilter = date('Y-m-d H:i:s', strtotime($_POST["toDate"]));
                                }
                                $listTransInfo = DBUtil::getInstance()->getListTransactionInfo($loginFilter, $typeNameFilter, $fromFilter, $toFilter);
                                echo("<div class='alert alert-info'><h3>Tổng Lượt Khách= <strong>".number_format($listTransInfo['totalCount'], 0, '.', ',')."</strong> -/- Tổng Doanh Thu= <strong>".number_format($listTransInfo['totalPrice'], 0, '.', ',')." VNĐ</strong> </h3></div>");
                            }
                            ?>
                 </div>

                <div class="panel panel-default">
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Mã Loại Thẻ</th>
                                <th>Loại Thẻ</th>
                                <th>Giá</th>
                                <th>Ngày giờ quẹt</th>
                                <th>Nhân viên trực</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                                $loginFilter = $_POST["login"];
                                $typeNameFilter = $_POST["typeName"];
                                if ($_POST["fromDate"] == '') {
                                    $fromFilter = '';
                                } else {
                                    $fromFilter = date('Y-m-d H:i:s', strtotime($_POST["fromDate"]));
                                }
                                if ($_POST["toDate"] == '') {
                                    $toFilter = '';
                                } else {
                                    $toFilter = date('Y-m-d H:i:s', strtotime($_POST["toDate"]));
                                }
                                $listTrans = DBUtil::getInstance()->getListTransaction($loginFilter, $typeNameFilter, $fromFilter, $toFilter);
                                while ($row = mysqli_fetch_array($listTrans)) {
                                    echo "<tr><td>" . htmlentities($row["cardCode"]) . "</td>";
                                    echo "<td>" . htmlentities($row["typeName"]) . "</td>";
                                    echo "<td>" . htmlentities($row["price"]) . "</td>";
                                    echo "<td>" . date('d-m-Y H:i:s', strtotime(htmlentities($row["tranDate"]))) . "</td>";
                                    echo "<td>" . htmlentities($row["login"]) . "</td></tr>";
                                }
                                mysqli_free_result($listTrans);
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
            </form>
        </center></div>
    </body>

    <script>
        jQuery('#datetimepicker').datetimepicker({
            format: 'd-m-Y H:i'
        });
        jQuery('#datetimepicker1').datetimepicker({
            format: 'd-m-Y H:i'
        });

        $(document).ready(function () {
            $('#example').DataTable();
        });
        
        function replaceAll(str, find, replace) {
            return str.replace(new RegExp(find, 'g'), replace);
        }
        function formatNumber(obj) {
            var tmp = replaceAll(obj.value.toString(), ',', '');
            obj.value = tmp.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
</html>
