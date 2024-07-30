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
  public function debugDumpParams() {
   return $this->statement->debugDumpParams();
  }
  public function countRows() {
   return $this->statement->rowCount();
  }
  public function execute() {
   return $this->statement->execute();
  }
  public function query(string $query, array $values) {
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
    }
    $this->statement->bindValue($value, $info, $_ValueType);
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