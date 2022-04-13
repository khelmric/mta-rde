<?php
   if(isset($_FILES['html_file'])){
      $errors= array();
      $timestamp=time();
      $file_name = $timestamp . "-" . $_FILES['html_file']['name'];
      $file_path="uploads/" . $file_name;
      $file_size = $_FILES['html_file']['size'];
      $file_tmp = $_FILES['html_file']['tmp_name'];
      $file_type = $_FILES['html_file']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['html_file']['name'])));

      $extensions= array("html");

      if(in_array($file_ext,$extensions)=== false){
         $errors[]="extension not allowed, please choose a HTML file.";
      }

      if($file_size > 10485760) {
         $errors[]='File size cannot be greater than 10 MB';
      }

      if(empty($errors)==true) {
         move_uploaded_file($file_tmp,$file_path);
//         echo "Success";
      }else{
         print_r($errors);
      }
   }
?>
<html>
   <title>MTA-RDE</title>
   <head>
     <link rel="stylesheet" href="style.css">
   </head>
   <body>
   <h3>MTA - Raw Data Extractor</h3>
   <button onclick="helpEnFunction()">Help (En)</button>
   <button onclick="helpHuFunction()">Help (Hu)</button>

   <div id="helpEn" style="display:none">
     <ol>
       <li>Login to <a href="https://mytrueancestry.com">https://mytrueancestry.com</a>.</li>
       <li>Save the webpage in html format.</li>
       <li>On the <a href="http://oci.atomx.hu/mta-rde">http://oci.atomx.hu/mta-rde</a> select the html file.</li>
       <li>Click on the "Submit" button.</li>
     </ol>   
     The uploaded file will be stored temporary (<5 min) on the server. 
   </div>
   <div id="helpHu" style="display:none">
     <ol>
       <li>Jelentkezz be a <a href="https://mytrueancestry.com">https://mytrueancestry.com</a> oldalra.</li>
       <li>Mentsd le a weboldalt html formátumban.</li>
       <li>A <a href="http://oci.atomx.hu/mta-rde">http://oci.atomx.hu/mta-rde</a> oldalon válaszd ki a html fájlt.</li>
       <li>Kattints a "Küldés" gombra.</li>
     </ol>
     A feltöltött fájl ideiglenesen (<5 perc) tárolódik a szerveren.
   </div>

   <script>
     function helpHuFunction() {
       var divhelphu = document.getElementById("helpHu");
       var divhelpen = document.getElementById("helpEn");
       if (divhelphu.style.display === "none") {
         divhelphu.style.display = "block";
         divhelpen.style.display = "none"
       } else {
         divhelphu.style.display = "none";
       }
     }
     function helpEnFunction() {
       var divhelphu = document.getElementById("helpHu");
       var divhelpen = document.getElementById("helpEn");
       if (divhelpen.style.display === "none") {
         divhelpen.style.display = "block";
         divhelphu.style.display = "none"
       } else {
         divhelpen.style.display = "none";
       }
     }
   </script>

      <form action = "" method = "POST" enctype = "multipart/form-data">
         <input type = "file" name = "html_file" />
         <input type = "submit"/>

<!--         <ul>
            <li>Sent file  : <?php echo $_FILES['html_file']['name'];  ?>
            <li>Stored file: <?php echo $file_name;  ?>
            <li>File size  : <?php echo $_FILES['html_file']['size'];  ?>
            <li>File type  : <?php echo $_FILES['html_file']['type'] ?>
         </ul>
-->
      </form>

<?php
  $html = file_get_contents("uploads/".$file_name);
  $html_data2 = explode(PHP_EOL, $html);
  $remaining = array_filter($html_data2, function($line) {
     return strpos($line, 'Sample match #') !== false;
  });
  $remaining = preg_replace('/^.*<br>.*<br>.*<br>.*<br>.*<br>.*<br>.*<br>.*$/i', '\1', $remaining);
  $remaining = array_map('trim', $remaining);
    $remaining = array_filter($remaining, function($value) {
        return $value !== '';
    });
  $remaining = preg_replace('/^(.*)$/', '<tr><td>$1</td></tr>', $remaining);
  $remaining = preg_replace('/<BR>/i', '</td><td>', $remaining);
  $patterns_to_remove = array('/\"/','/title: /','/,/');
  $remaining = preg_replace($patterns_to_remove, '', $remaining);
  $remaining = preg_replace('/^(<tr><td>Sample match #1:)/', '</table><table>$1', $remaining);
  $remaining = preg_replace('/<table>/', '<hr><h3>Raw data</h3><table>', $remaining);
  $remaining = implode(PHP_EOL, $remaining);
  $remaining = "<table>" . $remaining . "</table>";
  echo $remaining;
//  echo implode(PHP_EOL, $remaining);

?>
   </body>
</html>
