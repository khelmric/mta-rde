<?php
   if(!empty($_FILES['html_file']['name'])){
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
      }else{
         print_r($errors);
      }
   }
   if(!empty($_FILES['html_file2']['name'])){
      $errors2= array();
      $timestamp2=time();
      $file_name2 = $timestamp2 . "-" . $_FILES['html_file2']['name'];
      $file_path2="uploads/" . $file_name2;
      $file_size2 = $_FILES['html_file2']['size'];
      $file_tmp2 = $_FILES['html_file2']['tmp_name'];
      $file_type2 = $_FILES['html_file2']['type'];
      $file_ext2=strtolower(end(explode('.',$_FILES['html_file2']['name'])));

      $extensions= array("html");

      if(in_array($file_ext2,$extensions)=== false){
         $errors2[]="extension not allowed, please choose a HTML file.";
      }

      if($file_size2 > 10485760) {
         $errors2[]='File size cannot be greater than 10 MB';
      }

      if(empty($errors2)==true) {
         move_uploaded_file($file_tmp2,$file_path2);
      }else{
         print_r($errors2);
      }
   }
?>
<html>
   <title>MTA-RDE</title>
   <head>
     <link rel="stylesheet" href="style.css">
   </head>
   <body>
   <h2 style="text-align: center;">MyTrueAncestry - Raw Data Extractor</h3>
   <button onclick="helpEnFunction()">Help (En)</button>
   <button onclick="helpHuFunction()">Help (Hu)</button>
   <button onClick="window.open('https://github.com/khelmric/mta-rde');"> 
     <span>Code Repo on Github</span>
   </button>

   <div id="helpEn" style="display:none">
     <ol>
       <li>Login to <a href="https://mytrueancestry.com">https://mytrueancestry.com</a>.</li>
       <li>Save the webpage in html format.</li>
       <li>On the <a href="http://oci.atomx.hu/mta-rde">http://oci.atomx.hu/mta-rde</a> select the html file. (Selecting the second file allows a comparsin for two results.)</li>
       <li>Click on the "Submit" button.</li>
     </ol>   
     The uploaded file will be stored temporary (<5 min) on the server. 
   </div>
   <div id="helpHu" style="display:none">
     <ol>
       <li>Jelentkezz be a <a href="https://mytrueancestry.com">https://mytrueancestry.com</a> oldalra.</li>
       <li>Mentsd le a weboldalt html formátumban.</li>
       <li>A <a href="http://oci.atomx.hu/mta-rde">http://oci.atomx.hu/mta-rde</a> oldalon válaszd ki a html fájlt. (A második fájl kiválasztásával összehasonlítható két különböző eredmény.)</li>
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

  <div class="row">
    <div class="column">
      <form action = "" method = "POST" enctype = "multipart/form-data">
         File1: <input type = "file" name = "html_file" /><br>
         File2: <input type = "file" name = "html_file2" /><br>
         <br><input type = "submit"/>
           <br><br>File name  : <?php echo $_FILES['html_file']['name'];  ?>
      </form>
    </div>
    <div class="column">
           <br><br><br><br><br>File name  : <?php echo $_FILES['html_file2']['name'];  ?>
    </div>
  </div>
      <hr>

<?php
  if(!empty($_FILES['html_file']['name']) || !empty($_FILES['html_file2']['name'])){
      $script_with_parameters="./mta-data-to-html.sh " . $file_path . " " . $file_path2;
      exec($script_with_parameters, $output, $status);
      foreach($output as $value) {
          echo $value;
      }
  }
  
?>
   </body>
</html>
