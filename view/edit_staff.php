<?php
session_start();
if (!isset($_SESSION["username"])) {
   // Redirect to the login page if not logged in
   header("Location: ../user/login.php");
   exit(); // Make sure to exit after redirection
}
$error = [];
$success = '';

//1-Create Connection
require_once("../controller/connection.php");

// Function to get position table name based on position ID
function getPositionTableName($pdo, $position_id) {
   $stmt = $pdo->prepare("SELECT name FROM position WHERE id = :id");
   $stmt->bindValue(':id', $position_id);
   $stmt->execute();
   $result = $stmt->fetch(PDO::FETCH_ASSOC);
   return $result ? $result['name'] : null;
}

<?php
session_start();
if (!isset($_SESSION["username"])) {
   // Redirect to the login page if not logged in
   header("Location: ../user/login.php");
   exit(); // Make sure to exit after redirection
}
$error = [];
$success = '';

//1-Create Connection
require_once("../controller/connection.php");

// Function to get position table name based on position ID
function getPositionTableName($pdo, $position_id) {
   $stmt = $pdo->prepare("SELECT name FROM position WHERE id = :id");
   $stmt->bindValue(':id', $position_id);
   $stmt->execute();
   $result = $stmt->fetch(PDO::FETCH_ASSOC);
   return $result ? $result['name'] : null;
}

//====Update====
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
   // Get data from form
   $name = $_REQUEST["name"];
   $gender = $_REQUEST["gender"];
   $dob = $_REQUEST["dob"];
   $pob = $_REQUEST["pob"];
   $phone = $_REQUEST["phone"];
   $address = $_REQUEST["address"];
   $position = $_REQUEST["pos_id"];
   $id = $_REQUEST["id"]; // Don't forget to retrieve the ID

   //Validation
   if (!$name || !$gender || !$dob || !$phone || !$address || !$position) {
      $error[] = "All fields are required";
   }

   // Retrieve old position of the staff member
   $old_position_statement = $pdo->prepare("SELECT pos_id FROM staff WHERE id = :id");
   $old_position_statement->bindValue(':id', $id);
   $old_position_statement->execute();
   $old_position_result = $old_position_statement->fetch(PDO::FETCH_ASSOC);
   $old_position_id = $old_position_result['pos_id'];

   // Prepare and execute update query
   if (empty($error)) {
      $upSt = $pdo->prepare("UPDATE staff SET name=:name, gender=:gender, dob=:dob, pob=:pob, phone=:phone, address=:address, pos_id=:pos_id WHERE id=:id");

      $upSt->bindValue(':name', $name);
      $upSt->bindValue(':gender', $gender);
      $upSt->bindValue(':dob', $dob);
      $upSt->bindValue(':pob', $pob);
      $upSt->bindValue(':phone', $phone);
      $upSt->bindValue(':address', $address);
      $upSt->bindValue(':pos_id', $position);
      $upSt->bindValue(':id', $id);

      if ($upSt->execute()) {
         $success = "Staff details updated successfully!";

         // Get old position table name
         $old_position_table = getPositionTableName($pdo, $old_position_id);
         if ($old_position_table) {
            $remove_old_position_st = $pdo->prepare("DELETE FROM $old_position_table WHERE id = :id");
            $remove_old_position_st->bindValue(':id', $id);
            $remove_old_position_st->execute();
         }

         // Get new position table name
         $new_position_table = getPositionTableName($pdo, $position);
         if ($new_position_table) {
            $insert_new_position_st = $pdo->prepare("INSERT INTO $new_position_table (id, staff_name, gender, dob, pob, phone, address) VALUES (:id, :staff_name, :gender, :dob, :pob, :phone, :address)");
            $insert_new_position_st->bindValue(':id', $id);
            $insert_new_position_st->bindValue(':staff_name', $name);
            $insert_new_position_st->bindValue(':gender', $gender);
            $insert_new_position_st->bindValue(':dob', $dob);
            $insert_new_position_st->bindValue(':pob', $pob);
            $insert_new_position_st->bindValue(':phone', $phone);
            $insert_new_position_st->bindValue(':address', $address);
            $insert_new_position_st->execute();
         }
      } else {
         $error[] = "Error updating staff details. Please try again.";
      }
   }
}

// Retrieve staff ID
$id = $_REQUEST["id"];

// Prepare statement to fetch staff details
$statement = $pdo->prepare("SELECT staff.*, position.name as position_name FROM staff INNER JOIN position ON staff.pos_id = position.id WHERE staff.id = :id AND staff.status=1");

// Bind staff ID parameter
$statement->bindValue(':id', $id);

// Execute statement
$statement->execute();

// Fetch staff details
$pro = $statement->fetch(PDO::FETCH_ASSOC);

// Fetch positions from the database
$positionsStatement = $pdo->query("SELECT * FROM position");
$positions = $positionsStatement->fetchAll(PDO::FETCH_ASSOC);

// Fetch distinct genders from the database
$gendersStatement = $pdo->query("SELECT DISTINCT gender FROM staff");
$genders = $gendersStatement->fetchAll(PDO::FETCH_ASSOC);
?>

