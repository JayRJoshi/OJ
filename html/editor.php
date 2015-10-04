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
	<title>Bootstrap Case</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="Plugins/CodeMirror/lib/codemirror.js"></script>
	<link rel="stylesheet" href="Plugins/CodeMirror/lib/codemirror.css">
	<link rel="stylesheet" href="Plugins/CodeMirror/lib/codemirror.css">
	<link rel="stylesheet" href="Plugins/CodeMirror/addon/hint/show-hint.css">
	<script src="Plugins/CodeMirror/addon/edit/matchbrackets.js"></script>
	<script src="Plugins/CodeMirror/addon/search/match-highlighter.js"></script>
	<script src="Plugins/CodeMirror/addon/hint/show-hint.js"></script>
	<script src="Plugins/CodeMirror/addon/hint/anyword-hint.js"></script>
	<script src="Plugins/CodeMirror/addon/selection/active-line.js"></script>
	<script src="Plugins/CodeMirror/mode/clike/clike.js"></script>
	<script src="Plugins/CodeMirror/mode/javascript/javascript.js"></script>
	<script src="editor_lang.js"></script>
		
	</script>
	<style>
		.badge-notify{
			top: 10px;
			left: -35px;
		}
		#problem_area {
    			float: left;
   		 	width: 75%;
   			padding: 10px;
			margin-right: 10px;
			display: block;
			overflow: hidden;
   		}
		#recent_submissions{
			float: left;
			width: 22%;
			display: block;
			overflow: auto;
		}
		table{
			table-layout:fixed; 
		}
		td{
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
		}
		hr.dashed-dark{
			display: block;
    			height: 1px;
    			border: 0;
    			border-top: 1px dashed #aaaaaa;
    			padding: 0;
    			width: 100%;
		}
		hr.dashed-light{
			display: block;
    			height: 1px;
    			border: 0;
    			border-top: 1px dashed #eeeeee;
    			padding: 0;
    			width: 100%;
		}
		

		.CodeMirror{
			border-top: 2px solid #eeeeee; border-bottom: 2px solid #eeeeee;
			border-right: 1px solid #eeeeee;
		}
	</style>
</head>
<body>
<?php include('onlinejudge_header.html') ?>
	
<?php

/*
	FIXME: Invalid prob_id should result in "Page Not Found"
	Query to find Problem definition, test_in, test_out.
	TODO: Problem definition is given in HTML file. Add support for simple text, PDF files.	
*/

$prob_def_query = "SELECT Problem_Name, problem_definition, langs, test_in, test_out FROM problem_table where Problem_Id=".urldecode($_GET["prob_id"]);

$prob_def = $conn->query($prob_def_query)->fetch_assoc();
$langs=$prob_def["langs"];

?>
<div class="container">
	<div id="problem_area">
	<?php 
	echo "<h1> ".$prob_def["Problem_Name"]."</h1>";
	/*TODO: Adding whether it is attempted by logged-in user, accuracy.*/
	 
	?>
	<ul class="nav nav-tabs" id="problem_content">
		<li class="active"><a href="#">Problem</a></li>
		<li><a href="my_submissions.php">My Submissions</a></li>
		<li><a href="statistics.php">Statistics</a></li>
	</ul>
	<br>
	<br>
<?php
echo $prob_def["problem_definition"];
?>	
<hr class="dashed-dark">
<p><b>stdin:</b></p>
<?php
echo $prob_def["test_in"];
?>
<hr class="dashed-light">
<p><b>stdout:</p></b>
<?php
echo $prob_def["test_out"];
?>
<hr>

<textarea id="codearea" name="codearea" rows="20" cols="50" value="dsjdsjdsdjsdjddjsdkdjdsd" form="code_upload">
</textarea>
<script>
	
	CodeMirror.commands.autocomplete = function(cm) {
        	cm.showHint({hint: CodeMirror.hint.anyword});
     	}
	var editor = CodeMirror.fromTextArea(document.getElementById("codearea"), {	
	matchBrackets: true,	
	lineNumbers: true,
	highlightMatches: true,
	styleActiveLine: true,
	extraKeys: {"Ctrl-Space": "autocomplete"},
	indentUnit: 4,
	indentWithTabs: true,
        mode: "text/x-csrc"
        });
	editor.getDoc().setValue(c_text);
	editor.on('change',function(cm){
  // get value right from instance
  	codearea.value = cm.getValue();
});
</script>
	<br>

	<!-- Language Selection DropUp 
	TODO:Default value in hidden input, language is set to C, change it depends on langs from db.			
	-->
	
	<div class="dropup">
	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><div id="lang_selected" style="float:left;margin-right:5px">c</div>
  	<span class="caret"></span></button>
  	<ul class="dropdown-menu">
	<?php
				$lang_arr = json_decode($langs);
				foreach ( $lang_arr as $lang ) {
					echo "<li><a onclick=\"lang_select(this,'lang_selected','lang_id')\" style=\"cursor:pointer\">".$lang."</a></li>";
				}
	?> 	
	</ul>	
	</div>
	<br>
	
	<form action="code_upload.php" id="code_upload" method="post" enctype="multipart/form-data">
	<input type="hidden" name="prob_id"
      value="<?php echo htmlspecialchars($_GET['prob_id'], ENT_QUOTES); ?>" />
	<input type="hidden" name="lang_id" id="lang_id" value="c"/>
    	<input type="file" name="user_file" id="user_file">
	<script>
      		window.onload = file_to_editor('user_file');
    	</script>
    <input type="submit" value="submit" name="submit">
</form>
	
</div>

<div id="recent_submissions">
	<h4>Latest Submissions</h4>
	<hr>
	<table class="table" style="table-layout:fixed;overflow:hidden;white-space: nowrap;">
	<thead>
      <tr>
        <th>User</th>
        <th>Status</th>
        <th>Language</th>
	</tr>
    </thead>
	<tbody>
	<?php
	$submitted_solutions_query = "SELECT name,running_time,result,language FROM user_profile ,submitted_codes where Problem_Id=3 AND user_profile.user_id = submitted_codes.user_id ORDER BY submission_time DESC LIMIT 10";
	$submitted_solutions = $conn->query($submitted_solutions_query);
	if($submitted_solutions->num_rows > 0){
			while($row =  $submitted_solutions->fetch_assoc()){
				echo "<tr>
			<td> ".$row["name"]." </td>
			<td> <span title=".$row["running_time"].">".$row["result"]."</span> </td>
			<td> ".$row["language"]." </td>
			</tr>";
			}
	}
		echo "<tr>
			<td> Jadsadjsajddsjdfjy </td>
			<td> AC </td>
			<td> Java </td>
				</tr>
		";
	?>
	</tbody>
	</table>
</div>
</div>
	</div>

</body>
</html>
