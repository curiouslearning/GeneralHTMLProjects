<?php

require_once('transporter.php');
require_once('assessment_table.config.php');

//  returns an array containing every row in the
function getAssessmentInfo() {
    // create a query to get all rows in the table
    // not sure how they want use to do this
    $assessmentArray = null;
    $transporter = new Transporter();
    $db = $transporter->dbConnectPdo();
    $query = "SELECT * FROM " . TABLE_NAME . ";";
    $statement = $db->prepare($query);
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    if ($rows != false) {
        $assessmentArray = $rows;
    }
    return $assessmentArray;
}
