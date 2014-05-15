<?php 
session_start();
require_once('config.php');
require_once('twitteroauth.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Test Task</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Le styles -->
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/bootstrap-responsive.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Droid+Serif|Lobster+Two' rel='stylesheet' type='text/css'>
<link href="css/mystyles.css" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>

<body>
<div class="container">
  <div class="page-header">
    <h2> Twitter Feeds</h2>
  </div>
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <div class="container"> <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a> <a class="brand hidden-desktop" href="index.html">Tweets</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <li><a href="index.php">Follower's Tweets</a></li>
            <?php if(isset($_SESSION['sc_name'])){?>
            <li class="active"><a href="display.php"><?php echo $_SESSION['sc_name']; ?> Tweets</a></li>
            <?php } ?>
          </ul>
        </div>
        <!--/.nav-collapse --> 
      </div>
    </div>
  </div>
  <div class="container">
     
     
    <?php if(!isset($_SESSION['sc_name'])){?>
    	<h3>Please set screen name first</h3>
        
    <?php }else{ ?>
    
		<div class="row">
     	    
        	
            <?php 
			$connection 	= new TwitterOAuth(CONSUMERKEY, CONSUMERSECRET, OUTHTOKEN, OUTHSECRET);
			$tweets 		= $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$_SESSION['sc_name']."&count=5");
			
			foreach($tweets as $tweet) {
				$msg 	 = $tweet->text;
				$link 	= 'http://twitter.com/#!/'. $_SESSION['sc_name'] .'/status/'. $tweet->id_str;
				$time 	= explode('+', $tweet->created_at); 
				$time 	= $time[0]; 
				$img	 = $tweet->user->profile_image_url; 
				
				echo ' <div class="span1">
							<img src="'.$img.'" class="pull-left" alt="Profile Image">
					    </div>
						<div class="span8">
						<p><a href="'.$link.'" target="_new">'.$msg.'</a></p>
						<p> '. $time .'</p>
						</div>';  
			}
			
			?> 
            </div>
			<?php } ?> 
  </div>
  <div class="modal-footer">   
  </div>
</body>
</html>
