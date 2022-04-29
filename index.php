<html>
   <title>MTA-RDE</title>
   <link rel="icon" type="image/x-icon" href="images/favicon.ico">
   <head>
     <link rel="stylesheet" href="style.css">
   </head>

   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">
     google.charts.load('current', {'packages':['table', 'corechart', 'bar']});
   </script>

   <body style="font-family: Arial, Helvetica, sans-serif;">

   <h2>MyTrueAncestry - Raw Data Extractor</h3>
   <button onclick="helpEnFunction()">Help (En)</button>
   <button onclick="helpHuFunction()">Help (Hu)</button>
   <button onClick="window.open('https://github.com/khelmric/mta-rde');"> 
     <span>Code Repo on Github</span>
   </button>

   <div id="helpEn" style="display:none">
     <ol>
       <li>Login to <a href="https://mytrueancestry.com">https://mytrueancestry.com</a>.</li>
       <li>Save the webpage in html format.</li>
       <li>On the <a href="http://oci.atomx.hu/mta-rde">http://oci.atomx.hu/mta-rde</a> select the html file(s). 
           (Selecting the second file allows a comparsion of two results.)</li>
     </ol>   
   </div>
   <div id="helpHu" style="display:none">
     <ol>
       <li>Jelentkezz be a <a href="https://mytrueancestry.com">https://mytrueancestry.com</a> oldalra.</li>
       <li>Mentsd le a weboldalt html formátumban.</li>
       <li>A <a href="http://oci.atomx.hu/mta-rde">http://oci.atomx.hu/mta-rde</a> oldalon válaszd ki a html fájlt/fájlokat. 
           (A második fájl kiválasztásával összehasonlítható két különböző eredmény.)</li>
     </ol>
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

    function chartDataTransform(textContent, mREGEX, sREGEX) {
      textContent = textContent.match(mREGEX);
      textContent = String(textContent);
      textContent = textContent.match(sREGEX);
      textContent = String(textContent);
      REGEX = /\"\s?\,\s?\"/g;
      textContent = textContent.replace(REGEX, ',');
      textContent = String(textContent);
      REGEX = /[\s\S]*?\[|\]|\"/g;
      textContent = textContent.replace(REGEX, '');
      textContent = String(textContent);
      return textContent;
    }

    function listDataTransform(textContent, matchNo, mREGEX, sREGEX1, sREGEX2, sREGEX3, sREGEX4, sREGEX5, sREGEX6) {
      textContent = textContent.match(mREGEX);
      textContent = String(textContent[matchNo]);
      textContent = textContent.matchAll(sREGEX1);
      textContent = Array.from(textContent);
      textContent = String(textContent);
      textContent = textContent.replace(sREGEX2, '');
      textContent = String(textContent);
      textContent = textContent.replace(sREGEX3, ';');
      textContent = String(textContent);
      textContent = textContent.replace(sREGEX4, ',');
      textContent = String(textContent);
      textContent = textContent.replace(sREGEX5, '');
      textContent = String(textContent);
      textContent = textContent.replace(sREGEX6, ',');
      textContent = String(textContent);
      return textContent;
    }

    function printChartAsTable(textContent, mainREGEX, subREGEX1, subREGEX2, subREGEX3, targetDivId) {
      chartLabels = chartDataTransform(textContent, mainREGEX, subREGEX1);
      chartLabelArray = chartLabels.split(',');

      chartData = chartDataTransform(textContent, mainREGEX, subREGEX2);
      chartDataArray = chartData.split(',');

      chartBgColor = chartDataTransform(textContent, mainREGEX, subREGEX3);
      chartBgColorArray = chartBgColor.split(',');

      var data = new google.visualization.DataTable();

      data.addColumn('string', 'Description');
      data.addColumn('string', 'Value');
      data.addColumn('string', 'Color');

      for (var i = 0; i < chartLabelArray.length; i++) {
        var rowContent = 'data.addRow([chartLabelArray[i], chartDataArray[i], {v: null, f: null, p: {style: "background-color: ' + chartBgColorArray[i] + ';"}}]);';
        eval(rowContent);
      }
      var table = new google.visualization.Table(targetDivId);
      table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true});

