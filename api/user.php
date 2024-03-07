 <?php
  include "headers.php";

  class User
  {
    function signup($json)
    {
      // {"username":"joe1","email":"joe1@gmailcom","password":"joejoejoe"}
      include "connection.php";
      $data = json_decode($json, true);
      if (recordExists($data["username"], "tbl_user", "user_username")) {
        return -1;
      }
      $sql = "INSERT INTO tbl_user(user_username, user_password, user_level) VALUES(:username, :password, 10)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":username", $data["username"]);
      $stmt->bindParam(":password", $data["password"]);
      $stmt->execute();
      return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function login($json)
    {
      // {"username":"joe","password":"joejoejoe"}
      include "connection.php";
      $data = json_decode($json, true);
      $sql = "SELECT * FROM tbl_user WHERE (user_username = :username OR user_email = :username) AND BINARY user_password = :password";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":username", $data["username"]);
      $stmt->bindParam(":password", $data["password"]);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ? json_encode($result) : 0;
    }

  } //user

  function recordExists($value, $table, $column)
  {
    include "connection.php";
    $sql = "SELECT COUNT(*) FROM $table WHERE $column = :value";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":value", $value);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count > 0;
  }

  $json = isset($_POST["json"]) ? $_POST["json"] : "0";
  $operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

  $user = new User();

  switch ($operation) {
    case "login":
      echo $user->login($json);
      break;
    case "signup":
      echo $user->signup($json);
      break;
  }
