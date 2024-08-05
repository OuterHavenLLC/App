<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Link";
 $categorySQL = $category."s";
 $newRows = 0;
 $oh = New OH;
 $index = $oh->core->Data("Get", ["app", md5("Links")]) ?? [];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>$category</em> index file."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]);
 $sql = New SQL($oh->core->cypher->SQLCredentials());
 $query = "CREATE TABLE IF NOT EXISTS $categorySQL(
  Link_Description text not null,
  Link_Keywords text not null,
  Link_IconExists text not null,
  Link_ID varchar(64) not null,
  Link_Title text not null,
  PRIMARY KEY(Link_ID)
 )";
 $sql->query($query, []);
 $sql->execute();
 $r .= $oh->core->Element([
  "p", "Indexing data..."
 ]);
 $sql->query("SELECT * FROM $categorySQL", []);
 $data = $sql->set();
 foreach($data as $data) {
  $_Link = $data["Link_ID"];
  $curl = curl_init($_Link);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
  $linkData = curl_exec($curl);
  curl_close($curl);
  $dom = new DOMDocument();
  libxml_use_internal_errors(true);
  $dom->loadHTML($linkData);
  libxml_use_internal_errors(false);
  $tags = get_meta_tags($_Link) ?? [];
  $description = $tags["description"] ?? "No Description";
  $keywords = $tags["keywords"] ?? "None";
  $icon = parse_url($_Link, PHP_URL_SCHEME)."://".parse_url($_Link, PHP_URL_HOST); 
  $icon = trim($icon, "/");
  $icon = "$icon/apple-touch-icon.png";
  $iconExists = ($oh->core->RenderHTTPResponse($icon) == 200) ? 1 : 0;
  $title = $dom->getElementsByTagName("title")->item(0)->nodeValue ?? "Untitled";
  $query = "UPDATE $categorySQL
                    SET Link_Description=:Description,
                            Link_Keywords=:Keywords,
                            Link_IconExists=:IconExists,
                            Link_Title=:Title
                    WHERE Link_ID=:ID
  ";
  $sql->query($query, [
   ":Description" => $description,
   ":Keywords" => $keywords,
   ":IconExists" => $iconExists,
   ":ID" => $_Link,
   ":Title" => $title
  ]);
  $sql->execute();
  $r .= $oh->core->Element(["p", "$_Link... OK"]);
  $newRows++;
 }
 $r .= $oh->core->Element([
  "p", "Saving..."
 ]);
 $r .= $oh->core->Element([
  "p", "Re:Search indexing complete! $newRows entries indexed on ".$oh->core->timestamp."."
 ]).$oh->core->Element([
  "p", "Done"
 ]);
 echo $r;
?>