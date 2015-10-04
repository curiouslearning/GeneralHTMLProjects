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
    $rows = 1;//$_POST["$rowsName"];

    // collect ids
    $idArray = collectInputs($rows, $idName);
    validate("is_numeric", $idArray);

    // rhymiung score
    $rhymingScoreArray = collectInputs($rows, $rhymingName);
    //echo(print_r($rhymingScoreArray, true));
    validate("is_numeric", $rhymingScoreArray);

    // blending scores
    $blendingScoreArray = collectInputs($rows, $blendingName);
    validate("is_numeric", $blendingScoreArray);

    // nonwordRep scores
    $nonwordRepScoreArray = collectInputs($rows, $nonwordRepName);
    validate("is_numeric", $nonwordRepScoreArray);

    // receptive vocab score
    $recepVocabScoreArray = collectInputs($rows, $receptiveVocabName);
    validate("is_numeric", $recepVocabScoreArray);

    // letter id score
    $letterIDScoreArray = collectInputs($rows, $letterIDName);
    validate("is_numeric", $letterIDScoreArray);

    // dec sight word score
    $decSightWordScoreArray = collectInputs($rows, $decSightWordsName);
    validate("is_numeric", $decSightWordScoreArray);

    // collect birthdays
    $birthdayArray = collectInputs($rows, $birthDateName);
    validate("isDate", $birthdayArray);

    // testing dates
    $testingDateArray = collectInputs($rows, $testingDateName);
    validate("isDate", $testingDateArray);

    // scoring dates
    $scoringDateArray = collectInputs($rows, $scoringDateName);
    validate("isDate", $scoringDateArray);

    // comments dont need to be validated TODO: prevent SQL injection
    $commentArray = collectInputs($rows, $commentsName);

    // TODO: make this new function

    $connection = mysqli_connect("localhost","curious_learning","readingisgood","assessments");
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $names = array("id", "birth_date", "testing_date", "scoring_date", "receptive_vocab", "letter_id",
                   "decodable_sight_words", "rhyming", "blending", "nonword_repetition", "comments");
    $fields = fields_query_str($names, $connection);

    for ($i = 0; $i < $rows; $i++) {
        // insert data into table
        $data = array($idArray[$i], $rhymingScoreArray[$i], $blendingScoreArray[$i], $nonwordRepScoreArray[$i],
                     $recepVocabScoreArray[$i], $letterIDScoreArray[$i], $decSightWordScoreArray[$i],
                     $birthdayArray[$i], $testingDateArray[$i], $scoringDateArray[$i], $commentArray[$i]);
        $vals_str = vals_query_str($data);
        insertAssessmentData($vals_str, $fields, $connection);
        break;
    }

}


// values
// TODO: make this take types into account
function vals_query_str($vals, $con) {
    $str = "(";
    $i = 0;
    $count = count($vals);

    foreach ($vals as $v) {
        // form the $n
        if ($v == null) {
            $str = $str . "null";
        }
        elseif (isDate($v) == true) {
            // put it into the correct date format
            $date = date("Y-d-m", strtotime($v));
            $str = $str . $date;
        }
        else if (is_numeric($v)) {
            $str = $str . $v;
        }
        else if (is_string($v)) {
            //$str = $str . mysqli_real_escape_string($v, $con);
            $str = $str . '\'' . $v . '\'';
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

function fields_query_str($fields) {
    $str = "(";
    $i = 0;
    $count = count($fields);
    foreach ($fields as $f) {
        $str = $str . $f;
        // put a comma after every value but the last
        if ( $i < $count - 1) {
            $str = $str . ", ";
        }
        $i = $i + 1;
    }
    $str = $str . ")";
    return $str;

}

// inserts the given data into the assessment_data table under the given field names
function insertAssessmentData($values, $fields, $con) {
    // form the first part of the query
    $sql = "INSERT INTO assessment_data " . $fields . " VALUES " . $values . ";";
    $result = mysqli_query($con, $sql);
    var_dump($sql);
    var_dump($result);
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


