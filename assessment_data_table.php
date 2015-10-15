<?php
include_once("assessment_table.util.php");
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <title>Curious Learning assesment info display table</title>
</head>
<body>
    <div class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Assessment Info</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table id="assessment_data" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <?php
                                    // get all the assessments
                                    $assessmentArray = getAssessmentInfo();
                                    $headerNames = array_keys($assessmentArray[0]);

                                    // create all of the headers using column names
                                    foreach($headerNames as $header) {
                                        // create the header
                                        echo("<th>".$header."</th>");
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // loop through all of the assessments and give them rows
                                    foreach($assessmentArray as $assessment) {
                                        echo("<tr>");
                                        // output cells will data
                                        foreach($assessment as $value) {
                                            echo("<td>" . $value . "</td>");
                                        }
                                        echo("</tr>");
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#assessment_data').DataTable( {
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            } );
        } );
    </script>
</body>
</html>