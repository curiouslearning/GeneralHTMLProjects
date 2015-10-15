<?php
require_once('transporter.php');

// TODO: should put this into a function

?>
<div class="alert alert-success fade in" id="success_alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <strong>Success!</strong> Your form has been sent successfully.
</div>
<?php


// names of fields in input form
define('TABLE_NAME', 'foreign_site_child_analysis');
define('STUDENT_ID_NAME', 'student_id');
define('ASSESSMENT_PHASE_NAME', 'assessment_phase');
define('AGE_NAME', 'age');
define('GENDER_NAME', 'gender');
define('TESTING_DATE_NAME', 'testing_date');
define('RECEPTIVE_VOCAB_NAME', 'receptive_vocab');
define('LETTER_ID_ALPHABETICAL_NAME', 'letter_id_alpha');
define('LETTER_ID_RANDOM_NAME', 'letter_id_rand');
define('SOUND_LETTER_ID_NAME', 'sound_letter_id');
define('DECODABLE_SIGHT_WORDS_NAME', 'dec_sight_words');
define('RHYMING_NAME', 'rhyming');
define('BLENDING_NAME', 'blending');
define('NONWORD_REPETITION_NAME', 'nonword_rep');
define('COMMENTS_NAME', 'comments');
define('ROWS_NAME', 'table_rows');

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

function main() {
    $rows = 1; //$_POST[ROWS_NAME];

    // collect student ids
    $studentIDArray = collectInputs($rows, STUDENT_ID_NAME, null);
    $studentIDArray = validate("is_numeric", $studentIDArray);

    // TODO: check if this is the correct validataion
    // collect assessment_phases
    $assessmentPhaseArray = collectInputs($rows, ASSESSMENT_PHASE_NAME, null);
    $assessmentPhaseArray = validate("is_string", $assessmentPhaseArray);

    // collect ages
    $ageArray = collectInputs($rows, AGE_NAME, null);
    $ageArray = validate("is_numeric", $ageArray);

    // collect genders
    $genderArray = collectInputs($rows, GENDER_NAME, null);
    $genderArray = validate("is_string", $genderArray);

    // receptive vocab score
    $recepVocabScoreArray = collectInputs($rows, RECEPTIVE_VOCAB_NAME, 0);
    $recepVocabScoreArray = validate("is_numeric", $recepVocabScoreArray);

    // letter name id alphabetical score
    $alphaLetterIDArray = collectInputs($rows, LETTER_ID_ALPHABETICAL_NAME, 0);
    $alphaLetterIDArray = validate("is_numeric", $alphaLetterIDArray);

    // letter name id random score
    $randomLetterIDArray = collectInputs($rows, LETTER_ID_RANDOM_NAME, 0);
    $randomLetterIDArray = validate("is_numeric", $randomLetterIDArray);

    // sound letter id
    $soundLetterIDArray = collectInputs($rows, SOUND_LETTER_ID_NAME, 0);
    $soundLetterIDArray = validate("is_numeric", $soundLetterIDArray);

    // dec sight word score
    $decSightWordScoreArray = collectInputs($rows, DECODABLE_SIGHT_WORDS_NAME, 0);
    $decSightWordScoreArray = validate("is_numeric", $decSightWordScoreArray);

    // rhymiung score
    $rhymingScoreArray = collectInputs($rows, RHYMING_NAME, 0);
    $rhymingScoreArray = validate("is_numeric", $rhymingScoreArray);

    // blending scores
    $blendingScoreArray = collectInputs($rows, BLENDING_NAME, 0);
    $blendingScoreArray = validate("is_numeric", $blendingScoreArray);

    // nonwordRep scores
    $nonwordRepScoreArray = collectInputs($rows, NONWORD_REPETITION_NAME, 0);
    $nonwordRepScoreArray = validate("is_numeric", $nonwordRepScoreArray);

    // testing dates
    $testingDateArray = collectInputs($rows, TESTING_DATE_NAME, null);
    $testingDateArray = validate("isDate", $testingDateArray);

    $totalScoreArray = array();
    $recepVocabScoreArray, $alphaLetterIDArray, $randomLetterIDArray, $soundLetterIDArray,
                    $decSightWordScoreArray, $rhymingScoreArray, $blendingScoreArray, $nonwordRepScoreArray);
    for ($i = 0; $i < $rows; $i++) {
        $total = 0;
        foreach ($)

        $totalScoreArray[$i] = $recepVocabScoreArray[$i] + $alphaLetterIDArray[$i] +
                               $randomLetterIDArray[$i] + $soundLetterIDArray[$i] + $decSightWordScoreArray[$i] + $rhymingScoreArray[$i] + $blendingScoreArray[$i] + $nonwordRepScoreArray[$i];
    }

    // comments dont need to be validated
    $commentArray = collectInputs($rows, COMMENTS_NAME);

    // TODO: fix last two columns and put these field names in config
    // dict with field names in table and data rows for fields
    $assessmentData = array(
        "student_id" => $studentIDArray,
        "assessment_phase" => $assessmentPhaseArray,
        "age" => $ageArray,
        "testing_date" => $testingDateArray,
        "gender" => $genderArray,
        "receptive_vocabulary" => $recepVocabScoreArray,
        "letter_name_identification_in_alphabetical_order" => $alphaLetterIDArray,
        "letter_name_identification_in_random_order" => $randomLetterIDArray,
        "sound_letter_identification" => $soundLetterIDArray,
        "decodeable_words_and_sight_words" => $decSightWordScoreArray,
        "phonological_awareness_rhyming" => $rhymingScoreArray,
        "phonological_awareness_blending" => $blendingScoreArray,
        "phonological_awareness_non_word_repetition" => $nonwordRepScoreArray,
        "comments" => $commentArray,
        //"total_score" => , TODO: put these in
        //"percentage" => ,
    );

    insertAssessmentData($assessmentData, $rows);
    checkInsertionSuccess($assessmentData, $rows);
}


