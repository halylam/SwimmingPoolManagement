<?php
ob_start();
session_start();
require_once("Includes/db.php");
$cardTypeIsEmpty = false;
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
    if (isset($_GET['idCard'])) {
        $cardSelectedId = $_GET['idCard'];
        $cardItem = DBUtil::getInstance()->getCardDetail($cardSelectedId);
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $cardItem = array("idCard" => $_POST['idCard'],
        "cardCode" => $_POST['cardCode'],
        "longTerm" => $_POST['longTerm'],
        "remainTimes" => $_POST['remainTimes'],
        "idCardType" => $_POST['idCardType']);
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
        <title>Thông tin loại thẻ</title>
    </head>
    <body> 
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>THÔNG TIN CHI TIẾT THẺ</center></h2>
        <div style="width: 90%">
            <form class="form-horizontal" role="form" action="editCard.php" method="POST" >
                <input type="hidden" name="idCard" value="<?php echo $cardItem["idCard"]; ?>" />
                <div class="form-group">
                    <label class="control-label col-sm-2" for="cardCode">Mã thẻ<label style="color: red">(*)</label>:</label>
                    <div class="col-sm-10"> 
                        <input type="text" readonly="true" class="form-control" name="cardCode" value="<?php echo $cardItem["cardCode"]; ?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="idCardType">Tên Loại Thẻ<label style="color: red">(*)</label>: </label>
                    <div class="col-sm-10">
                        <select class="selectpicker"  name='idCardType' id='idCardType' value='<?php echo $cardItem["idCardType"]; ?>'>
                            <option value=''>Chọn</option>
                            <?php
                            $listCardType = DBUtil::getInstance()->getListCardType();

                            while ($row = mysqli_fetch_array($listCardType)) {
                                if ($cardItem["idCardType"] == $row["idCardType"]) {
                                    $selected = ' selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option value=" . htmlentities($row["idCardType"]) . $selected . ">" . htmlentities($row["typeName"], ENT_QUOTES, 'utf-8') . "</option>\n";

                            }
                            mysqli_free_result($listCardType);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="longTerm">Thẻ Dài Hạn:</label>
                    <div class="col-sm-10"> 
                        <input type="checkbox" name="longTerm" value="<?php echo $cardItem['longTerm']; ?>" <?php if ($cardItem['longTerm'] == 1) echo "checked='checked'"; ?>/>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="remainTimes">Số lần quẹt còn lại:</label>
                    <div class="col-sm-10"> 
                        <input type="text" class="form-control numberOnly" name="remainTimes" value="<?php echo $cardItem["remainTimes"]; ?>" />
                    </div>
                </div>

                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-10">
                        <input class="btn btn-success" type="<?php if ($userType == 'Admin' || 1==1) echo 'submit'; else echo 'hidden'; ?>" value="Lưu chỉnh sửa" />     
                        <input type="button" class="btn btn-success" value="Trở lại" onClick="history.back();" />
                    </div>
                </div>
            </form>
        </div>
        <?php
        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $mess = '';
            if ($_POST['idCardType'] == "") {
                $cardTypeIsEmpty = true;
                $mess = $mess . "<br/>Loại thẻ bắt buộc nhập ";
            } 
            if (!$cardTypeIsEmpty) {
                if(isset($_POST['longTerm'])) $longTerm = 1; else $longTerm = 0;
                DBUtil::getInstance()->updateCard($_POST["idCard"], $_POST["idCardType"], $longTerm, $_POST["remainTimes"]);
                header('Location: listCard.php');
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
            } 
        });
    </script>
</html>
