
<?php


var_dump($_POST);
$id = $_POST['id'];

$rhymingName = "rhyming";

$idArray = arrray();

for($i = 0; $i <numberOfRows; i++)
{
	$idArray[$i] = $_POST["id$i"];
}


//TODO for all all vars
if(isset($_POST[$rhymingName]))
	$rhyming = $_POST[$rhymingName];


echo("The id is: $id");


// Validate numbers
if(!is_numeric($rhyming))
{
	//TODO throw error or log
}
else
{
	
	echo("$rhymingName is valid");
}
//Validate dates




?>


