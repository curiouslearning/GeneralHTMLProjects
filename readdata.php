<?php
require_once('transporter.php');
// TODO: should put this into a function

?>
<div class="alert alert-success fade in" id="success_alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <strong>Success!</strong> Your form has been sent successfully.
</div>
<?php

define('TABLE_NAME', "foreign_site_child_analysis");
define('ID_NAME', "id");
define('BIRTH_DATE_NAME', 'birth_date');
define('TESTING_DATE_NAME', 'testing_date');
define('SCORING_DATE_NAME', 'scoring_date');
define('RECEPTIVE_VOCAB_NAME', 'receptive_vocab');
define('LETTER_ID_NAME', 'letter_id');
define('DEC_SIGHT_WORDS_NAME', 'dec_sight_words');
define('RHYMING_NAME', 'rhyming');
define('BLENDING_NAME', 'blending');
define('NONWORD_REP_NAME', 'nonword_rep');
define('COMMENTS_NAME', 'comments');
define('ROWS_NAME', 'table_rows');

ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);

function main() {
    $rows = $_POST[ROWS_NAME];

    // collect ids
    $idArray = collectInputs($rows, ID_NAME);
    $idArray = validate("is_numeric", $idArray);

    // rhymiung score
    $rhymingScoreArray = collectInputs($rows, RHYMING_NAME);
    //echo(print_r($rhymingScoreArray, true));
    $rhymingScoreArray = validate("is_numeric", $rhymingScoreArray);

    // blending scores
    $blendingScoreArray = collectInputs($rows, BLENDING_NAME);
    $blendingScoreArray = validate("is_numeric", $blendingScoreArray);

    // nonwordRep scores
    $nonwordRepScoreArray = collectInputs($rows, NONWORD_REP_NAME);
    $nonwordRepScoreArray = validate("is_numeric", $nonwordRepScoreArray);

    // receptive vocab score
    $recepVocabScoreArray = collectInputs($rows, RECEPTIVE_VOCAB_NAME);
    $recepVocabScoreArray = validate("is_numeric", $recepVocabScoreArray);

    // letter id score
    $letterIDScoreArray = collectInputs($rows, LETTER_ID_NAME);
    $letterIDScoreArray = validate("is_numeric", $letterIDScoreArray);

    // dec sight word score
    $decSightWordScoreArray = collectInputs($rows, DEC_SIGHT_WORDS_NAME);
    $decSightWordScoreArray = validate("is_numeric", $decSightWordScoreArray);

    // collect birthdays
    $birthdayArray = collectInputs($rows, BIRTH_DATE_NAME);
    $birthdayArray = validate("isDate", $birthdayArray);

    // testing dates
    $testingDateArray = collectInputs($rows, TESTING_DATE_NAME);
    $testingDateArray = validate("isDate", $testingDateArray);

    // scoring dates
    $scoringDateArray = collectInputs($rows, SCORING_DATE_NAME);
    $scoringDateArray = validate("isDate", $scoringDateArray);

    // comments dont need to be validated
    $commentArray = collectInputs($rows, COMMENTS_NAME);

    // TODO: the names given in the table they have don't match the ones on my form
    // dict with field names in table and data rows for fields
    $assessmentData = array(
        "id" => $idArray,
        "phonological_awareness_rhyming" => $rhymingScoreArray,
        "phonological_awareness_blending" => $blendingScoreArray,
        "phonological_awareness_non_word_repetition" => $nonwordRepScoreArray,
        "receptive_vocabulary" => $recepVocabScoreArray,
        "letter_name_identification_in_alphabetical_order" => $letterIDScoreArray, // TODO: is this alphabetical or non alphabetical order?
        "decodeable_words_and_sight_words" => $decSightWordScoreArray
        //"birth_date" => $birthdayArray, TODO: see if this should be age varchar
        //"testing_date" => $testingDateArray, TODO: see if this should be in there
        //"scoring_date" => $scoringDateArray,TODO: see if this should be in there
        //"comments" => $commentArray TODO: see if this should be in there
    );

    insertAssessmentData($assessmentData, $rows);
    checkInsertionSuccess($assessmentData, $rows);
}


// checks that all of our assessmentData was inserted into mysql
function checkInsertionSuccess($data, $rows) {
    $success = true;
    $db = new PDO('mysql:dbname=tablet_data;host=localhost;port=8889', 'root', 'root');
    $count = count($data);

    for ($i = 0; $i < $rows; $i++) {
        $sql = 'SELECT * from foreign_site_child_analysis WHERE ';
        $j = 0;
        foreach ($data as $fieldName => $dataArray) {
            $value = $dataArray[$i];
            $sql = $sql . $fieldName ;
            if ($value != null) {
                $sql = $sql . '=' . format_value($value);
            }
            else {
                $sql = $sql . " IS NULL";
            }
            if ($j < $count - 1 ) {
                $sql = $sql . " AND ";
            }
            $j = $j + 1;
        }
        // TODO: MAKE THIS HOW THEY WANT
        $sql = $sql . ';';
        $statement = $db->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if ($row == false) {
            $success = false;
        }
    }
    $db = null;
    return $success;
}



// TODO: include table name somehow
// inserts the given data into the assessment_data table under the given field names
function insertAssessmentData($field_data_dict, $numRows) {
    $transporter = new Transporter();
    // get the names of the fields we are inserting into

    $fieldStr = queryStr(array_keys($field_data_dict));
    // go through each row in the table

    var_dump($numRows);
    for ($i = 0; $i < $numRows; $i++) {
        $values = array();
        // collect all values from that row
        foreach ($field_data_dict as $field => $dataArray){
            array_push($values, $dataArray[$i]);
        }
        $valsStr = queryStr($values, "format_value");
        // form the first part of the query
        $sql = "INSERT INTO " . TABLE_NAME . " " . $fieldStr . " VALUES " . $valsStr . ";";
        var_dump($sql);
        $result = $transporter->sendQuery($sql); // TODO: don't capture this if you don't need it
    }
    // make space
    $transporter = null;
}


function format_value($value) {
    $formattedStr = null;
    if ($value == null) {
        $formattedStr = "null";
    }
    elseif (isDate($value) == true) {
        // put it into the correct date format
        $formattedStr = date("Y-d-m", strtotime($value));
    }
    else if (is_numeric($value)) {
        $formattedStr = $value;
    }
    else if (is_string($value)) {
        $formattedStr = '\'' . $value . '\'';
    }
    else {
        echo "WE HAVE A BIG PROBLEM";
        echo "Unknown value $value";
    }
    return $formattedStr;
}


// values
// TODO: make this take types into account
function queryStr($elements, callable $apply = null) {
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


