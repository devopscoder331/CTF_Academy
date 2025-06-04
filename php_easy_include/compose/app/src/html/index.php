<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PHP injection: php10</title>
  <style>
    body  {
    font-family: 'Press Start 2P';
    min-width: 260px;
    //text-align: center;
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
      //text-align: center;
      color: #7393B3;
    }
		img {
		margin-top: 1.4% !important;
		height: 560px;
		padding: 20px;
		max-width: 100%;
		display: block;
		height: auto;
		//margin: auto;
		object-fit: cover;
		overflow: hidden;
		}
  </style>

</head>
<body>
	<h2>PHP injection: php10</h2>
	<?php 
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
			include($page);
		}else{
      echo "<img src='static/img/mrrobot_hack.gif'>";
			echo "<p>Введите имя документа через параметр page методом GET";
			echo "<br>Например, загрузите about.php</p>";
			echo "<p>Ты знаком с PHP Code Injection? Время научиться, Good Luck!</p>";
      echo "<p>Утилиты которые пригодятся в этом задании:<br>
        - burp suite community edition<br>
        - пейлоды для php injection<br>
        </p>";
		}
	?>
</body>
