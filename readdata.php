<?php

?>
<div class="alert alert-success fade in" id="success_alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <strong>Success!</strong> Your form has been sent successfully.
</div>
<?php


function main() {
    $idName = "id";
    $birthDateName = "birth_date";
    $testingDateName = "testing_date";
    $scoringDateName = "scoring_date";
    $receptiveVocabName = "receptive_vocab";
    $letterIDName = "letter_id";
    $decSightWordsName = "dec_sight_words";
    $rhymingName = "rhyming";
    $blendingName = "blending";
    $nonwordRepName = "nonword_rep";
    $commentsName = "comments";
    $rowsName = "table_rows";
    $rows = $_POST["$rowsName"];

    // collect ids
    $idArray = collectInputs($rows, $idName);
    $idArray = validate("is_numeric", $idArray);

    // rhymiung score
    $rhymingScoreArray = collectInputs($rows, $rhymingName);
    //echo(print_r($rhymingScoreArray, true));
    $rhymingScoreArray = validate("is_numeric", $rhymingScoreArray);

    // blending scores
    $blendingScoreArray = collectInputs($rows, $blendingName);
    $blendingScoreArray = validate("is_numeric", $blendingScoreArray);

    // nonwordRep scores
    $nonwordRepScoreArray = collectInputs($rows, $nonwordRepName);
    $nonwordRepScoreArray = validate("is_numeric", $nonwordRepScoreArray);

    // receptive vocab score
    $recepVocabScoreArray = collectInputs($rows, $receptiveVocabName);
    $recepVocabScoreArray = validate("is_numeric", $recepVocabScoreArray);

    // letter id score
    $letterIDScoreArray = collectInputs($rows, $letterIDName);
    $letterIDScoreArray = validate("is_numeric", $letterIDScoreArray);

    // dec sight word score
    $decSightWordScoreArray = collectInputs($rows, $decSightWordsName);
    $decSightWordScoreArray = validate("is_numeric", $decSightWordScoreArray);

    // collect birthdays
    $birthdayArray = collectInputs($rows, $birthDateName);
    $birthdayArray = validate("isDate", $birthdayArray);

    // testing dates
    $testingDateArray = collectInputs($rows, $testingDateName);
    $testingDateArray = validate("isDate", $testingDateArray);

    // scoring dates
    $scoringDateArray = collectInputs($rows, $scoringDateName);
    $scoringDateArray = validate("isDate", $scoringDateArray);

    // comments dont need to be validated TODO: prevent SQL injection
    $commentArray = collectInputs($rows, $commentsName);

    $assessment_data = array(
        "id" => $idArray,
        "rhyming" => $rhymingScoreArray,
        "blending" => $blendingScoreArray,
        "nonword_repetition" => $nonwordRepScoreArray,
        "receptive_vocab" => $recepVocabScoreArray,
        "letter_id" => $letterIDScoreArray,
        "decodable_sight_words" => $decSightWordScoreArray,
        "birth_date" => $birthdayArray,
        "testing_date" => $testingDateArray,
        "scoring_date" => $scoringDateArray,
        "comments" => $commentArray
    );

    insertAssessmentData($assessment_data, $rows);
}

// inserts the given data into the assessment_data table under the given field names
function insertAssessmentData($field_data_dict, $numRows) {
    // make mysql connection
    $con = sqlConnection();
    if ($con == null) {
        return false;
    }

    // get the names of the fields we are inserting into
    $field_str = query_str(array_keys($field_data_dict));

    // go through each row in the table
    for ($i = 0; $i < $numRows; $i++) {
        $values = array();
        // collect all values from that row
        foreach ($field_data_dict as $field => $dataArray){
            array_push($values, $dataArray[$i]);
        }
        $vals_str = query_str($values, "format_value");
        // form the first part of the query
        $sql = "INSERT INTO assessment_data " . $field_str . " VALUES " . $vals_str . ";";
        $result = mysqli_query($con, $sql);
        var_dump($result);
    }
}


function sqlConnection() {
    $connection = mysqli_connect("localhost", "curious_learning", "readingisgood", "assessments");
    // Check connection
    if (mysqli_connect_errno()) {
        $connection = null;
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    return $connection;
}

function format_value($value) {
    $formatted_str = null;
    if ($value == null) {
        $formatted_str = "null";
    }
    elseif (isDate($value) == true) {
        // put it into the correct date format
        $formatted_str = date("Y-d-m", strtotime($value));
    }
    else if (is_numeric($value)) {
        $formatted_str = $value;
    }
    else if (is_string($value)) {
        $formatted_str = '\'' . $value . '\'';
    }
    else {
        echo "WE HAVE A BIG PROBLEM";
        echo "Unknown value $value";
    }
    return $formatted_str;
}


// values
// TODO: make this take types into account
function query_str($elements, callable $apply = null) {
    $str = "(";
    $i = 0;
    $count = count($elements);

    foreach ($elements as $elem) {
        if ($apply != null) {
            $str = $str . $apply($elem);
        }
        else {
            $str = $str . $elem;
        }
        // put a comma after every value but the last
        if ( $i < $count - 1) {
            $str = $str . ", ";
        }
        $i = $i + 1;
    }
    $str = $str . ")";
    return $str;
}


// Todo: include post in purpose statement
// purp: returns an array containing the input fields from the given number
// of rows in the table belonging to the field with the given field name
// args: numer of rows to take input from,
function collectInputs($numRows, $fieldName) {
    $inputArray = array();
    for ($i = 0; $i < $numRows; $i++) {
        if(isset($_POST["$fieldName$i"]) and $_POST["$fieldName$i"] != "") {
            // input is set so add it to the array
            array_push($inputArray, $_POST["$fieldName$i"]);
        }
        else {
            array_push($inputArray, null);
        }
    }
    return $inputArray;
}

// purp: returns an array where all invalid elements in the given array are null
function validate(callable $isValid, $array) {
    $validArray = array();
    foreach ($array as $elem) {
        if ($isValid($elem) == true or $elem == null) {
            array_push($validArray, $elem);
            //echo("$elem is not valid");
        }
        else {
            array_push($validArray, null);
            //echo("$elem is valid");
        }
    }
    return $validArray;
}

// TODO: check if this throws an error if it is of the wrong type
// returns true if the the given string describes a valid date in one
// of the following two formats: mm/dd/yyyy  or yyyy-dd-mm
function isDate($str) {
    if (validateDate($str, 'm/d/Y') or validateDate($str, 'Y-d-m')) {
        return true;
    }
    else {
        return false;
    }
}

// TODO: acknowledge taken from online
function validateDate($date, $format = 'm-d-Y') {
    $d = DateTime::createFromFormat($format, $date);
    return $d;
}


main();

?>


