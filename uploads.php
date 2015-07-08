<?php 
	if(isset($_POST['enviar'])) { 
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES['archivo']['name']);
		//echo $target_file;
		$uploadOk = 1;
		//echo $target_file;
		// Check if file already exists
		if (file_exists($target_file)) {
		    echo "Sorry, file already exists.\n";
		    $uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		//echo $uploadOk;
		if ($uploadOk == 0) {
		    echo "Sorry, your file was not uploaded.\n";
		// if everything is ok, try to upload file
		} else {
			//echo $_FILES["archivo"]["tmp_name"];
		    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $target_file)) {
		        echo "The file ". basename( $_FILES["archivo"]["name"]). " has been uploaded.\n";
		    } else {
		        echo "Sorry, there was an error uploading your file.\n";
		    }
		}
	} 
?>