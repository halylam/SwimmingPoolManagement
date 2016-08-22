<?php
date_default_timezone_set("Asia/Bangkok");
class DBUtil extends mysqli {

    // single instance of self shared among all instances
    private static $instance = null;
    // db connection config vars
    private $user = "root";
    private $pass = "root1";
    private $dbName = "swim_pool_manage";
    private $dbHost = "localhost";
    
//    private $user = 'u933279007_root1';
//    private $pass = '123456';
//    private $dbName = 'u933279007_swim';
//    private $dbHost = 'mysql.hostinger.vn';
//    
//    private $user = 'd1789p04_uqlhb';
//    private $pass = 'Q@123Hb16';
//    private $dbName = 'd1789p04_qlhb';
//    private $dbHost = '127.0.0.1';

    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    // private constructor
    private function __construct() {
        parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
        if (mysqli_connect_error()) {
            exit('Connect Error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }
        $this->set_charset("utf8");
    }

    public function checkLogin($login, $pass) {
        $login = $this->real_escape_string($login);
        $pass = $this->real_escape_string($pass);
        $user = $this->query("SELECT * FROM user WHERE inactive = 0 and login='" . $login . "'" . " and pass='" . $pass . "'");
        return $user->fetch_assoc();
    }

    public function getListUser() {
        return $this->query("SELECT * FROM user WHERE inactive=0");
    }

    public function getUserDetail($idUser) {
        $result = $this->query("SELECT * FROM user WHERE idUser=" . $idUser);
        return $result->fetch_assoc();
    }

    public function checkUserExisted($login) {
        $result = $this->query("SELECT * FROM user WHERE login='" . $login . "'");
        if (!$result) {
            throw new Exception("Database Error [{$this->errno}] {$this->error}");
        }
        return $result->fetch_assoc();
    }

    public function insertUser($login, $pass, $fullname, $phone, $address, $birthday, $email, $avatar) {
        $login = $this->real_escape_string($login);
        $pass = $this->real_escape_string($pass);
        $fullname = $this->real_escape_string($fullname);
        $phone = $this->real_escape_string($phone);
        $address = $this->real_escape_string($address);
        $birthday = $this->real_escape_string($birthday);
        $email = $this->real_escape_string($email);
        $avatar = $this->real_escape_string($avatar);

        $this->query("INSERT user (login, pass, fullname, phone, address, birthday, email, avatar) VALUES "
                . " ('" . $login . "', '" . $pass . "', '" . $fullname . "', '" . $phone . "', '" . $address . "', '" . $birthday . "', '" . $email . "', '" . $avatar . "')");
    }

    public function updateUser($idUser, $login, $pass, $fullname, $phone, $address, $birthday, $email, $avatar) {
        $login = $this->real_escape_string($login);
        $pass = $this->real_escape_string($pass);
        $fullname = $this->real_escape_string($fullname);
        $phone = $this->real_escape_string($phone);
        $address = $this->real_escape_string($address);
        $birthday = $this->real_escape_string($birthday);
        $email = $this->real_escape_string($email);
        $avatar = $this->real_escape_string($avatar);

        $this->query("UPDATE user SET login = '" . $login . "', pass='" . $pass . "', fullname='" . $fullname .
                "', phone='" . $phone . "', address='" . $address . "', birthday='" . $birthday . "', email='" . $email . "', avatar='" . $avatar . "' WHERE idUser=" . $idUser);
    }

    public function deleteUser($idUser) {
        $this->query("UPDATE user SET inactive=1 WHERE idUser=" . $idUser);
    }

    //-------------------------END USER-----------------------------//

    public function insertTransaction($cardCode, $cardType, $price, $date, $login) {
        $cardCode = $this->real_escape_string($cardCode);
        if (!empty($price)) {
            $price = $this->real_escape_string($price);
        } else {
            $price = 0;
        }
        $result = $this->query("INSERT transaction (tranDate, price, typeName, login, cardCode) VALUES "
                . " ('" . $date . "', '" . $price . "', '" . $cardType . "', '" . $login . "', '" . $cardCode . "')");
        if (!$result) {
            throw new Exception("Database Error [{$this->errno}] {$this->error}");
        }
    }
    
    
    public function getListTransaction($login, $typeName, $from, $to) {
        $query = "SELECT * FROM transaction WHERE 1=1";
        if ($login != null && $login != '') {
            $query .= " and login='" . $login . "'";
        }
        if ($typeName != null && $typeName != '') {
            $query .= " and typeName='" . $typeName . "'";
        }
        if ($from != null && $from != '') {
            $query .= " and tranDate >= '" . $from . "'";
        }
        if ($to != null && $to != '') {
            $query .= " and tranDate <= '" . $to . "'";
        }
        $query .= " ORDER BY tranDate DESC LIMIT 1000 ";
        return $this->query($query);
    }
    
    public function getListTransactionInfo($login, $typeName, $from, $to) {
        $query = "SELECT count(*) as totalCount, sum(price) as totalPrice, typeName FROM transaction WHERE 1=1";
        if ($login != null && $login != '') {
            $query .= " and login='" . $login . "'";
        }
        if ($typeName != null && $typeName != '') {
            $query .= " and typeName='" . $typeName . "'";
        }
        if ($from != null && $from != '') {
            $query .= " and tranDate >= '" . $from . "'";
        }
        if ($to != null && $to != '') {
            $query .= " and tranDate <= '" . $to . "'";
        }        
        $query .= " group by typeName ";
        return $this->query($query);
    }

    //-------------------------END TRANSACTION-----------------------------//

    public function insertCardType($typeName, $price) {
        $typeName = $this->real_escape_string($typeName);
        $price = $this->real_escape_string($price);
        $this->query("INSERT cardtype (typeName, price) VALUES ('" . $typeName . "', '" . $price . "')");
    }

    public function updateCardType($idCardType, $typeName, $price, $typeNameOld, $priceOld) {
        $typeName = $this->real_escape_string($typeName);
        $typeNameOld = $this->real_escape_string($typeNameOld);
        $price = $this->real_escape_string($price);
        $priceOld = $this->real_escape_string($priceOld);
        $this->query("UPDATE cardtype SET typeName = '" . $typeName . "', price='" . $price . "' WHERE idCardType=" . $idCardType);
        if (($price != $priceOld) || ($typeName != $typeNameOld)) {
            $this->query("UPDATE card SET typeName = '" . $typeName . "', price='" . $price . "' WHERE idCardType=" . $idCardType);
        }
    }

    public function deleteCardType($idCardType) {
        $this->query("DELETE FROM cardtype WHERE idCardType = " . $idCardType);
    }

    public function getListCardType() {
        return $this->query("SELECT * FROM cardtype");
    }

    public function getCardTypeDetail($idCardType) {
        $result = $this->query("SELECT * FROM cardtype WHERE idCardType=" . $idCardType);
        return $result->fetch_assoc();
    }
    
    public function checkCardTypeExisted($typeName) {
        $result = $this->query("SELECT * FROM cardtype WHERE typeName='" . $typeName . "'");
        if (!$result) {
            throw new Exception("Database Error [{$this->errno}] {$this->error}");
        }
        return $result->fetch_assoc();
    }

    //-------------------------END CARDTYPE-----------------------------//
    
     public function insertCard($cardCode, $idCardType, $longTerm, $remainTimes) {
        $cardType = $this->getCardTypeDetail($idCardType);
        if ($remainTimes == ''){
            $remainTimes = 0;
        }
        $result = $this->query("INSERT card (idCardType, cardCode, typeName, price, longTerm, remainTimes) VALUES ('" . $idCardType . "', '" . $cardCode . "', '" . $cardType["typeName"] . "', '" . $cardType["price"] . "', '" . $longTerm . "', '" . $remainTimes . "')");
        if (!$result) {
            throw new Exception("Database Error [{$this->errno}] {$this->error}");
        }
    }
    
     public function updateCard($idCard, $idCardType, $longTerm, $remainTimes) {
        $cardType = $this->getCardTypeDetail($idCardType);
        $result = $this->query("UPDATE card SET idCardType = '" . $idCardType . "', typeName='" . $cardType["typeName"] . "', price='" . $cardType["price"] . "', longTerm='" . $longTerm . "', remainTimes='" . $remainTimes . "' WHERE idCard=" . $idCard);
        if (!$result) {
            throw new Exception("Database Error [{$this->errno}] {$this->error}");
        }
    }

    public function deleteCard($idCard) {
        $this->query("DELETE FROM card WHERE idCard = " . $idCard);
    }

    public function getListCard() {
        return $this->query("SELECT * FROM card LIMIT 1000");
    }

    public function getCardDetail($idCard) {
        $result = $this->query("SELECT * FROM card WHERE idCard=" . $idCard);
        return $result->fetch_assoc();
    }
    
    public function getCardDetailByCode($cardCode) {
        $result = $this->query("SELECT * FROM card WHERE cardCode='" . $cardCode . "'");
        return $result->fetch_assoc();
    }
    
    public function minusRemainTimes($cardCode) {
        return $this->query("UPDATE card SET remainTimes = (remainTimes-1) WHERE cardCode='" . $cardCode . "'");
    }

    //-------------------------END CARD-----------------------------//
    
    public function insertTeacher($name) {
        $name = $this->real_escape_string($name);
        $this->query("INSERT teacher (name) VALUES ('" . $name . "')");
    }

    public function updateTeacher($idTeacher, $name) {
        $name = $this->real_escape_string($name);
        $this->query("UPDATE teacher SET name = '" . $name . "' WHERE idTeacher=" . $idTeacher);
    }

    public function deleteTeacher($idTeacher) {
        $this->query("DELETE FROM teacher WHERE idTeacher = " . $idTeacher);
    }
    
    public function getListTeacher() {
        return $this->query("SELECT * FROM teacher");
    }

    public function getTeacherDetail($idTeacher) {
        $result = $this->query("SELECT * FROM teacher WHERE idTeacher=" . $idTeacher);
        return $result->fetch_assoc();
    }
    
    //-------------------------END TEACHER-----------------------------//
    
    public function insertTeacherPlan($idTeacher, $studentName, $endDate, $fee, $rate) {
        $this->query("INSERT teacherplan (idTeacher, studentName, endDate, fee, rate) VALUES ('" . $idTeacher . "', '".$studentName."', '".$endDate."', '".$fee."', '".$rate."')");
    }

    public function updateTeacherPlan($id, $idTeacher, $studentName, $endDate, $fee, $rate) {
        $this->query("UPDATE teacherplan SET idTeacher=".$idTeacher.", studentName = '" . $studentName . "', endDate = '" . $endDate . "', fee = '" . $fee . "'"
                . ", rate = '" . $rate . "' WHERE id=" . $id);
    }

    public function deleteTeacherPlan($id) {
        $this->query("DELETE FROM teacherplan WHERE id = " . $id);
    }

    public function getListTeacherPlan() {
        return $this->query("SELECT tp.*, t.name FROM teacherplan tp LEFT JOIN teacher t on tp.idTeacher = t.idTeacher");
    }

    public function getTeacherPlanDetail($id) {
        $result = $this->query("SELECT * FROM teacherplan WHERE id=" . $id);
        return $result->fetch_assoc();
    }
    
    public function getTeacherPlanInfo($month, $year) {
		return $this->query("select sum((tp.fee*(100-tp.rate))/100) as totalIncome, sum(tp.fee*tp.rate/100) as total, t.name as name, count(*) as amount  FROM teacherplan tp"
                . " LEFT JOIN teacher t ON tp.idTeacher = t.idTeacher where MONTH(tp.endDate) = ".$month." and YEAR(tp.endDate) = ".$year." group by tp.idTeacher");
    }
    
    //-------------------------END TEACHER PLAN-----------------------------//
}
