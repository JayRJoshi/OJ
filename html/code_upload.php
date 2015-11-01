<?php
include('config.php');
include('db_config.php');

session_start();
if (!isset($_SESSION['user_id'])) {
header('Location: index.php');
}

/*Debugginh Purpose */
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$user_id=mysqli_real_escape_string($conn,$_SESSION['user_id']);
$prob_id=mysqli_real_escape_string($conn,$_POST['prob_id']);
$code=mysqli_real_escape_string($conn,$_POST['codearea']);
$unscaled_time_limit=mysqli_real_escape_string($conn,$_POST['time_limit']);
$total_testcases=mysqli_real_escape_string($conn,$_POST['total_testcases']);
$lang_id=$_POST['lang_id'];

//$target_dir = "solutions_from_users/";
//$target_file = $target_dir . basename($_FILES["user_file"]["name"]);

//echo "From codearea: ".$_POST['codearea'];
//$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);


/*$prob_def_query = "INSERT INTO `onlinejudge`.`submitted_codes` VALUES (NULL,".$prob_id.",".$code.",NOW(),".$user_id.",NULL,NULL,".$lang_id.",NULL)";
*/
echo "\n\n";
echo $judgehost."\n";
echo $judgeport."\n";
$lock_query="LOCK TABLE `submitted_codes` WRITE";
$unlock_query="UNLOCK TABLE";
$autoincr_query="SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'onlinejudge' AND TABLE_NAME = 'submitted_codes'";
$code_submission_query="INSERT INTO submitted_codes VALUE  (NULL,".$prob_id.",'".$code."', NOW() , '".$user_id."', NULL , NULL , '".$lang_id."', NULL)";

$testcase_query="SELECT `testcase_no`, `test_in`, `test_out` FROM `testcases` WHERE Problem_Id=".$prob_id." ORDER BY testcase_no";
$language_query="SELECT * FROM Language WHERE lang_id='".$lang_id."'";

$conn->query($lock_query);

$submission_id_query=$conn->query($autoincr_query)->fetch_assoc();
$submission_id=$submission_id_query["AUTO_INCREMENT"];

if (!$submission_id) {
    die('Error:submission_id ' . mysql_error());
}

echo $submission_id."\n";

$code_submission = $conn->query($code_submission_query);
if (!$code_submission) {
    die('Error: code_submission' . mysql_error());
}

$conn->query($unlock_query);

$testcases=$conn->query($testcase_query);
if(!$testcases){
	die('Error: '. mysql_error());
}

$language_info=$conn->query($language_query)->fetch_assoc();
if(!$language_info){
	die('Error :'.mysql_error());
}

$time_factor=$language_info["time_factor"];
$time_limit=$time_factor*$unscaled_time_limit;

$socket = fsockopen($judgehost, $judgeport);
if($socket) {
			fwrite($socket, $submission_id."\n");
			fwrite($socket,"Main.java.\n"); //TODO:CHANGE FILENAME
			$code = str_replace("\n", '$_\\_$', makeValidText($code));
			fwrite($socket, $code."\n");
			fwrite($socket,$time_limit."\n");
			fwrite($socket,$lang_id."\n");
			fwrite($socket,$total_testcases."\n");
			if($testcases->num_rows > 0){
				while($testcase =  $testcases->fetch_assoc()){
					$testcase_no = $testcase["testcase_no"];
					$testcase_in = $testcase["test_in"];
					echo $testcase_in;
					$testcase_out = $testcase["test_out"];
					echo $testcase_out;
					$testcase_in = str_replace("\n", '$_\\_$', makeValidText($testcase_in));
					$testcase_out = str_replace("\n", '$_\\_$', makeValidText($testcase_out));
					fwrite($socket,$testcase_in."\n");
					fwrite($socket,$testcase_out."\n");
					$status=fgets($socket);
					echo $status;
					//$="";
					while(!feof($socket)){
						$code_out=$code_out.fgets($socket);			
					}
					
				}
			}
			//$status = fgets($socket);
			//$contents = "";
			/*while(!feof($socket))
				$contents = $contents.fgets($socket);*/
			//echo $status;
			/*if($status == 0) {
					// oops! compile error
					$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
					mysql_query($query);
					$_SESSION['cerror'] = trim($contents);
					header("Location: solve.php?cerror=1&id=".$_POST['id']);
				} else if($status == 1) {
					if(trim($contents) == trim(makeValidText($fields['output']))) {
						// holla! problem solved
						$query = "UPDATE solve SET status=2 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
						mysql_query($query);
						header("Location: index.php?success=1");
					} else {
						// duh! wrong output
						$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
						mysql_query($query);
						header("Location: solve.php?oerror=1&id=".$_POST['id']);
					}
				} else if($status == 2) {
					$query = "UPDATE solve SET status=1 WHERE (username='".$_SESSION['username']."' AND problem_id='".$_POST['id']."')";
					mysql_query($query);
					header("Location: solve.php?terror=1&id=".$_POST['id']);
				}*/
			} else{
				header("Location: editor.php?serror=1&prob_id=".$_POST['prob_id']); // compiler server not running
		}


// Check file size*/
//if($prob_def==TRUE){
//	echo "successfully updated";
//}
//else{
//	echo "failed";
//}
/*
if ($_FILES["user_file"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats
/*
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
*/
// Check if $uploadOk is set to 0 by an error

/*
	
    if (move_uploaded_file($_FILES['user_file']['tmp_name'], $target_file)) {
        echo "The file ". basename( $_FILES["user_file"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

*/
function makeValidText($text) {
	$str = str_replace("\n\r", "\n", $text);
	return str_replace("\r", "", $str);
}
?>
