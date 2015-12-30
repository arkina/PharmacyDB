<?php
/**
 * Created by PhpStorm.
 * User: Neo_
 * Date: 12/27/15
 * Time: 3:47 PM
 */


if(file_exists('../mysql_connector.php')){
   include '../mysql_connector.php';
}

if (isset($_POST["addUser"])) {
    addUser();
} elseif (isset($_POST["login"])) {
    logIn();
}

if (isset($_POST['remove'])) {
    removeUser($_POST['userId']);

    header('Location: http://localhost/PharmacyDB/adminpanel.php?option=1');
}

function removeUser($userId)
{

    $link = getConnection();
    $sql = "DELETE FROM fcustomer WHERE CustomerId='" . $userId . "'";

    $resultset = mysqli_query($link, $sql);
    $link->close();

    return $resultset;
}


function addUser()
{
    $userName = preg_replace('#[^A-Za-z0-9]#', '', $_POST["username"]);
    $fullName = preg_replace('#[^A-Za-z0-9]#', '', $_POST["fullname"]);
    $address = preg_replace('#[^A-Za-z0-9]#', '', $_POST["address"]);
    $telephone = preg_replace('#[^A-Za-z0-9]#', '', $_POST["telephone"]);
    $nic = preg_replace('#[^A-Za-z0-9]#', '', $_POST["nic"]);
    $password = preg_replace('#[^A-Za-z0-9]#', '', $_POST["password"]);


    $connection = getConnection();
    $sql = "INSERT INTO fcustomer (UserName,FullName,Address,Telephone,NIC,Password) VALUES (?,?,?,?,?,?)";
    $statement = $connection->prepare($sql);
    $statement->bind_param("ssssss", $userName, $fullName, $address, $telephone, $nic, $password);
    $statement->execute();

    $last_id = $connection->insert_id;
    echo "New record created successfully. Last inserted ID is: " . $last_id;


    $statement->close();
    $connection->close();

    header('Location: http://localhost/PharmacyDB/login.php');
}

function logIn()
{
    session_start();
    $userName = preg_replace('#[^A-Za-z0-9]#', '', $_POST["username"]);
    $password = preg_replace('#[^A-Za-z0-9]#', '', $_POST["password"]);

    $connection = getConnection();

    if ($userName != 'administrator') {
        $sql = mysqli_query($connection, "SELECT CustomerId FROM fcustomer WHERE username='$userName' AND password='$password' LIMIT 1");

        $existCount = mysqli_num_rows($sql);
        if ($existCount == 1) {
            while ($row = mysqli_fetch_array($sql)) {
                $id = $row["CustomerId"];
            }
            $_SESSION["customerId"] = $id;
            $_SESSION["custName"] = $userName;
            $_SESSION["custPassword"] = $password;
            header('Location: http://localhost/PharmacyDB/index.php');
        } else {
            header('Location: http://localhost/PharmacyDB/login.php?attempt=1');
        }
    } else {
        $sql = 'SELECT * FROM fadmin';

        $resultset = mysqli_query($connection, $sql);
        $row = mysqli_fetch_assoc($resultset);


        if ($row['PassKey'] == $password) {
            $_SESSION["admin"] = "admin";
            header('Location: http://localhost/PharmacyDB/adminpanel.php');
        } else {
            header('Location: http://localhost/PharmacyDB/login.php?attempt=1');
        }
    }

    $connection->close();

}

function getAllUserDetails()
{
    $connection = getConnection();
    $sql = "SELECT * FROM fcustomer";

    $resultset = mysqli_query($link, $sql);
    mysqli_close($link);
    $link->close();
    $resultset = mysqli_query($connection, $sql);
    $connection->close();

    return $resultset;
}

?>