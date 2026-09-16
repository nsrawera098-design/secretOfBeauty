<?php
class User{
    
    public $name;
    public $password;
    public $id;
    public $email;

    
   public function __construct($id, $name, $email, $password) {
    $this->id = $id;
    $this->name = $name;
    $this->email = $email;
    $this->password = $password;
}



    public function addUser($dbConn) {
        $sql = "INSERT INTO user (name, password, id, email) 
                VALUES ('$this->name', '$this->password', '$this->id', '$this->email')";
        if (mysqli_query($dbConn, $sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function isUserExist($conn) {
        $sql = "SELECT id FROM user WHERE id = '$this->id'";
        $result = $conn->query($sql);
    
        // אם השאילתא נכשלת, הצג את השגיאה
        if (!$result) {
            echo "<script>alert('❌ SQL Error: " . $conn->error . "');</script>";
            return false;
        }
    
        return mysqli_num_rows($result) > 0;
    }
    
}    
?>