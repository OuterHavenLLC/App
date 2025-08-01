<?php
 class DatabaseException extends Exception {}
 class SQL {
  private $data;
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
   } catch (PDOException $error) {
    throw new DatabaseException("<h4>Database Error</h4>\r\n<p>".$error->getMessage()."</p>");
   }
  }
  public function debugDumpParams(): string {
   if(!$this->statement) {
    throw new DatabaseException("<p>No statement prepared.</p>");
   }
   ob_start();
   $this->statement->debugDumpParams();
   return ob_get_clean();
  }
  public function countRows(): int {
   if(!$this->statement) {
    throw new DatabaseException("<p>No statement prepared.</p>");
   }
   return $this->statement->rowCount();
  }
  public function execute(): bool {
   if(!$this->statement) {
    throw new DatabaseException("<p>No statement prepared.</p>");
   } try {
    return $this->statement->execute();
   } catch(PDOException $error) {
    throw new DatabaseException("<h4>Query Execution Error</h4>\r\n<p>An error occurred while attempting to execute the query:</p>\r\n<p><strong>Message</strong>: ".$error->getMessage()."</p>");
   }
  }
  public function query(string $query, array $values): void {
   try {
    $this->statement = $this->data->prepare($query);
    foreach($values as $param => $value) {
     $type = PDO::PARAM_STR;
     if(is_int($value)) {
      $type = PDO::PARAM_INT;
     } elseif(is_bool($value)) {
      $type = PDO::PARAM_BOOL;
     } elseif(is_null($value)) {
      $type = PDO::PARAM_NULL;
     } try {
      $this->statement->bindValue($param, $value, $type);
     } catch(PDOException $error) {
      throw new DatabaseException("<h4>Bind Error</h4>\r\n<p>An error occurred while attempting to bind values to the query:</p>\r\n<p><strong>Message</strong>: ".$error->getMessage()."</p>\r\n<p><strong>Query</strong>: $query</p>\r\n<p><strong>Values</strong>: ".json_encode($values, JSON_PRETTY_PRINT)."</p>");
     }
    }
   } catch(PDOException $error) {
    throw new DatabaseException("<h4>Query Preparation Error</h4>\r\n<p>An error occurred while attempting to prepare the query:</p>\r\n<p><strong>Message</strong>: ".$error->getMessage()."</p>\r\n<p><strong>Query</strong>: $query</p>\r\n<p><strong>Values</strong>: ".json_encode($values, JSON_PRETTY_PRINT)."</p>");
   }
  }
  public function set(): array {
   $this->execute();
   return $this->statement->fetchAll(PDO::FETCH_ASSOC);
  }
  public function single(): ?array {
   $this->execute();
   $result = $this->statement->fetch(PDO::FETCH_ASSOC);
   return $result ?: null;
  }
 }
?>