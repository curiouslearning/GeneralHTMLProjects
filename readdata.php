<?php
var_dump($_POST);
/*
?>
<div class="alert alert-success fade in" id="success_alert">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <strong>Success!</strong> Your form has been sent successfully.
</div>
<?php
*/

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
/*
    $names = array("id", "birth_date", "testing_date", "scoring_date", "receptive_vocab", "letter_id",
                   "decodable_sight_words", "rhyming", "blending", "nonword_repition", "comments");

    for ($i = 0; $i < $rows; $i++) {
        // insert data into table
        $data = array($idArray[$i], $rhymingScoreArray[$i], $blendingScoreArray[$i], $nonwordRepScoreArray[$i],
                     $recepVocabScoreArray[$i], $letterIDScoreArray[$i], $decSightWordScoreArray[$i],
                     $birthdayArray[$i], $testingDateArray[$i], $scoringDateArray[$i], $commentArray[$i]);
        insertAssessmentData($data, $names);
    }
*/
}

/*
// values
// TODO: make this take types into account
function sql_values($vals) {
    $str = "(";
    $i = 0;
    $count = array_count_values($vals);
    for ($names as $n) {
        // form the $n
        if (isDate($n)) {
            // put it into the correct date format
            $newDate = date("Y-d-m", strtotime($n));
        } else if (is_string())
            // put escaped quotes around it
        }

        $fields = $fields . $n;
        if ( $i != $count - 1) {
            $fields = $fields . ", ";
        }
        $i = $i + 1;
    }
    $fields = $fields . ")";
    return $fields;
}



// inserts the given data into the assessment_data table under the given field names
function insertAssessmentData($data, $names) {
    // form the first part of the query
    $names_str = array_reduce($names, "concat");
    $sql = "INSERT INTO assessment_data " . $names_str;


    for ($names as $n) {
        $sql = $sql + $n;
    }

}
*/

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
    if ($str == null) {
        return true;
    }
    elseif (validateDate($str, 'm/d/Y') or validateDate($str, 'Y-d-m')) {
        return true;
    }
    else {
        return false;
    }
}

// TODO: acknowledge taken from online
function validateDate($date, $format = 'm-d-Y') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}


main();

?>