// checks that all of our assessmentData was inserted into mysql
function checkInsertionSuccess($data, $rows) {
    $success = true;
    $count = count($data);
    $transporter = new Transporter();
    $db = $transporter->dbConnectPdo();

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


// purp: inserts the given data into the assessment_data table under the given field names
function insertAssessmentData($field_data_dict, $numRows) {
    $transporter = new Transporter();
    $db = $transporter->dbConnectPdo();

    $fieldStr = queryStr(array_keys($field_data_dict));

    // go through each row in the table
    for ($i = 0; $i < $numRows; $i++) {
        $values = array();
        // collect all values from that row
        foreach ($field_data_dict as $field => $dataArray){
            array_push($values, $dataArray[$i]);
        }
        $valsStr = queryStr($values, "format_value");
        // form the first part of the query
        $sql = "INSERT INTO " . TABLE_NAME . " " . $fieldStr . " VALUES " . $valsStr . ";";
        $statement = $db->prepare($sql);
        var_dump($sql);
        $result = $statement->execute();
        var_dump($result);
        # TODO: print some sort of error message saying it wasn't inserted
        if ($result == false) {
            print($db->errorInfo());
        }
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
        $formattedStr = date("Y-m-d", strtotime($value));
        $formattedStr = '\'' . $formattedStr . '\'';
    }
    else if (is_numeric($value) or is_string($value)) {
        $formattedStr = $value;
    }
    else { // TODO: make something better here
        echo "WE HAVE A BIG PROBLEM";
        echo "Unknown value $value";
    }
    return $formattedStr;
}


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


// purp: returns an array containing the input fields from the given number
// of rows in the table belonging to the field with the given field name
// args: numer of rows to take input from,
function collectInputs($numRows, $fieldName, $default) {
    $inputArray = array();
    for ($i = 0; $i < $numRows; $i++) {
        if(isset($_POST["$fieldName$i"]) and $_POST["$fieldName$i"] != "") {
            // input is set so add it to the array
            array_push($inputArray, $_POST["$fieldName$i"]);
        }
        else {
            array_push($inputArray, $default);
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


function validateDate($date, $format = 'm-d-Y') {
    $d = DateTime::createFromFormat($format, $date);
    return $d;
}


main();

?>


