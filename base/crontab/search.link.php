<?php
 # Re:Search Index
 require_once("/var/www/html/base/Bootloader.php");
 $category = "Link";
 $oh = New OH;
 $index = $oh->core->Data("Get", ["app", md5("Links")]) ?? [];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Re:Search Index"
 ]).$oh->core->Element([
  "p", "This tool maintains the Re:Search <em>$category</em> index file."
 ]).$oh->core->Element([
  "p", "Fetching source database list..."
 ]).$oh->core->Element([
  "p", json_encode($index, true)
 ]);
 foreach($index as $link => $info) {
  $curl = curl_init($link);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
  $linkData = curl_exec($curl);
  curl_close($curl);
  $dom = new DOMDocument();
  libxml_use_internal_errors(true);
  $dom->loadHTML($linkData);
  libxml_use_internal_errors(false);
  $icon = parse_url($link, PHP_URL_SCHEME)."://".parse_url($link, PHP_URL_HOST); 
  $icon = trim($icon, "/");
  $icon = "$icon/apple-touch-icon.png";
  $iconExists = ($this->core->RenderHTTPResponse($icon) == 200) ? 1 : 0;
  $tags = get_meta_tags($link) ?? [];
  $description = $tags["description"] ?? "No Description";
  $keywords = $tags["keywords"] ?? "No Keywords";
  $title = $dom->getElementsByTagName("title")->item(0)->nodeValue ?? "No Title";
  $index[$link] = [
   "Description" => $description,
   "Keywords" => $keywords,
   "IconExsists" => $iconExists,
   "Title" => $title
  ];
 }
 $r .= $oh->core->Element([
  "p", "Saving..."
 ]);
 #$oh->core->Data("Save", ["app", md5("Links"), $index]);
 $r .= $oh->core->Element([
  "p", "Re:Search indexing complete!"
 ]).$oh->core->Element([
  "p", "Here is the <em>$category</em> Re:Search Index:"
 ]).$oh->core->Element([
  "p", json_encode($index, true)
 ]).$oh->core->Element([
  "p", "Done"
 ]);
 echo $r;
?>