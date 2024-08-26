<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Sample page</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the WORKERS table exists. */
  VerifyWorkersTable($connection, DB_DATABASE);

  /* If input fields are populated, add a row to the WORKERS table. */
  $employee_name = htmlentities($_POST['NAME']);
  $employee_age = intval($_POST['AGE']);
  $employee_company = htmlentities($_POST['COMPANY']);
  $employee_license = isset($_POST['LICENSE']) ? 1 : 0;

  if (!empty($employee_name) && $employee_age > 0 && !empty($employee_company)) {
    AddEmployee($connection, $employee_name, $employee_age, $employee_company, $employee_license);
  }
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NAME</td>
      <td>AGE</td>
      <td>COMPANY</td>
      <td>LICENSE</td>
    </tr>
    <tr>
      <td><input type="text" name="NAME" maxlength="45" size="30" /></td>
      <td><input type="number" name="AGE" maxlength="3" size="3" /></td>
      <td><input type="text" name="COMPANY" maxlength="90" size="30" /></td>
      <td><input type="checkbox" name="LICENSE" /></td>
      <td><input type="submit" value="Add Data" /></td>
    </tr>
  </table>
</form>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NAME</td>
    <td>AGE</td>
    <td>COMPANY</td>
    <td>LICENSE</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM WORKERS");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>", $query_data[0], "</td>",
       "<td>", $query_data[1], "</td>",
       "<td>", $query_data[2], "</td>",
       "<td>", $query_data[3], "</td>",
       "<td>", $query_data[4] ? 'Yes' : 'No', "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>

<?php

/* Add an employee to the table. */
function AddEmployee($connection, $name, $age, $company, $license) {
   $n = mysqli_real_escape_string($connection, $name);
   $c = mysqli_real_escape_string($connection, $company);

   $query = "INSERT INTO WORKERS (NAME, AGE, COMPANY, LICENSE) VALUES ('$n', $age, '$c', $license);";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data: " . mysqli_error($connection) . "</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyWorkersTable($connection, $dbName) {
  if(!TableExists("WORKERS", $connection, $dbName))
  {
     $query = "CREATE TABLE WORKERS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         AGE INT(3),
         COMPANY VARCHAR(90),
         LICENSE TINYINT(1)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>