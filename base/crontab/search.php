<?php
 # Re:Search Index Cron Job
 require_once("Bootloader.php");
 $gw = New GW();
 $key = $gw->system->core["SQL"]["Key"];
 echo "Initializing Outer Haven Re:Search Index...\r\n";
 sleep(3);
 echo "Restoring Re:Search Database if down...";
 $gw->system->SQL("CREATE DATABASE IF NOT EXISTS ReSearch ENCRYPTION='y'", [], "Create");
 echo "OK!\r\n";
 echo "Dropping Members Index for re-indexing...";
 $gw->system->SQL("DROP TABLE Members", [], "Drop");
 echo "OK!\r\n";
 echo "Restoring Re:Search Members Index...\r\n";
 print_r($gw->system->SQL("CREATE TABLE Members(
  Age VARBINARY(8000),
  DisplayName VARBINARY(8000),
  Email VARBINARY(8000),
  Gender VARBINARY(8000),
  Username VARBINARY(8000),
  INDEX(Username(1000))
 ) ENCRYPTION='y'", []));
 echo "OK!\r\n";
 echo "Populating Members Index...\r\n";
 $files = $gw->system->DatabaseSet("MBR") ?? [];
 foreach($files as $key => $value) {
  $value = str_replace("c.oh.mbr.", "", $value);
  $member = $gw->system->Data("Get", ["mbr", $value]) ?? [];
  if(!empty($member["Login"]["Username"])) {
   echo "Populating data for ".$member["Login"]["Username"]."...";
   $gw->system->SQL("INSERT INTO Members(Age, DisplayName, Email, Gender, Username)
        VALUES(AES_ENCRYPT(:Age, :key),
               AES_ENCRYPT(:DisplayName, :key),
               AES_ENCRYPT(:Email, :key),
               AES_ENCRYPT(:Gender, :key),
               AES_ENCRYPT(:Username, :key))", [
    ":Age" => $member["Personal"]["Age"],
    ":DisplayName" => $member["Personal"]["DisplayName"],
    ":Email" => $member["Personal"]["Email"],
    ":Gender" => $member["Personal"]["Gender"],
    ":Username" => $member["Login"]["Username"],
    ":key" => base64_decode($key)
   ]);
   echo "OK!\r\n";
   $gw->system->SQL("SELECT *, AES_DECRYPT(Username, :key) AS Username
   FROM Members
   WHERE(CONVERT(AES_DECRYPT(Username, :key) USING utf8mb4)=:Username)", [
    ":Username" => $member["Login"]["Username"],
    ":key" => base64_decode($key)
   ]);
  }
 }
 echo "Done re-populating Members Index...\r\n";
 echo "Sampling the new Members data set, searching for \"Mike\"...\r\n";
 $members = $gw->system->SQL("SELECT *, AES_DECRYPT(Age, :key) AS Age,
                        AES_DECRYPT(DisplayName, :key) AS DisplayName,
                        AES_DECRYPT(Email, :key) AS Email,
                        AES_DECRYPT(Gender, :key) AS Gender,
                        AES_DECRYPT(Username, :key) AS Username
 FROM Members
 WHERE CONVERT(AES_DECRYPT(Age, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(DisplayName, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(Email, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(Gender, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(Username, :key) USING utf8mb4) LIKE :search", [
  ":key" => base64_decode($key),
  ":search" => "%Mike%"
 ]);
 print_r($members->fetchAll(PDO::FETCH_ASSOC));
 echo "OK\r\n";
 echo "Dropping Pages
  Index for re-indexing...";
 $gw->system->SQL("DROP TABLE Pages", [], "Drop");
 echo "OK!\r\n";
 echo "Restoring Re:Search Pages Index...\r\n";
 print_r($gw->system->SQL("CREATE TABLE Pages(
  Body VARBINARY(8000),
  Description VARBINARY(8000),
  ID VARBINARY(8000),
  Title VARBINARY(8000),
  INDEX(ID(1000))
 ) ENCRYPTION='y'", []));
 echo "OK!\r\n";
 echo "Populating Pages Index...\r\n";
 $files = $gw->system->DatabaseSet("PG") ?? [];
 foreach($files as $key => $value) {
  $value = str_replace("c.oh.pg.", "", $value);
  $page = $gw->system->Data("Get", ["pg", $value]) ?? [];
  echo "Populating data for ".$page["ID"]."...";
  $gw->system->SQL("INSERT INTO Pages(Body, Description, ID, Title)
       VALUES(AES_ENCRYPT(:Body, :key),
              AES_ENCRYPT(:Description, :key),
              AES_ENCRYPT(:ID, :key),
              AES_ENCRYPT(:Title, :key))", [
   ":Body" => substr($gw->system->PlainText([
    "Data" => $page["Body"],
    "Decode" => 1,
    "Display" => 1,
    "HTMLDecode" => 1
   ]), 0, 5000),
   ":Description" => $page["Description"],
   ":ID" => $page["ID"],
   ":Title" => $page["Title"],
   ":key" => base64_decode($key)
  ]);
  echo "OK!\r\n";
 }
 echo "Done re-populating Pages Index...\r\n";
 echo "Sampling the new Pages data set, searching for \"About\"...\r\n";
 $pages = $gw->system->SQL("SELECT *, AES_DECRYPT(Body, :key) AS Body,
                        AES_DECRYPT(Description, :key) AS Description,
                        AES_DECRYPT(ID, :key) AS ID,
                        AES_DECRYPT(Title, :key) AS Title
 FROM Pages
 WHERE CONVERT(AES_DECRYPT(Body, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(Description, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(ID, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(Title, :key) USING utf8mb4) LIKE :search", [
  ":key" => base64_decode($key),
  ":search" => "%About%"
 ]);
 print_r($pages->fetchAll(PDO::FETCH_ASSOC));
 echo "OK\r\n";
 echo "Data has been indexed and is ready for searching as of ".$gw->system->timestamp.".\r\n";
 echo "Exiting...\r\n";
?>