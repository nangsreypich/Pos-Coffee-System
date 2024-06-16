<?php
session_start();
if (!isset($_SESSION["username"])) {
   // Redirect to the login page if not logged in
   header("Location: ../user/login.php");
   exit(); // Make sure to exit after redirection
}

$error = [];
$success = '';

// Include database connection
require_once("../controller/connection.php");

// Function to get position table name based on position ID
function getPositionTableName($pdo, $position_id) {
   $stmt = $pdo->prepare("SELECT name FROM position WHERE id = :id");
   $stmt->bindValue(':id', $position_id);
   $stmt->execute();
   $result = $stmt->fetch(PDO::FETCH_ASSOC);
   return $result ? $result['name'] : null;
}

//====Update Operation====
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
   // Get data from form
   $name = $_POST["name"];
   $gender = $_POST["gender"];
   $dob = $_POST["dob"];
   $pob = $_POST["pob"];
   $phone = $_POST["phone"];
   $address = $_POST["address"];
   $position = $_POST["pos_id"];
   $id = $_POST["id"]; // Don't forget to retrieve the ID

   // Validation
   if (!$name || !$gender || !$dob || !$phone || !$address || !$position) {
      $error[] = "All fields are required";
   }

   // Retrieve old position ID of the staff member
   $old_position_statement = $pdo->prepare("SELECT pos_id FROM staff WHERE id = :id");
   $old_position_statement->bindValue(':id', $id);
   $old_position_statement->execute();
   $old_position_result = $old_position_statement->fetch(PDO::FETCH_ASSOC);
   $old_position_id = $old_position_result['pos_id'];

   // Prepare and execute update query
   if (empty($error)) {
      $update_statement = $pdo->prepare("UPDATE staff SET name=:name, gender=:gender, dob=:dob, pob=:pob, phone=:phone, address=:address, pos_id=:pos_id WHERE id=:id");

      $update_statement->bindValue(':name', $name);
      $update_statement->bindValue(':gender', $gender);
      $update_statement->bindValue(':dob', $dob);
      $update_statement->bindValue(':pob', $pob);
      $update_statement->bindValue(':phone', $phone);
      $update_statement->bindValue(':address', $address);
      $update_statement->bindValue(':pos_id', $position);
      $update_statement->bindValue(':id', $id);

      if ($update_statement->execute()) {
         $success = "Staff details updated successfully!";

         // Remove old position entry if it exists
         $old_position_table = getPositionTableName($pdo, $old_position_id);
         if ($old_position_table) {
            $remove_old_position_statement = $pdo->prepare("DELETE FROM $old_position_table WHERE id = :id");
            $remove_old_position_statement->bindValue(':id', $id);
            $remove_old_position_statement->execute();
         }

         // Insert new position entry
         $new_position_table = getPositionTableName($pdo, $position);
         if ($new_position_table) {
            $insert_new_position_statement = $pdo->prepare("INSERT INTO $new_position_table (id, staff_name, gender, dob, pob, phone, address) VALUES (:id, :staff_name, :gender, :dob, :pob, :phone, :address)");
            $insert_new_position_statement->bindValue(':id', $id);
            $insert_new_position_statement->bindValue(':staff_name', $name);
            $insert_new_position_statement->bindValue(':gender', $gender);
            $insert_new_position_statement->bindValue(':dob', $dob);
            $insert_new_position_statement->bindValue(':pob', $pob);
            $insert_new_position_statement->bindValue(':phone', $phone);
            $insert_new_position_statement->bindValue(':address', $address);
            $insert_new_position_statement->execute();
         }
      } else {
         $error[] = "Error updating staff details. Please try again.";
      }
   }
}

// Retrieve staff ID from POST
$id = $_POST["id"];

// Fetch updated staff details for display
$statement = $pdo->prepare("SELECT staff.*, position.name as position_name FROM staff INNER JOIN position ON staff.pos_id = position.id WHERE staff.id = :id AND staff.status=1");
$statement->bindValue(':id', $id);
$statement->execute();
$pro = $statement->fetch(PDO::FETCH_ASSOC);

// Fetch positions for dropdown
$positionsStatement = $pdo->query("SELECT * FROM position");
$positions = $positionsStatement->fetchAll(PDO::FETCH_ASSOC);

// Fetch distinct genders for dropdown
$gendersStatement = $pdo->query("SELECT DISTINCT gender FROM staff");
$genders = $gendersStatement->fetchAll(PDO::FETCH_ASSOC);

// Return JSON response if AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
   if (empty($error)) {
      // Prepare data for JSON response
      $response = [
         'success' => true,
         'name' => $pro['name'],
         'gender' => $pro['gender'],
         'dob' => $pro['dob'],
         'pob' => $pro['pob'],
         'phone' => $pro['phone'],
         'email' => $pro['email'],
         'address' => $pro['address'],
         'position_name' => $pro['position_name']
      ];
      echo json_encode($response);
   } else {
      // Return error message
      $response = [
         'success' => false,
         'message' => 'Failed to update staff: ' . implode(', ', $error)
      ];
      echo json_encode($response);
   }
   exit; // Make sure to exit after handling AJAX response
}
?>
