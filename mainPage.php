<?php
ob_start();
session_start();
require_once("Includes/db.php");


if (isset($_SESSION['userID'])) {
    $idUser = $_SESSION['userID'];
    $login = $_SESSION['login'];
    $fullname = $_SESSION['fullname'];
    $userType = $_SESSION['userType'];
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
        <title>Trang chủ</title>
    </head>
    <body>        
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>KIỂM SOÁT VÀO CỔNG</center></h2>

        <div style="width: 100%"><center>
            <form class="form-horizontal" role="form" action="mainPage.php" method="POST" >
                <div class="form-group">
                    <label class="control-label col-sm-2" for="cardCode">Mã Thẻ: </label>
                    <div class="col-sm-10" style="width: 75%">
                        <input type="text" placeholder="Quét mã vạch thẻ từ" class="form-control" name="cardCode" id="cardCode" value="" onchange="loadCard(this.value);" />
                    </div>
                </div>
                <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" style="display: none;" class="btn btn-success" id="agree">Đồng ý</button> 
                    </div>
                </div>
            </form>
            <div style="width: 65%">
            <?php
                /** Check that the page was requested from itself via the POST method. */

                if ($_SERVER['REQUEST_METHOD'] == "POST") {            
                    $cardCode = $_POST['cardCode'];
                    $cardItem = DBUtil::getInstance()->getCardDetailByCode($cardCode);
                    if ($cardItem != null) {
                        if ($cardItem["longTerm"] == 1 && $cardItem["remainTimes"] == 0) {
                            echo("<div class='alert alert-danger'><strong>Lỗi!</strong> Thẻ dài hạn đã hết lượt quẹt thẻ.!</div>");
                        } else {
                            $date = date('Y-m-d H:i:s');
                            DBUtil::getInstance()->insertTransaction($cardCode, $cardItem["typeName"], $cardItem["price"], $date, $login);
                            if ($cardItem["longTerm"] == 1) {
                                DBUtil::getInstance()->minusRemainTimes($cardCode);
                            }
                            echo("<div  class='alert alert-info'><h3><center><strong>THÀNH CÔNG!</strong> Loại Thẻ: ".$cardItem["typeName"]." - Mệnh Giá: ".$cardItem["price"]."</center></h3></div>");
                        }
                    } else {
                        echo("<div class='alert alert-danger'><h3><center><strong>Lỗi!</strong> Thẻ chưa được đăng ký trong hệ thông!</center></h3></div>");
                    }
                }
                ?>
            </div></center>
        </div>
        <br/>
        <center>
        <h2 style="color: #188420;">PHẦN QUẢN TRỊ</h2>
        <input class="btn btn-success" type="button" value="Thông Tin Cá Nhân" onClick="document.location.href = 'editUser.php?idUser=<?php echo $idUser; ?>&source=1'" />      
        <input class="btn btn-success" type="button" value="Danh sách loại thẻ" onClick="document.location.href = 'listCardType.php'" />     
        <input class="btn btn-success" type="button" value="Danh sách thẻ" onClick="document.location.href = 'listCard.php'" />     
        <input class="btn btn-success" type="<?php if ($userType == 'Admin') echo 'button'; else echo 'hidden'; ?>" value="Danh sách nhân viên" onClick="document.location.href = 'listUser.php'" />     
        <input class="btn btn-success" type="<?php if ($userType == 'Admin') echo 'button'; else echo 'hidden'; ?>" value="Thống kê báo cáo" onClick="document.location.href = 'thongke.php'" />      
        </center>
    </body>
    
     <script>
        function loadCard(cardCode) {
            $("#agree").click();
        };
        
         $(document).ready(function () {
            $("#cardCode").focus();
        });
    </script>

</html>
