<?php
 class SQL {
  private $connection;
  private $error;
  private $statement;
  public function __construct(array $credentials) {
   $connection = "mysql:host=localhost;dbname=ReSearch";
   $options = [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
   ];
   $password = base64_decode($credentials["Password"]);
   $username = base64_decode($credentials["Username"]);
   try {
    $this->data = new PDO($connection, $username, $password, $options);
   } catch(PDOException $error) {
    $this->error = "<h1>Database Error</h1>\r\n<p>".$error->getMessage()."</p>\r\n";
   }
  }
  public function beginTransaction() {
   return $this->data->beginTransaction();
  }
  public function cancelTransaction() {
   return $this->data->rollBack();
  }
  public function debugDumpParams() {
   return $this->statement->debugDumpParams();
  }
  public function countRows() {
   return $this->statement->rowCount();
  }
  public function endTransaction() {
   return $this->data->commit();
  }
  public function execute() {
   return $this->statement->execute();
  }
  public function query(string $query, array $values) {
   $_ValueType = "";
   $this->statement = $this->data->prepare($query);
   foreach($values as $value => $info) {
    if(is_null($_ValueType)) {
     switch(true) {
      case is_int($info):
       $_ValueType = PDO::PARAM_INT;
       break;
      case is_bool($info):
       $_ValueType = PDO::PARAM_BOOL;
       break;
      case is_null($info):
       $_ValueType = PDO::PARAM_NULL;
       break;
      default:
       $_ValueType = PDO::PARAM_STR;
     }
    }
    $this->statement->bindValue($value, $info, $_ValueType);
   }
  }
  public function lastInsertId() {
   return $this->data->lastInsertId();
  }
  public function resultset() {
   $this->execute();
   return $this->statement->fetchAll(PDO::FETCH_ASSOC);
  }
  public function single() {
   $this->execute();
   return $this->statement->fetch(PDO::FETCH_ASSOC);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
/*--
<?php //tutorial.php

include 'config.php';
include 'database.class.php';

$db = new Database();

* Insert a new record
$db->query('INSERT INTO mytable (placeholder, placeholder, placeholder, placeholder) VALUES (:fname, :lname, :age, :gender)');

$db->bind(':fname', 'John');
$db->bind(':lname', 'Smith');
$db->bind(':age', '24');
$db->bind(':gender', 'male');

$db->execute();

echo $db->lastInsertId();

* Insert multiple records using a Transaction
$db->beginTransaction();

$db->query('INSERT INTO mytable (placeholder, placeholder, placeholder, placeholder) VALUES (:fname, :lname, :age, :gender)');

$db->bind(':fname', 'Jenny');
$db->bind(':lname', 'Smith');
$db->bind(':age', '23');
$db->bind(':gender', 'female');

$db->execute();

$db->bind(':fname', 'Jilly');
$db->bind(':lname', 'Smith');
$db->bind(':age', '25');
$db->bind(':gender', 'female');

$db->execute();

echo $db->lastInsertId();

$db->endTransaction();

* Select a single row
$db->query('SELECT FName, LName, Age, Gender FROM mytable WHERE FName = :fname');

$db->bind(':fname', 'Jenny');

$row = $db->single();

echo "<pre>";
print_r($row);
echo "</pre>";

* Select multiple rows
$db->query('SELECT FName, LName, Age, Gender FROM mytable WHERE LName = :lname');

$db->bind(':lname', 'Smith');

$rows = $db->resultset();

echo "<pre>";
print_r($rows);
echo "</pre>";

echo $db->rowCount();
--*/
?>