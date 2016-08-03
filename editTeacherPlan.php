<?php
ob_start();
session_start();
require_once("Includes/db.php");
$teacherIsEmpty = false;
$studentNameIsEmpty = false;
$endDateIsEmpty = false;
$feeIsEmpty = false;
$rateIsEmpty = false;
if (isset($_SESSION['userID'])) {
    $idUser = $_SESSION['userID'];
    $userType = $_SESSION['userType'];
    $fullname = $_SESSION['fullname'];
} else {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['id'])) {
        $teacherPlanSelectedId = $_GET['id'];
        $teacherPlanItem = DBUtil::getInstance()->getTeacherPlanDetail($teacherPlanSelectedId);
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $teacherPlanItem = array("id" => $_POST['id'],
        "idTeacher" => $_POST['idTeacher'],
        "studentName" => $_POST['studentName'],
        "endDate" => $_POST['endDate'],
        "fee" => $_POST['fee'],
        "rate" => $_POST['rate']);
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
        <title>Thông tin lịch dạy</title>
    </head>
    <body> 
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>THÔNG TIN CHI TIẾT LỊCH DẠY</center></h2>
        <div style="width: 90%">
            <form class="form-horizontal" role="form" action="editTeacherPlan.php" method="POST" >
                <input type="hidden" name="id" value="<?php echo $teacherPlanItem["id"]; ?>" />
                <div class="form-group">
                    <label class="control-label col-sm-2" for="idTeacher">Tên Giáo Viên<label style="color: red">(*)</label>: </label>
                    <div class="col-sm-10">
                        <select class="selectpicker"  name='idTeacher' id='idTeacher' value='<?php echo $teacherPlanItem["idTeacher"]; ?>'>
                            <option value=''>Chọn</option>
                            <?php
                            $listTeacher = DBUtil::getInstance()->getListTeacher();

                            while ($row = mysqli_fetch_array($listTeacher)) {
                                if ($teacherPlanItem["idTeacher"] == $row["idTeacher"]) {
                                    $selected = ' selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option value=" . htmlentities($row["idTeacher"]) . $selected . ">" . htmlentities($row["name"]) . "</option>\n";
                            }
                            mysqli_free_result($listTeacher);
                            ?>
                        </select>
                    </div>
                </div>
                 <div class="form-group">
                    <label class="control-label col-sm-2" for="studentName">Tên học viên<label style="color: red">(*)</label>:</label>
                    <div class="col-sm-10"> 
                        <input type="text" class="form-control" name="studentName" value="<?php echo $teacherPlanItem["studentName"]; ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="endDate">Ngày kết thúc<label style="color: red">(*)</label>: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="datetimepicker" name="endDate" value="<?php echo $teacherPlanItem["endDate"]; ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="fee">Học phí<label style="color: red">(*)</label>:</label>
                    <div class="col-sm-10"> 
                        <input type="text" class="form-control numberOnly" name="fee" value="<?php echo $teacherPlanItem["fee"]; ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="rate">Phần trăm thầy<label style="color: red">(*)</label>:</label>
                    <div class="col-sm-10"> 
                        <input type="text" class="form-control numberOnly" name="rate" value="<?php echo $teacherPlanItem["rate"]; ?>" />
                    </div>
                </div>

                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success">Lưu chỉnh sửa</button> 
                        <input type="button" class="btn btn-success" value="Trở lại" onClick="history.back();" />
                    </div>
                </div>
            </form>
        </div>
        <?php
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $mess = '';
            if ($_POST['idTeacher'] == "") {
                $teacherIsEmpty = true;
                $mess = $mess . "<br/>Giáo viên bắt buộc nhập ";
            } 
            if ($_POST['studentName'] == "") {
                $studentNameIsEmpty = true;
                $mess = $mess . "<br/>Tên học viên bắt buộc nhập ";
            } 
             if ($_POST['endDate'] == "") {
                $endDateIsEmpty = true;
                $mess = $mess . "<br/>Ngày kết thúc bắt buộc nhập ";
            } 
            if ($_POST['fee'] == "") {
                $feeIsEmpty = true;
                $mess = $mess . "<br/>Học phí bắt buộc nhập ";
            }  
            if ($_POST['rate'] == "") {
                $rateIsEmpty = true;
                $mess = $mess . "<br/>Phần trăm bắt buộc nhập ";
            } else {
                $rate = str_replace(',', '', $_POST["rate"]);
                if ($rate > 100) {
                    $rateIsEmpty = true;
                    $mess = $mess . "<br/>Phần trăm phải <= 100 ";
                }
            }
            if (!$teacherIsEmpty && !$studentNameIsEmpty && !$endDateIsEmpty && !$feeIsEmpty && !$rateIsEmpty) {
                DBUtil::getInstance()->updateTeacherPlan($_POST["id"], $_POST["idTeacher"], $_POST["studentName"], date('Y-m-d H:i:s', strtotime($_POST["endDate"])), str_replace(',', '', $_POST["fee"]), $rate);
                header('Location: listTeacherPlan.php');
                exit;
            } else {
                echo("<div class='alert alert-danger'><strong>Lỗi!</strong> " . $mess . "</div>");
            }
            
        }
        ?>
    </body>
    <script>
        $(".numberOnly").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 46 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            } else {
                $('input').keyup(function () {
                    formatNumber(this);
                });
            }
        });

        $(".numberOnly").change(function () {
            formatNumber(this);
        });

        function replaceAll(str, find, replace) {
            return str.replace(new RegExp(find, 'g'), replace);
        }
        function formatNumber(obj) {
            var tmp = replaceAll(obj.value.toString(), ',', '');
            obj.value = tmp.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $(document).ready(function () {
            $(".numberOnly").each(function () {
                formatNumber(this);
            });
        });
        
         jQuery('#datetimepicker').datetimepicker({
            timepicker: false,
            format: 'd-m-Y'
        });
        
    </script>
</html>
