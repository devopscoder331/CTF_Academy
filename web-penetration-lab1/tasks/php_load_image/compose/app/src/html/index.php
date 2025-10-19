<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PHP injection: load_image</title>
  <style>
    body  {
    font-family: 'Press Start 2P';
    min-width: 260px;
    color: #36454F;
    background-color: #ffffff;
    } 
    #title {
      display: inline-flex;
    }

    #title i {
      color: #D34156;
      font-size: 50px;
      margin: 0px 40px;
    }
    #time {
      color: #36454F;
    }
    h2 {
      color: #7393B3;
    }
		img {
		margin-top: 1.4% !important;
		height: 560px;
		padding: 20px;
		max-width: 100%;
		display: block;
		height: auto;
		object-fit: cover;
		overflow: hidden;
		}
  </style>
</head>
<body>
<h2>PHP injection: load_image</h2>
<?php
echo "<img src='static/img/file_upload_hack.png'>";
echo "<br>Сможешь найти уязвимость?</p>";
?>

<form action="" enctype="multipart/form-data" method="post"
name="upload">file: <input type="file" name="file" /><br><br>
<input type="submit" value="upload" /></form>
<?php
if(!empty($_FILES["file"]))

{
    if (((@$_FILES["file"]["type"] == "image/gif") || (@$_FILES["file"]["type"] == "image/jpeg")
    || (@$_FILES["file"]["type"] == "image/jpg") || (@$_FILES["file"]["type"] == "image/pjpeg")
    || (@$_FILES["file"]["type"] == "image/x-png") || (@$_FILES["file"]["type"] == "image/png"))
    && (@$_FILES["file"]["size"] < 102400))
    {
        move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
        echo "Load in:  " . "upload/" . $_FILES["file"]["name"];
    }
    else
    {
        echo "upload failed!";
    }
}
?>
