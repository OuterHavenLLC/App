<?php
 class SQL {
  private $connection;
  private $error;
  private $statement;
  public function __construct(array $credentials) {
   $database = base64_decode($credentials["Database"]);
   $connection = "mysql:host=localhost;dbname=$database";
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
  public function debugDumpParams() {
   return $this->statement->debugDumpParams();
  }
  public function countRows() {
   return $this->statement->rowCount();
  }
  public function execute($queryData = NULL) {
   try {
    return $this->statement->execute();
   } catch(PDOException $error) {
    $this->error = "<h1>Query Execution Error</h1>\r\n";
    $this->error .= "<p>An error ocurred while attempting to execute the query:</p>\r\n";
    $this->error .= "<p><strong>Message</strong>: $error</p>\r\n";
    $this->error .= "<p><strong>Query Data</strong>: ".json_encode($queryData, true)."</p>\r\n";
    die($this->error);
   }
  }
  public function query(string $query, array $values) {
   try {
    $_ValueType = PDO::PARAM_STR;
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
      }
     } try {
      $this->statement->bindValue($value, $info, $_ValueType);
     } catch(PDOException $error) {
      $this->error = "<h1>Bind Error</h1>\r\n";
      $this->error .= "<p>An error ocurred while attempting to bind values to the query:</p>\r\n";
      $this->error .= "<p><strong>Message</strong>: $error</p>\r\n";
      $this->error .= "<p><strong>Query</strong>: $query</p>\r\n";
      $this->error .= "<p><strong>Values</strong>: ".json_encode($values, true)."</p>\r\n";
      die($this->error);
     }
    }
   } catch(PDOException $error) {
    $this->error = "<h1>Query Preparation Error</h1>\r\n";
    $this->error .= "<p>An error ocurred while attempting to prepare the query:</p>\r\n";
    $this->error .= "<p><strong>Message</strong>: $error</p>\r\n";
    $this->error .= "<p><strong>Query</strong>: $query</p>\r\n";
    $this->error .= "<p><strong>Values</strong>: ".json_encode($values, true)."</p>\r\n";
    die($this->error);
   }
  }
  public function set() {
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
?>