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

    $rows = 1;

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
    validate("is_numeric", $nonwordRepScoreArray);

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
    $comentArray = collectInputs($rows, $commentsName);
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
    }
    return $inputArray;
}

// purp: applies the given validation function to each item in the list
// issuing error messages for each invalid element and returns the total number
// of invalid elements
function validate(callable $isValid, $array) {
    $numInvalid = 0;
    foreach ($array as $elem) {
        if ($isValid($elem) == false) {
            $numInvalid++;
            echo("$elem is not valid");
        }
        else {
            echo("$elem is valid");
        }
    }
}

// returns true if the the given string describes a valid date in one
// of the following two formats: mm/dd/yyyy  or yyyy-dd-mm
function isDate($str) {
    if (validateDate($str, 'm/d/Y') or validateDate($str, 'Y-d-m')) {
        return true;
    } else {
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


