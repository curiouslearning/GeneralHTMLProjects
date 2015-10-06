<?php include_once("includes/topwrapper.php");
error_reporting(E_ALL ^ E_DEPRECATED);
?>
<link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<script src="http://cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<!--<div class="pull-right">-->
<!--    <form action="createdeployment.php"><button class="btn btn-success" >Create New Deployment</button></form>-->
<!--</div>-->
<!--<br />-->
<!--<br />-->

<!--    <head>-->
<!--        <meta charset="UTF-8">-->
<!--        <title>AdminLTE | Data Tables</title>-->
<!--        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>-->
<!---->
<!--        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />-->
<!---->
<!--        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />-->
<!---->
<!--        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />-->
<!---->
<!--        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />-->
<!---->
<!--    </head>-->


<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Tablets</h3>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <?php
                            include_once("includes/util.php");
                            $util = new Util();
                            $activeColumnIndex = -1;
                            $piColumnIndex = -1;
                            $counter = 0;
                            $serialIdIndex = -1;
                            $deploymentArray = $util->getTabletInformation();
                            $firstArray = $deploymentArray[0];
                            while(current($firstArray) != null)
                            {
                                if(strtolower(trim(key($firstArray))) == "is active")
                                    $activeColumnIndex = $counter;
                                elseif(strtolower(trim(key($firstArray))) == "using pi")
                                    $piColumnIndex = $counter;
                                elseif(strtolower(trim(key($firstArray))) == "serial id")
                                    $serialIdIndex = $counter;
                                $counter++;
                                echo("<th>".key($firstArray)."</th>");
                                next($firstArray);
                            }
                            echo('<th>Command</th>');
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $isfirst = true;
                        $counter = 0;
                        $dropdown = false;
                        $serialId = "";
                        foreach($deploymentArray as $deployment)
                        {
                            echo("<tr>");
                            foreach($deployment as $dataValue)
                            {
                                if($counter === $activeColumnIndex)
                                {
                                    if($dataValue == 1)
                                        echo("<td>Yes</td>");
                                    else
                                        echo("<td>No</td>");
                                }
                                elseif($counter === $piColumnIndex)
                                {
                                    if($dataValue == 1)
                                        echo("<td>Yes</td>");
                                    else
                                        echo("<td>No</td>");
                                }
                                else
                                    echo("<td>$dataValue</td>");
                                if($counter === $serialIdIndex)
                                    $serialId = trim($dataValue);
                                $counter++;
                            }
                            echo("<td><a href=\"commands.php?serialId=$serialId&scope=single\"> <button class=\"btn btn-default\">Issue</button></a></td>");
                            $counter = 0;
                            echo("</tr>");
                        }
                        ?>

                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>

</section><!-- /.content -->

<!-- jQuery 2.0.2 -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<!-- DATA TABES SCRIPT -->
<script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="js/AdminLTE/app.js" type="text/javascript"></script>
<!-- AdminLTE for demo purposes -->
<script src="js/AdminLTE/demo.js" type="text/javascript"></script>
<!-- page script -->
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $('#example2').dataTable({
            "bPaginate": true,
            "bLengthChange": false,
            "bFilter": false,
            "bSort": true,
            "bInfo": true,
            "bAutoWidth": false
        });
    });
</script>