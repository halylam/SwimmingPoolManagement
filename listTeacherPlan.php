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
        <title>Danh sách giảng dạy</title>
    </head>
    <body>
        <?php include 'header.php' ?>
        <h2 style="color: #188420;"><center>DANH SÁCH GIẢNG DẠY</center></h2>

        <div class="panel panel-default">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Tên Giáo Viên</th>
                        <th>Tên Học Viên</th>
                        <th>Ngày Kết Thúc</th>
                        <th>Học Phí</th>
                        <th>Phần Trăm Thầy</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Tên Giáo Viên</th>
                        <th>Tên Học Viên</th>
                        <th>Ngày Kết Thúc</th>
                        <th>Học Phí</th>
                        <th>Phần Trăm Thầy</th>
                        <th>Thao Tác</th>
                    </tr>
                </tfoot>
                <tbody>

                    <?php
                    $listTeacherPlan = DBUtil::getInstance()->getListTeacherPlan();

                    while ($row = mysqli_fetch_array($listTeacherPlan)) {
                        $id = $row["id"];
                        echo "<tr><td>" . htmlentities($row["name"], ENT_QUOTES, 'utf-8') . "</td>";
                        echo "<td>" . htmlentities($row["studentName"], ENT_QUOTES, 'utf-8') . "</td>";
                        echo "<td>" . date('d-m-Y H:i:s', strtotime(htmlentities($row["endDate"]))) . "</td>";
                        echo "<td>" . number_format($row['fee'], 0, '.', ',') . "</td>";
                        echo "<td>" . htmlentities($row["rate"]) . "</td>";
                        if ($userType == 'Admin') {
                            echo "<td><a href='editTeacherPlan.php?id=$id'><i class='glyphicon glyphicon-pencil'></i></a>"
                            . "<a href='deleteTeacherPlan.php?id=" . $id . "' onClick=\"javascript:return confirm('Bạn có chắc chắn lịch dạy ko?');\">"
                            . "<i style='margin-left: 15px; color: red;' class='glyphicon glyphicon-remove'></i></a></td></tr>";
                        } else {
                            echo "<td></td></tr>";
                        }
                    }
                    mysqli_free_result($listTeacherPlan);
                    ?>


                </tbody>
            </table>
        </div>
        <br/>
        <input class="btn btn-success" type="<?php if ($userType == 'Admin') echo 'button';
                    else echo 'hidden'; ?>" value="Đăng ký lịch dạy" onClick="document.location.href = 'newTeacherPlan.php'" />     

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
