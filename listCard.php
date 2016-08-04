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
        <title>Danh sách thẻ</title>
    </head>
    <body>
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>DANH SÁCH THẺ</center></h2>

        <div class="panel panel-default">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Mã Loại Thẻ</th>
                        <th>Loại Thẻ</th>
                        <th>Giá</th>
                        <th>Thẻ dài hạn</th>
                        <th>Số lần quẹt còn lại</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Mã Loại Thẻ</th>
                        <th>Loại Thẻ</th>
                        <th>Giá</th>
                        <th>Thẻ dài hạn</th>
                        <th>Số lần quẹt còn lại</th>
                        <th>Thao tác</th>
                    </tr>
                </tfoot>
                <tbody>

                    <?php
                    $listCard = DBUtil::getInstance()->getListCard();

                    while ($row = mysqli_fetch_array($listCard)) {
                        $idCard = $row["idCard"];
                        echo "<tr><td>" . htmlentities($row["cardCode"]) . "</td>";
                        echo "<td>" . htmlentities($row["typeName"], ENT_QUOTES, 'utf-8') . "</td>";
                        echo "<td>" . htmlentities($row["price"]) . "</td>";
                        if ($row["longTerm"] == 0) {
                            echo "<td>Không</td>";
                        } else {
                            echo "<td>Có</td>";
                        }
                        echo "<td>" . htmlentities($row["remainTimes"]) . "</td>";
                        echo "<td><a href='editCard.php?idCard=$idCard'><i class='glyphicon glyphicon-pencil'></i></a>"
                        . "<a href='deleteCard.php?idCard=" . $idCard . "' onClick=\"javascript:return confirm('Bạn có chắc chắn xóa thẻ ko?');\">"
                        . "<i style='margin-left: 15px; color: red;' class='glyphicon glyphicon-remove'></i></a></td></tr>";
                    }
                    mysqli_free_result($listCard);
                    ?>


                </tbody>
            </table>
        </div>
        <br/>
        <input class="btn btn-success" type="button" value="Đăng ký mới thẻ" onClick="document.location.href = 'newCard.php'" />     

        <input class="btn btn-success" type="button" value="Trang chủ" onClick="document.location.href = 'mainPage.php'" />

    </body>
    <script>
        $(document).ready(function () {
            $('#example tfoot th').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Tìm Kiếm ' + title + '" />');
            });
            // DataTable
            var table = $('#example').DataTable();
            // Apply the search
            table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that
                                .search(this.value)
                                .draw();
                    }
                });
            });
        });
    </script>
</html>
