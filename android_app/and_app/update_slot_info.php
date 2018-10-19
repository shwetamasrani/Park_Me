<?php

class update_slot_info {

    private $conn;

    // constructor
    function __construct() {
        require_once 'android_login_connect.php';
        // connecting to database
        $db = new android_login_connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {

    }

    /**
     * Storing new user
     * returns user details
     */
     public function StoreUserInfo($email,$startT,$endT,$build) {
       
        $t='0';
        //$q=CURRENT_DATE();
        $est=$startT+$endT;             //DEFINED THE ESTIMATED TIME VARIABLE
        $est_wait=$est-time();          //DEFINED THE ESTIMATED WAITING TIME
        $stmt = $this->conn->prepare("INSERT INTO slot_tbl(email, startT, endT, build,est_exit) VALUES(?, ?, ?, ?, ?)");
        //$stmt->bind_param("ssss",$email,$startT,$endT,$build);
        $stmt->bind_param("ssss",$email,$startT,$endT,$build,$est);
        $result = $stmt->execute();
        $stmt->close();
        
       /* $mysqli_query($conn,"INSERT INTO slot_tbl(email,startT,endT,build,date) VALUES('$email','$startT','$endT','$build'");*/

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT startT, endT, build FROM slot_tbl WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt-> bind_result($token2,$token3,$token4);
            while ( $stmt-> fetch() ) {
               $user["startT"] = $token2;
               $user["endT"] = $token3;
               $user["build"] = $token4;
               
            }
            $stmt->close();
            return $user.$est_wait;//check this
        } else {
          return false;
        }
    }
    
     /**
     * Check user is existed or not
     */
    public function CheckExistingUser($email) {
        $stmt = $this->conn->prepare("SELECT email from slot_tbl WHERE email = ? and (c='1' or c='0')");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed 
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }
}

?>