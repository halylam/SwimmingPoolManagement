<?php

require_once("Includes/db.php");

if (isset($_GET['cardCode'])) {
    $cardCode = $_GET['cardCode'];
    $cardItem = DBUtil::getInstance()->getCardDetailByCode($cardCode);
    if ($cardItem != null) {
        echo json_encode($cardItem);
    } else {
        echo json_encode('');
    }
}
        
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

