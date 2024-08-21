<?php
 require_once("/var/www/html/base/Bootloader.php");
 $_AggregateFunction = "";
 $_CreateTables = "";
 $_Insert = "";
 $_Join = "";
 $_Retrieve = "";
 $oh = New OH;
 $payload = json_decode(file_get_contents("./payload.json"), true);
 $data = $payload["Data"] ?? [];
 $queries = $payload["Queries"] ?? [];
 $sql = $oh->core->cypher->SQLCredentials();
 $sql = New SQL([
  "Database" => base64_encode("INFO640_Company"),
  "Password" => $sql["Password"],
  "Username" => $sql["Username"]
 ]);
 $title = "Relational Databases";
 foreach($queries as $entity => $query) {
  $_CreateTable = $query["CreateTable"];
  $_InsertData = $queries[$entity]["InsertData"];
  $_RetrieveAllData = $queries[$entity]["RetrieveData"];
  $_Tuples = $data[$entity] ?? [];
  $sql->query($_CreateTable, []);
  $sql->execute();
  $_CreateTables .= $oh->core->Element([
   "h4", "Created the <em>$entity</em> entity using the following query:"
  ]).$oh->core->Element([
   "p", $_CreateTable
  ]);
  foreach($_Tuples as $tupleData) {
   $sql->query($_InsertData, $tupleData);
   $sql->execute([$_InsertData, $tupleData]);
   $_Insert .= $oh->core->Element([
    "h4", "Inserted a tuple into the <em>$entity</em> entity (if it is absent or outdated) using the following:"
   ]).$oh->core->Element([
    "p", "<strong>Query</strong>: $_InsertData."
   ]).$oh->core->Element([
    "p", "<strong>Prepared Values</strong>: ".json_encode($tupleData, true)
   ]);
  }
  $_Insert .= $oh->core->Element([
   "div", NULL, ["class" => "NONAME", "style" => "height:1em"]
  ]);
  $sql->query($_RetrieveAllData, []);
  $retrievedData = $sql->set();
  $_Retrieve .= $oh->core->Element([
   "h4", "Retrieved all data from the <em>$entity</em> entity using the following query:"
  ]).$oh->core->Element([
   "p", $_RetrieveAllData
  ]);
  $rowCount = 1;
  foreach($retrievedData as $row => $info) {
   $_Retrieve .= $oh->core->Element([
    "p", "<strong>Row #$rowCount</strong>: ".json_encode($info, true)
   ]);
   $rowCount++;
  }
  $_Retrieve .= $oh->core->Element([
   "div", NULL, ["class" => "NONAME", "style" => "height:1em"]
  ]);
 }
 $join = "Select * from DepartmentLocations
               join Department
               on Department.DeptNumber=DepartmentLocations.DeptNumber
               join Project
               on Project.ProjectLocation=DepartmentLocations.DeptLocation
               order by rand() desc
               limit 5
 ";
 $join2 = "Select * from WorksOn
               join Employee
               on Employee.SSN=WorksOn.EmployeeSSN
               join Project
               on Project.ProjectNumber=WorksOn.ProjectNumber
               order by rand() desc
               limit 5
 ";
 $join3 = "Select * from Employee
               left join Dependent on Dependent.EmployeeSSN=Employee.SSN
               union all
               Select * from Employee
               right join Dependent on Dependent.EmployeeSSN=Employee.SSN
               order by rand() desc
               limit 5
 ";
 $join4aggregate = "Select avg(Hours) from WorksOn
               join Employee
               on Employee.SSN=WorksOn.EmployeeSSN
               join Project
               on Project.ProjectNumber=WorksOn.ProjectNumber
 ";
 $join5aggregate = "Select json_arrayagg(DeptNumber) from Employee
               left join Dependent on Dependent.EmployeeSSN=Employee.SSN
               union all
               Select json_arrayagg(DeptNumber) from Employee
               right join Dependent on Dependent.EmployeeSSN=Employee.SSN
 ";
 $sql->query($join, []);
 $retrievedData = $sql->set();
 $_Join .= $oh->core->Element([
  "h4", "Retrieved all data from the <em>DepartmentLocations</em> entity with the following query:"
 ]).$oh->core->Element([
  "p", $join
 ]);
 $rowCount = 1;
 foreach($retrievedData as $row => $info) {
  $_Join .= $oh->core->Element([
   "p", "<strong>Row #$rowCount</strong>: ".json_encode($info, true)
  ]);
  $rowCount++;
 }
 $_Join .= $oh->core->Element([
  "div", NULL, ["class" => "NONAME", "style" => "height:1em"]
 ]);
 $sql->query($join2, []);
 $retrievedData = $sql->set();
 $_Join .= $oh->core->Element([
  "h4", "Retrieved all data from the <em>WorksOn</em> entity with the following query:"
 ]).$oh->core->Element([
  "p", $join2
 ]);
 $rowCount = 1;
 foreach($retrievedData as $row => $info) {
  $_Join .= $oh->core->Element([
   "p", "<strong>Row #$rowCount</strong>: ".json_encode($info, true)
  ]);
  $rowCount++;
 }
 $_Join .= $oh->core->Element([
  "div", NULL, ["class" => "NONAME", "style" => "height:1em"]
 ]);
 $sql->query($join3, []);
 $retrievedData = $sql->set();
 $_Join .= $oh->core->Element([
  "h4", "Retrieved all data from the <em>Employee</em> entity  using a full outer join with the following query:"
 ]).$oh->core->Element([
  "p", $join3
 ]);
 $rowCount = 1;
 foreach($retrievedData as $row => $info) {
  $_Join .= $oh->core->Element([
   "p", "<strong>Row #$rowCount</strong>: ".json_encode($info, true)
  ]);
  $rowCount++;
 }
 $_Join .= $oh->core->Element([
  "div", NULL, ["class" => "NONAME", "style" => "height:1em"]
 ]);
 $sql->query($join4aggregate, []);
 $retrievedData = $sql->set();
 $_AggregateFunction .= $oh->core->Element([
  "h4", "Retrieved all data from the <em>WorksOn</em> entity using aggregate functionality to determine the average amount of hours worked with the following query:"
 ]).$oh->core->Element([
  "p", $join4aggregate
 ]);
 $rowCount = 1;
 foreach($retrievedData as $row => $info) {
  $_AggregateFunction .= $oh->core->Element([
   "p", "<strong>Row #$rowCount</strong>: ".json_encode($info, true)
  ]);
  $rowCount++;
 }
 $_Join .= $oh->core->Element([
  "div", NULL, ["class" => "NONAME", "style" => "height:1em"]
 ]);
 $sql->query($join5aggregate, []);
 $retrievedData = $sql->set();
 $_AggregateFunction .= $oh->core->Element([
  "h4", "Retrieved all data from the <em>Employee</em> entity using aggregate functionality to aggregate employees by department number with the following query:"
 ]).$oh->core->Element([
  "p", $join5aggregate
 ]);
 $rowCount = 1;
 foreach($retrievedData as $row => $info) {
  $_AggregateFunction .= $oh->core->Element([
   "p", "<strong>Row #$rowCount</strong>: ".json_encode($info, true)
  ]);
  $rowCount++;
 }
 echo $oh->core->Change([[
  "[App.Description]" => $oh->core->config["App"]["Description"],
  "[App.Keywords]" => $oh->core->config["App"]["Keywords"],
  "[App.Owner]" => $oh->core->ShopID,
  "[App.Title]" => $title,
  "[Lab.AggregateFunction]" => $_AggregateFunction,
  "[Lab.CreateTables]" => $_CreateTables,
  "[Lab.Insert]" => $_Insert,
  "[Lab.Join]" => $_Join,
  "[Lab.Retrieve]" => $_Retrieve
 ], $oh->core->PlainText([
  "BBCodes" => 1,
  "Data" => file_get_contents("./index.txt"),
  "Display" => 1
 ])]);
?>