<?php

require_once "Connection.class.php";

class User {
    private $connection;

    private $id;
    private $first_name;
    private $last_name;
    private $email;
    private $password;
    private $birthday;
    private $status;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function insertUser($first_name, $last_name, $email, $password, $birthday, $status) {
        
        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("insert into users (first_name, last_name, email, password, birthday, status)
            values (:f, :l, :e, :p, :b, :s)");
            $params = array (":f" => $first_name, ":l" => $last_name, ":e" => $email, ":p" => $password, ":b" => $birthday, ":s" => $status);
            $stmt->execute($params);
            echo "Query executed succesfully!";
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Error while executing query! ". $ex->getMessage();
        }
    }

    public function getUserInfos() {

        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("select * from users");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $value) {
                echo "Id: " .$value["id"] . ", first name: " . $value["first_name"] . ", last name: " . $value["last_name"] . ", email: " . $value["email"] . ", password: " . str_repeat("*", strlen($value["password"])) . ", birthday: " . $value["birthday"] . ", status: " . $value["status"] ."<br>";
            }
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Query error! " . $ex->getMessage();
        }
    }

    public function getFullName() {
        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("select first_name, last_name from users");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul><b>Full name:</b>";
            foreach ($results as $value) {
                echo "<li>". $value["first_name"] . " " . $value["last_name"] . "</li>";
            }
            echo "</ul>";
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Query error! " . $ex->getMessage();
        }
    }

    public function getEmail() {
        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("select email from users");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul><b>Email adresses:</b>";
            foreach ($results as $value) {
                echo "<li>" . $value["email"] . "</li>";
            }
            echo "</ul>";
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Query error! " . $ex->getMessage();
        }
    }

    public function getUserId() {
        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("select id from users");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul><b>ID:</b>";
            foreach ($results as $value) {
                echo "<li>" . $value["id"] . "</li>";
            }
            echo "</ul>";
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Query error! " . $ex->getMessage();
        }
    }

    public function getUserInfo($value) {
        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("select * from users");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul><b>" . ucfirst($value).":</b>";
            foreach ($results as $r) {
                if ($value == "password" && array_key_exists($value, $r)) {
                    echo "<li>" . str_repeat("*", strlen($r[$value])) . "</li>";
                }
                elseif (array_key_exists($value, $r)){
                    echo "<li>" . $r["{$value}"] . "</li>";
                    }
                else {
                    echo " <<< Invalid value!<br>";
                    break;
                }
            }
            echo "</ul>";
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Query error! " . $ex->getMessage();
        }
    }

    public function userAge($id) {
        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("select birthday from users where id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $result = $stmt->fetch();
            if ($result)
            {
                $date = $result["birthday"];

                if (time() < strtotime("+18 years", strtotime($date))) {
                echo "User with ID {$id} is under 18!<br>";
                }
                else {
                echo "User with ID {$id} is over 18!<br>";
                }
            }
            else
            {
                echo "No results!";
            }
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Query error! " . $ex->getMessage();
        }
    }

    public function editUser($id, $first_name = null, $last_name = null, $email = null, $password = null, $birthday = null, $status = null) {
     
        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("update users set
            first_name = ifnull(:f, first_name),
            last_name = ifnull(:l, last_name),
            email = ifnull(:e, email),
            password = ifnull(:p, password),
            birthday = ifnull(:b, birthday),
            status = ifnull(:s, status)
            where id = :id");
            $params = array (":id" => $id, ":f" => $first_name, ":l" => $last_name, ":e" => $email, ":p" => $password, ":b" => $birthday, ":s" => $status);
            $stmt->execute($params);
            echo "User updated succesfully!<br>";
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Error while executing query! ". $ex->getMessage();
        }
    }

    public function setUserStatus($id, $value) {
        try {
            $conn = $this->connection->getConnection();
            $stmt = $conn->prepare("update users set status = :status where id = :id");
            $stmt2 = $conn->prepare("select status from users where id = :id");
            $stmt2->execute(array(":id" => $id));
            $r = $stmt2->fetchColumn();
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":status", $value);
            $stmt->execute();
            $res = $stmt->rowCount();
            if ($res != 0) {
                echo "User status updated succesfully!<br>";
                $this->status = $value;
            }
            else if ($r == $value) {
                echo "User already got that status!<br>";
                $this->status = $value;
            }
            else {
                echo "Invalid ID value!<br>";
            }
            $conn = null;
        }
        catch (PDOException $ex) {
            echo "Query error! " . $ex->getMessage();
        }
    }
}
$connection = new Connection("localhost", "root", "", "assg_db");
$user = new User($connection);
//$user->insertUser("Nedim", "Garib", "nedimg@hotmail.com", "325092", "1998-08-28", 1);
//$user->insertUser("Larisa", "Burko", "larisab@gmail.com", "32894", "2003-04-01", 2);
$user->getUserInfos();
$user->getFullName();
$user->getEmail();
$user->getUserId();
$user->getUserInfo("birthday");
$user->userAge(2);
$user->editUser(1, null, null, null, "2808889", null, null);
$user->setUserStatus(2, 4);
//$user->getUserId();
//$user->insertUser("Ajdin", "Catic", "ajdin@gmail.com", "38294", "1999-01-08", 2);
$user->editUser(3, null, null, null, "434", null, 3);
$user->setUserStatus(1, 1);