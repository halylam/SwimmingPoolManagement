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
    $monthFilter = date('m');
    $yearFilter = date('Y');
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $monthFilter = $_POST["month"];
    $yearFilter = $_POST["year"];
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
        <h2 style="color: #188420;"><center>THỐNG KÊ TIỀN DẠY</center></h2>
        <div style="width: 100%"><center>
                <form class="form-horizontal" role="form" action="tienday.php" method="POST" >
                    <div class="form-group">
                        <label class="control-label col-sm-2">Chọn Tháng Năm:</label>
                        <div class="col-sm-10" style="width: 35%"> 
                            <select class="selectpicker"  name='month' id='month' value='<?php echo $monthFilter ?>'>
                                <?php
                                for ($x = 1; $x <= 12; $x++) {
                                    if ($monthFilter == $x) {
                                        $selected = ' selected="selected"';
                                    } else {
                                        $selected = '';
                                    }
                                    echo "<option value='" . $x . "'" . $selected . ">Tháng " . $x . "</option>\n";
                                }
                                ?>
                            </select>
                            <select class="selectpicker"  name='year' id='year' value='<?php echo $yearFilter ?>'>
                                <?php
                                for ($y = date('Y'); $y >= (date('Y')-10); $y--) {
                                    if ($yearFilter == $y) {
                                        $selected = ' selected="selected"';
                                    } else {
                                        $selected = '';
                                    }
                                    echo "<option value='" . $y . "'" . $selected . ">" . $y . "</option>\n";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group"> 
                        <div class="col-sm-offset-2 col-sm-10" style="width: 65%">
                            <button type="submit" class="btn btn-success">Tìm Kiếm</button> 
                            <input type="button" class="btn btn-success" value="Trang chủ" onClick="document.location.href = 'mainPage.php'" />
                        </div>
                    </div>

                    <div style="width: 100%; " >
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == "POST") {
                            $monthFilter = $_POST["month"];
                            $yearFilter = $_POST["year"];
                            $tempInfo = "";
                            $listTeacherPlanInfo = DBUtil::getInstance()->getTeacherPlanInfo($monthFilter, $yearFilter);
                            while ($row = mysqli_fetch_array($listTeacherPlanInfo)) {
                                $tempInfo .= "<div style='width: 20%; display: inline-block; margin-left: 5px;' class='alert alert-info'>Giáo Viên: <font size='4'><strong>" . $row['name'] . "</strong></font><hr style='margin: 5px; background-color: #419641; height: 1px;'>Tổng Học Viên: <font size='4'><strong>" . number_format($row['amount'], 0, '.', ',') . "</strong></font></br>Tổng Tiền: <font size='4'><strong>" . number_format($row['total'], 0, '.', ',') . " VNĐ</strong></font></div>";
                            }
                            mysqli_free_result($listTeacherPlanInfo);
                            if($tempInfo != '') {
                                echo("<div class='row'>" . $tempInfo . "</div>");
                            } else {
                                $tempInfo .= "<div style='width: 25%; display: inline-block; margin-left: 5px;' class='alert alert-danger'><font size='4'><strong>Không có dữ liệu tiền dạy tháng ".$monthFilter."/".$yearFilter."</strong></font></div>";
                                echo("<div class='row'>" . $tempInfo . "</div>");
                            }
                            
                        }
                        ?>
                    </div>
                </form>
            </center></div>
    </body>
</html>
