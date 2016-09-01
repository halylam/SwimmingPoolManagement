<?php
ob_start();
session_start();
require_once("Includes/db.php");
$typeNameIsEmpty = false;
$priceIsEmpty = false;
if (isset($_SESSION['userID'])) {
    $idUser = $_SESSION['userID'];
    $userType = $_SESSION['userType'];
    $fullname = $_SESSION['fullname'];
} else {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['idCardType'])) {
        $cardTypeSelectedId = $_GET['idCardType'];
        $cardTypeItem = DBUtil::getInstance()->getCardTypeDetail($cardTypeSelectedId);
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $cardTypeItem = array("idCardType" => $_POST['idCardType'],
        "typeName" => $_POST['typeName'],
        "price" => $_POST['price']);
    
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
        <h2 style="color: #188420;"><center>THÔNG TIN CHI TIẾT LOẠI THẺ</center></h2>
        <div style="width: 90%">
            <form class="form-horizontal" role="form" action="editCardType.php" method="POST" >
                <input type="hidden" name="idCardType" value="<?php echo $cardTypeItem["idCardType"]; ?>" />
                <input type="hidden" name="typeNameOld" value="<?php echo $cardTypeItem["typeName"]; ?>" />
                <input type="hidden" name="priceOld" value="<?php echo $cardTypeItem["price"]; ?>" />
                <div class="form-group">
                    <label class="control-label col-sm-2" for="typeName">Tên Loại Thẻ<label style="color: red">(*)</label>: </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="typeName" value="<?php echo $cardTypeItem["typeName"]; ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="price">Giá thẻ<label style="color: red">(*)</label>:</label>
                    <div class="col-sm-10"> 
                        <input type="text" class="form-control numberOnly" name="price" value="<?php echo $cardTypeItem["price"]; ?>" />
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
            if ($_POST['typeName'] == "") {
                $typeNameIsEmpty = true;
                $mess = $mess . "<br/>Tên loại thẻ bắt buộc nhập ";
            } else {
                if ($_POST['typeName'] != $_POST['typeNameOld']) {
                    $cardTypeExisted = DBUtil::getInstance()->checkCardTypeExisted($_POST['typeName']);
                    if ($cardTypeExisted != null) {
                        $typeNameIsEmpty = true;
                        $mess = $mess . "<br/>Tên loại thẻ đã tồn tại ";
                    }
                }
            }
            if ($_POST['price'] == "") {
                $priceIsEmpty = true;
                $mess = $mess . "<br/>Giá bắt buộc nhập ";
            }

            if (!$typeNameIsEmpty && !$priceIsEmpty ) {
                DBUtil::getInstance()->updateCardType($_POST["idCardType"], $_POST["typeName"], str_replace(',', '', $_POST["price"]), $_POST["typeNameOld"], $_POST["priceOld"]);
                header('Location: listCardType.php');
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

    </script>
</html>
