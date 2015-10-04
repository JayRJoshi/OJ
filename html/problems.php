<?php
include('config.php');
session_start();
if (!isset($_SESSION['user_id'])) {
header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Problems</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="Plugins/bootstrap-min.css" rel="stylesheet">	
	<link href="Plugins/bootstrap-tagsinput.css" rel="stylesheet">
    	<script src="Plugins/jquery-old.min.js"></script>
   	<script src="Plugins/bootstrap.min.js"></script>
	<script src="Plugins/bootstrap-tagsinput.min.js"></script>
	<style>
		.badge-notify{
		background:red;
		position:relative;
		top: 10px;
		left: -35px;
		}
	</style>
</head>
<body>
<?php include('onlinejudge_header.html') ?>
	<div class="container">
	<h2> Problems </h2>
	<input type="text" placeholder="search for tags" data-role="tagsinput" >
	<table id="problemTable" class="table table-hover">
    	<thead>
      	<tr>
        <th>#</th>
        <th>Problem Name</th>
        <th>Accuracy</th>
	<th>Tags</th>
	</tr>
    	</thead>
	<tbody>
<?php

/*
Query to list out problems and overall statistics.
Uses just one table problem_table
*/

$problem_user_info_query = "SELECT Problem_Id, Problem_Name, total_user_submissions, total_submissions, correct_submissions, incorrect_submissions FROM problem_table";

$problem_user_info = $conn->query($problem_user_info_query);

if ($problem_user_info->num_rows > 0) {

	$i = 1; // just for index of problem.

   	while($row = $problem_user_info->fetch_assoc()) {
		/*
		Query to list out tags for particular Problem_Id
		*/

		$tag_info = "SELECT tg.Tag FROM tags tg, problem_to_tags pt, problem_table t WHERE tg.Tag_Id = pt.Tag_Id AND pt.Problem_Id = ".$row["Problem_Id"]." AND pt.Problem_Id = t.Problem_Id";

		$tag_result = $conn->query($tag_info);
		$tags = "";

		/*
		Query will give status for particular problem for logged-in user
		*/

		$problem_status_query = "SELECT best_result from user_to_problem where Problem_Id=".$row["Problem_Id"]." AND user_id = '".$_SESSION["user_id"]."'";

		$problem_status = $conn->query($problem_status_query);

		if($tag_result->num_rows > 0){
			while($tag_row =  $tag_result->fetch_assoc()){
				$tags = $tags.$tag_row["Tag"].",";
			}
		}
	
		$tags = substr($tags,0,strlen($tags)-1);
		/*
		0:correct solution, 1:incorrect solution,2:partly incorrect solution, 3:run-time error, 4:compile-time error
		
		TODO: Depending on error code, following table row will have dfferent colors.
		Above list is incomplete
		*/
		if($problem_status->num_rows != 0){
			$status = $problem_status->fetch_assoc();
			if($status["best_result"] == 0){echo "<tr class=\"success\">";}
			else if($status["best_result"] == 2){echo "<tr class=\"info\">";}
			else if($status["best_result"] == 1){echo "<tr class=\"danger\">";}
			else {echo "<tr class=\"warning\">";}
		}

		echo "<td>".$i."</td>
		<td><a href=\"editor.php?prob_id=".urlencode($row["Problem_Id"])."\">".$row["Problem_Name"]."</td>
		<td>".($row["total_submissions"]-$row["incorrect_submissions"])."/".$row["total_submissions"]. "</td>
		<td> ".$tags."</td>
		</tr>";
		$i = $i + 1;
	}
} else {
     echo "0 results";
}

$conn->close();
?>  

    </tbody>
	</table>
</div>

</body>
</html>
