#!/bin/bash
file1="$1"
file2="$2"

declare -A array_to_print
declare -A sample_match_data1
declare sm_counter
declare -A dd_chart_data1
declare dd_chart_counter
declare -A asb_chart_data1
declare asb_chart_counter

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
    echo "</table>"
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

  function print_chart_as_table() {
    var=$(declare -p "$1")
    eval "declare -A array_to_print="${var#*=}
    table_name="$2"
    maxelements=$3

    echo "<h4>$table_name</h4>"
    echo "<table style="width:400px">"
    for ((row=0;row<$maxelements;row++)); do
      echo -n "<tr>"
      for ((col=0;col<3;col++)); do
        echo -n "<td"
        if [[ "$col" -eq 2 ]]; then
          echo -n " style=\"background-color:${array_to_print[2,$row]};\">"
        else   
          echo -n ">${array_to_print[$col,$row]}"
        fi
        if [[ "$col" -eq 1 ]]; then
          echo -n "%"
        fi
        echo -n "</td>"
      done
      echo "</tr>"
    done
    echo "</table>"
  }

### MAIN ############################################################################

    asb_chart_to_array $file1
    print_chart_as_table "asb_chart_data1" "Ancient Sample Breakdown Chart Data" $asb_chart_counter

    dd_chart_to_array $file1
    print_chart_as_table "dd_chart_data1" "Deep Dive Chart Data" $dd_chart_counter

    samplematch_to_array $file1
    print_array_as_table "sample_match_data1" "Sample Maches" "No;Name;Y-DNA;mtDNA;Age;GD;Arch ID" "sm_no;sm_name;sm_ydna;sm_mtdna;sm_age;sm_gd;sm_aid" $sm_counter

