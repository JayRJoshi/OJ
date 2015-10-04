<?php
include('config.php');
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
$lang_id=$_POST['lang_id'];
$target_dir = "solutions_from_users/";
$target_file = $target_dir . basename($_FILES["user_file"]["name"]);

echo "From codearea: ".$_POST['codearea'];
//$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);


/*$prob_def_query = "INSERT INTO `onlinejudge`.`submitted_codes` VALUES (NULL,".$prob_id.",".$code.",NOW(),".$user_id.",NULL,NULL,".$lang_id.",NULL)";
*/
echo "\n\n";
$prob_def_query="INSERT INTO `onlinejudge`.`submitted_codes` VALUE  (NULL,".$prob_id.",'".$code."', NOW() , '".$user_id."', NULL , NULL , '".$lang_id."', NULL)";

echo $prob_def_query;
/*
BEGIN
INSERT INTO `submission_queue` VALUES (NEW.submission_id,0);
INSERT INTO `submission_results` SELECT testcase_id,NEW.submission_id,NULL,0,NULL,NULL FROM `testcases` WHERE `testcases`.Problem_Id=NEW.Problem_Id;
END
*/
$prob_def = $conn->query($prob_def_query);
// Check if image file is a actual image or fake image
/*if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
*/
// Check if file already exists
/*
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}*/
// Check file size
if($prob_def==TRUE){
	echo "successfully updated";
}
else{
	echo "failed";
}
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
?>
