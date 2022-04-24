#!/bin/bash
file1="$1"
file2="$2"

declare -A array_to_print
declare -A sample_match_data1
declare -A sample_match_data2
declare sm_counter
declare -A dd_chart_data1
declare -A dd_chart_data2
declare dd_chart_counter
declare -A asb_chart_data1
declare -A asb_chart_data2
declare asb_chart_counter
declare -A y_chart_data1
declare -A y_chart_data2
declare y_chart_counter
declare -A x_chart_data1
declare -A x_chart_data2
declare x_chart_counter
declare -A ancient_population_data1
declare -A ancient_population_data2
declare ancient_population_counter
declare -A modern_population_data1
declare -A modern_population_data2
declare modern_population_counter
declare -A ancient_relatives_data1
declare -A ancient_relatives_data2
declare ancient_relatives_data1
### FUNCTIONS ######################################################################

  function samplematch_to_array() {
    sm_counter=0
    while read line; do
      while IFS=";" read -r sm_no sm_name sm_ydna sm_mtdna sm_age sm_gd sm_aid; do
        sample_match_data1[$sm_counter,sm_no]="$sm_no"
        sample_match_data1[$sm_counter,sm_name]="$sm_name"
        sample_match_data1[$sm_counter,sm_ydna]="$sm_ydna"
        sample_match_data1[$sm_counter,sm_mtdna]="$sm_mtdna"
        sample_match_data1[$sm_counter,sm_age]="$sm_age"
        sample_match_data1[$sm_counter,sm_gd]="$sm_gd"
        sample_match_data1[$sm_counter,sm_aid]="$sm_aid"
        ((sm_counter++))
      done< <(echo $line|sed 's/.*Sample match \#//'|sed 's/\(:<br>\|<br>Y-DNA:\s*\|<br>mtDNA:\s*\|<br>Age:\s*\|<br>Genetic Distance:\s*\|<br>Archaeological ID:\s*\|\",\|<END>\)/\;/g')
    done< <(grep -P 'Sample match #\d+:<br>[^<]*<br>Y-DNA.*Age:.*Genetic Distance:' $1)
  }

  function print_array_as_table() {
    var=$(declare -p "$1")
    eval "declare -A array_to_print="${var#*=}
    table_name="$2"
    IFS=';' read -a headers<<<"$3"
    IFS=';' read -a keys <<<"$4"
    maxelements=$5

    echo "<h4>$table_name</h4>"
    echo "<table>"
    echo -n "<tr>"
    for header in "${headers[@]}"; do
      echo -n "<td>$header</td>"
    done
    echo "</tr>"
    for ((row=0;row<$maxelements;row++)); do
      echo -n "<tr>"
      for key in "${keys[@]}"; do
        echo -n "<td>${array_to_print[$row,$key]}</td>"
      done
      echo "</tr>"
    done
    echo "</table><br>"
  }

  function asb_chart_to_array() {
    asb_chart_counter=0
    line_counter=0
    while read line; do
      element_counter=0
      elements=$(echo $line|sed 's/\"//g'|sed 's/, /\n/g'|sed 's/ /-/g')
      for element in $elements; do
        asb_chart_data1[$line_counter,$element_counter]="$element"
        ((element_counter++))
        if [[ "$line_counter" -eq 0 ]]; then
          ((asb_chart_counter++))
        fi
      done
    ((line_counter++))
    done< <(grep -A30 "function refreshFunctionChartAncient1" $1|egrep 'labels: \[|data: \[|backgroundColor: \['|sed 's/.*\[//'|sed 's/\].*//')
  }

  function dd_chart_to_array() {
    dd_chart_counter=0
    line_counter=0
    while read line; do
      element_counter=0
      elements=$(echo $line|sed 's/\"//g'|sed 's/, /\n/g'|sed 's/ /-/g')
      for element in $elements; do
        dd_chart_data1[$line_counter,$element_counter]="$element"
        ((element_counter++))
        if [[ "$line_counter" -eq 0 ]]; then
          ((dd_chart_counter++))
        fi
      done
    ((line_counter++))
    done< <(grep -A30 "function refreshFunctionChartDDAncient1" $1|egrep 'labels: \[|data: \[|backgroundColor: \['|sed 's/.*\[//'|sed 's/\].*//')
  }

  function y_chart_to_array() {
    y_chart_counter=0
    line_counter=0
    while read line; do
      element_counter=0
      elements=$(echo $line|sed 's/\"//g'|sed 's/, /\n/g'|sed 's/ /-/g')
      for element in $elements; do
        y_chart_data1[$line_counter,$element_counter]="$element"
        ((element_counter++))
        if [[ "$line_counter" -eq 0 ]]; then
          ((y_chart_counter++))
        fi
      done
    ((line_counter++))
    done< <(grep -A30 'document.getElementById("pieChartY' $1|egrep 'labels: \[|data: \[|backgroundColor: \['|sed 's/.*\[//'|sed 's/\].*//')
  }

  function x_chart_to_array() {
    x_chart_counter=0
    line_counter=0
    while read line; do
      element_counter=0
      elements=$(echo $line|sed 's/\"//g'|sed 's/, /\n/g'|sed 's/ /-/g')
      for element in $elements; do
        x_chart_data1[$line_counter,$element_counter]="$element"
        ((element_counter++))
        if [[ "$line_counter" -eq 0 ]]; then
          ((x_chart_counter++))
        fi
      done
    ((line_counter++))
    done< <(grep -A30 'document.getElementById("pieChartX' $1|egrep 'labels: \[|data: \[|backgroundColor: \['|sed 's/.*\[//'|sed 's/\].*//')
  }

  function print_chart_as_table() {
    var=$(declare -p "$1")
    eval "declare -A array_to_print="${var#*=}
    table_name="$2"
    maxelements=$3
    IFS=';' read -a headers<<<"$4"
    unit=$5

    echo "<h4>$table_name</h4>"
    echo "<table style="width:400px">"
    echo -n "<tr>"
    for header in "${headers[@]}"; do
      echo -n "<td>$header</td>"
    done
    echo "</tr>"
    for ((row=0;row<$maxelements;row++)); do
      echo -n "<tr>"
      for ((col=0;col<3;col++)); do
        echo -n "<td"
        if [[ "$col" -eq 2 ]]; then
          echo -n " style=\"background-color:${array_to_print[2,$row]};\">"
        else   
          echo -n ">${array_to_print[$col,$row]}"|sed 's/-/ /g'
        fi
        if [[ "$col" -eq 1 ]]; then
          echo -n "$unit"
        fi
        echo -n "</td>"
      done
      echo "</tr>"
    done
    echo "</table><br>"
  }

  function print_chart2_as_table() {
    var=$(declare -p "$1")
    eval "declare -A array_to_print="${var#*=}
    table_name="$2"
    maxelements=$3
    IFS=';' read -a headers<<<"$4"
    unit=$5

    echo "<h4>$table_name</h4>"
    echo "<table>"
    echo -n "<tr>"
    for header in "${headers[@]}"; do
      echo -n "<td>$header</td>"
    done
    echo "</tr>"
    for ((row=0;row<$maxelements;row++)); do
      echo -n "<tr>"
      for ((col=0;col<8;col++)); do
        echo -n "<td"
          if [[ "${array_to_print[$col,$row]}" == "Exact-Match" ]]; then
             echo " style=\"background-color:#ffbbbb;\""
          fi
          echo -n ">${array_to_print[$col,$row]}"|sed 's/-/ /g'
        if [[ "$col" -eq 1 ]]; then
          echo -n "$unit"
        fi
        echo -n "</td>"
      done
      echo "</tr>"
    done
    echo "</table><br>"
  }

  function ancient_population_to_array() {
    ancient_population_counter=0
    while read line; do
      while IFS=";" read -r pop_name pop_gd; do
        ancient_population_data1[$ancient_population_counter,pop_name]="$pop_name"
        ancient_population_data1[$ancient_population_counter,pop_gd]="$pop_gd"
        ((ancient_population_counter++))
      done< <(echo $line)
    done< <(grep 'Your closest Ancient populations' -A3 $1|grep -P '\(\d*\.\d*\)'|sed 's/<\/font>/\n/g'|grep color|sed 's/.*>//g'|sed 's/(/\;/g'|sed 's/)//g')
  }

  function modern_population_to_array() {
    modern_population_counter=0
    while read line; do
      while IFS=";" read -r pop_name pop_gd; do
        modern_population_data1[$modern_population_counter,pop_name]="$pop_name"
        modern_population_data1[$modern_population_counter,pop_gd]="$pop_gd"
        ((modern_population_counter++))
      done< <(echo $line)
    done< <(grep 'Your closest genetic mo' -A3 $1|grep -P '\(\d*\.\d*\)'|sed 's/<br>/\n/g'|grep '('|sed 's/^.*\.\s//g'|sed 's/(/\;/g'|sed 's/)//g')
  }

  function ancient_relatives_to_array() {
    ancient_relatives_counter=0
    line_counter=0
    while read line; do
      element_counter=0
      elements=$(echo $line|sed 's/\"//g'|sed 's/,/\n/g'|sed 's/ /-/g')
      for element in $elements; do
        ancient_relatives_data1[$line_counter,$element_counter]="$element"
        ((element_counter++))
        if [[ "$line_counter" -eq 0 ]]; then
          ((ancient_relatives_counter++))
        fi
      done
    ((line_counter++))
    done< <(grep 'afterLabel: function(tooltipItem, data)' -A10 $1|egrep 'var tags=\[|var smplen=\[|var smptotal=\[|var dist=\[|var names=\[|var smpuser=\[|var civ=\[|var age=\['|sed 's/.*\[//'|sed 's/\].*//')
  }

### MAIN ############################################################################

echo '<div class="row">'
  echo '<div class="column" style="background-color:#fafafa;">'
    if [[ ! -z "$file1" ]]; then
      asb_chart_to_array $file1
      print_chart_as_table "asb_chart_data1" "Ancient Sample Breakdown Chart Data" $asb_chart_counter "Name;Percentage;Color" "%"

      dd_chart_to_array $file1
      print_chart_as_table "dd_chart_data1" "Deep Dive Chart Data" $dd_chart_counter "Name;Percentage;Color" "%"

      y_chart_to_array $file1
      print_chart_as_table "y_chart_data1" "Y-DNA Haplogroups Chart Data" $y_chart_counter "ID;Percentage;Color" "%"

      x_chart_to_array $file1
      print_chart_as_table "x_chart_data1" "mtDNA Haplogroups Chart Data" $x_chart_counter "ID;Percentage;Color" "%"

      ancient_population_to_array $file1
      print_array_as_table "ancient_population_data1" "Closest Ancient Populations" "Name;GD" "pop_name;pop_gd" $ancient_population_counter

      modern_population_to_array $file1
      print_array_as_table "modern_population_data1" "Closest Modern Populations" "Name;GD" "pop_name;pop_gd" $modern_population_counter
 
      samplematch_to_array $file1
      print_array_as_table "sample_match_data1" "Maching Samples" "No;Name;Y-DNA;mtDNA;Age;GD;Arch ID" "sm_no;sm_name;sm_ydna;sm_mtdna;sm_age;sm_gd;sm_aid" $sm_counter

      ancient_relatives_to_array $file1
      print_chart2_as_table "ancient_relatives_data1" "Ancient Relatives" $ancient_relatives_counter "ID;SMPlen;SMPtotal;GD;Name;SMPuser;Civ;Age"

    fi
  echo '</div>'
  echo '<div class="column" style="background-color:#fafafa;">'
    if [[ ! -z "$file2" ]]; then
      asb_chart_to_array $file2
      print_chart_as_table "asb_chart_data1" "Ancient Sample Breakdown Chart Data" $asb_chart_counter "Name;Percentage;Color" "%"

      dd_chart_to_array $file2
      print_chart_as_table "dd_chart_data1" "Deep Dive Chart Data" $dd_chart_counter "Name;Percentage;Color" "%"

      y_chart_to_array $file2
      print_chart_as_table "y_chart_data1" "Y-DNA Haplogroups Chart Data" $y_chart_counter "ID;Percentage;Color" "%"

      x_chart_to_array $file2
      print_chart_as_table "x_chart_data1" "mtDNA Haplogroups Chart Data" $x_chart_counter "ID;Percentage;Color" "%"

      ancient_population_to_array $file2
      print_array_as_table "ancient_population_data1" "Closest Ancient Populations" "Name;GD" "pop_name;pop_gd" $ancient_population_counter

      modern_population_to_array $file2
      print_array_as_table "modern_population_data1" "Closest Modern Populations" "Name;GD" "pop_name;pop_gd" $modern_population_counter

      samplematch_to_array $file2
      print_array_as_table "sample_match_data1" "Maching Samples" "No;Name;Y-DNA;mtDNA;Age;GD;Arch ID" "sm_no;sm_name;sm_ydna;sm_mtdna;sm_age;sm_gd;sm_aid" $sm_counter

      ancient_relatives_to_array $file1
      print_chart2_as_table "ancient_relatives_data1" "Ancient Relatives" $ancient_relatives_counter "ID;SMPlen;SMPtotal;GD;Name;SMPuser;Civ;Age"
      
    fi
  echo '</div>'
echo '</div>'