//      data.removeColumn(2);
//      var chart1 = new google.visualization.PieChart(document.getElementById("test_div"));
//      chart1.draw(data);

    }

    function printListAsTable(textContent, matchNo, mainREGEX, subREGEX1, subREGEX2, subREGEX3, subREGEX4, subREGEX5, subREGEX6, targetDivId, tableHeader) {
      listContent = listDataTransform(textContent, matchNo, mainREGEX, subREGEX1, subREGEX2, subREGEX3, subREGEX4, subREGEX5, subREGEX6);
      listContentLineArray = listContent.split(';');

      var data = new google.visualization.DataTable();

      
      headersArray = tableHeader.split(',');
      for (var i = 0; i < headersArray.length; i++) {
        data.addColumn('string', headersArray[i]);
      }

      for (var i = 0; i < listContentLineArray.length-1; i++) {
        listContentElementArray = listContentLineArray[i].split(',');
        var rowContent = "";
        for (var j = 0; j < listContentElementArray.length; j++) {
          cleanVar = listContentElementArray[j].replace(/\<br\>/g, '');
          if (rowContent) {
            rowContent += ", '" + cleanVar + "'";
          } else {
            rowContent = "data.addRow(['" + cleanVar + "'";
          }
        }
        rowContent += "]);";
        eval(rowContent);
      }
      var table = new google.visualization.Table(targetDivId);
      table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true});
    }
 
    async function openFileFunction(event,col) {
      const selectedFile = event.target.files.item(0);
      textContent = await selectedFile.text();

      mainREGEXasb=/refreshFunctionChartAncient1\(jobid\) \{[\s\S]*?\}/g;
      mainREGEXdd=/refreshFunctionChartDDAncient1\(jobid\) \{[\s\S]*?\}/g;
      mainREGEXydna=/document.getElementById\(\"pieChartY[\s\S]*?\{[\s\S]*?\}/g;
      mainREGEXmtdna=/document.getElementById\(\"pieChartX[\s\S]*?\{[\s\S]*?\}/g;
      subREGEX1=/labels[\s\S]*?\]/g;
      subREGEX2=/data[\s\S]*?\]/g;
      subREGEX3=/backgroundColor[\s\S]*?\]/g;

      if (col == 1) {
        printChartAsTable(textContent, mainREGEXasb, subREGEX1, subREGEX2, subREGEX3, document.getElementById("asb1_result"));
        printChartAsTable(textContent, mainREGEXdd, subREGEX1, subREGEX2, subREGEX3, document.getElementById("dd1_result"));
        printChartAsTable(textContent, mainREGEXydna, subREGEX1, subREGEX2, subREGEX3, document.getElementById("ydna1_result"));
        printChartAsTable(textContent, mainREGEXmtdna, subREGEX1, subREGEX2, subREGEX3, document.getElementById("mtdna1_result"));
      } else {
        printChartAsTable(textContent, mainREGEXasb, subREGEX1, subREGEX2, subREGEX3, document.getElementById("asb2_result"));
        printChartAsTable(textContent, mainREGEXdd, subREGEX1, subREGEX2, subREGEX3, document.getElementById("dd2_result"));
        printChartAsTable(textContent, mainREGEXydna, subREGEX1, subREGEX2, subREGEX3, document.getElementById("ydna2_result"));
        printChartAsTable(textContent, mainREGEXmtdna, subREGEX1, subREGEX2, subREGEX3, document.getElementById("mtdna2_result"));
      }

      mainREGEXsm=/refreshFunctionGlobeHaplo1\(samplemax\) \{[\s\S]*?\]\;/g;
      subREGEX1sm=/title[\s\S]*?\,/g;
      subREGEX2sm=/title\"\:\s?\"Sample\s?match\s?\#/g;
      subREGEX3sm=/\"\,\,|\"\,/g;
      subREGEX4sm=/Y-DNA\:\s?|mtDNA\:\s?|Age\:\s?|Genetic\s?Distance\:\s?|Archaeological\s?ID\:\s?/g;
      subREGEX5sm=/\r*|\n*/g;
      subREGEX6sm=/\:/g;
      smMatchNo=0;
      smTableHeader="No,Name,Y-DNA,mtDNA,Age,GD,Arch ID";

      mainREGEXddar=/dataDD\=\[[\s\S]*?\]\;/g;
      subREGEX1ddar=/title[\s\S]*?\,/g;;
      subREGEX2ddar=/title\"\:\s?\"Deep\s?Dive\s?match\:/g;
      subREGEX3ddar=/\"\,\,|\"\,/g;
      subREGEX4ddar=/Y-DNA\:\s?|mtDNA\:\s?|Age\:\s?|Longest\s?Shared\s?DNA\:\s?|Archaeological\s?ID\:\s?/g;
      subREGEX5ddar=/\r*|\n*/g;
      subREGEX6ddar=/\:/g;
      ddarMatchNo=1;
      ddarTableHeader="Name,Y-DNA,mtDNA,Age,Longest Shared DNA,Arch ID";

      if (col == 1) {
        printListAsTable(textContent, smMatchNo, mainREGEXsm, subREGEX1sm, subREGEX2sm, subREGEX3sm, subREGEX4sm, subREGEX5sm, subREGEX6sm, document.getElementById("sm1_result"), smTableHeader);
        printListAsTable(textContent, ddarMatchNo, mainREGEXddar, subREGEX1ddar, subREGEX2ddar, subREGEX3ddar, subREGEX4ddar, subREGEX5ddar, subREGEX6ddar, document.getElementById("ddar1_result"), ddarTableHeader);
      } else {
        printListAsTable(textContent, smMatchNo, mainREGEXsm, subREGEX1sm, subREGEX2sm, subREGEX3sm, subREGEX4sm, subREGEX5sm, subREGEX6sm, document.getElementById("sm2_result"), smTableHeader);
        printListAsTable(textContent, ddarMatchNo, mainREGEXddar, subREGEX1ddar, subREGEX2ddar, subREGEX3ddar, subREGEX4ddar, subREGEX5ddar, subREGEX6ddar, document.getElementById("ddar2_result"), ddarTableHeader);
      }

    }

   </script>

  <div class="row">
    <div class="column">
      <input type="file" style="width:400" ACCEPT="text/html" onchange="openFileFunction(event,1)" />
    </div>
    <div class="column">
      <input type="file" style="width:400" ACCEPT="text/html" onchange="openFileFunction(event,2)" />
    </div>
  </div>

  <hr>

  <div class="row" id="asb">
    <h3>Ancient Samples - Civilization Full Breakdown</h3>
    <div class="column">
      <div id="asb1_chart1"></div>
      <div id="asb1_result"></div>
    </div>
    <div class="column">
      <div id="asb2_chart1"></div>
      <div id="asb2_result"></div>
    </div>
  </div>

  <div class="row" id="dd">
    <h3>Deep Dive Full Breakdown</h3>
    <div class="column">
      <div id="dd1_result"></div>
    </div>
    <div class="column">
      <div id="dd2_result"></div>
    </div>
  </div>

  <div class="row" id="ydna">
    <h3>Y-DNA Breakdown</h3>
    <div class="column">
      <div id="ydna1_result"></div>
    </div>
    <div class="column">
      <div id="ydna2_result"></div>
    </div>
  </div>

  <div class="row" id="mtdna">
    <h3>mtDNA Breakdown</h3>
    <div class="column">
      <div id="mtdna1_result"></div>
    </div>
    <div class="column">
      <div id="mtdna2_result"></div>
    </div>
  </div>

  <div class="row" id="sm">
    <h3>The Closest Archaeogenetic matches</h3>
    <div class="column">
      <div id="sm1_result"></div>
    </div>
    <div class="column">
      <div id="sm2_result"></div>
    </div>
  </div>

  <div class="row" id="ddar">
    <h3>Deep Dive - Ancient Relatives</h3>
    <div class="column">
      <div id="ddar1_result"></div>
    </div>
    <div class="column">
      <div id="ddar2_result"></div>
    </div>
  </div>
  <div id="test_div"></div>
   </body>
</html>
