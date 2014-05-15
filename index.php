<?php 
session_start();
require_once('config.php');
require_once('twitteroauth.php');

if(isset($_GET['reset']) && $_GET['reset']==1)
{
	session_destroy();
	header('Location: index.php');
	exit;	
}

if(isset($_POST['setting_form']))
{
	$_SESSION['sc_name'] = strip_tags($_POST['name']); 
	header('Location: index.php');
	exit;
}
	 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title> Twitter feeds</title>
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
    <div class="logo"><h2> Twitter Feeds </h2> </div>
  </div>
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <div class="container"> <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a> <a class="brand hidden-desktop" href="index.html">Tweets</a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <li  class="active"><a href="index.php">Follower's Tweets</a></li>
            <?php if(isset($_SESSION['sc_name'])){?>
            <li><a href="display.php"><?php echo $_SESSION['sc_name']; ?> Tweets</a></li>
            <?php } ?>
          </ul>
        </div>
        <!--/.nav-collapse --> 
      </div>
    </div>
  </div>
   
  <div class="container">
     
     
    <?php if(!isset($_SESSION['sc_name'])){?>
    <!-- from -->
        <form role="form" method="post" action="">
            <div class="form-group">
                <label for="name">Screen Name</label>
                <input type="text" name="name" class="form-control" id="name" required placeholder="Enter Screen Name">
            </div>
             
             
            <button type="submit" name="setting_form" class="btn btn-default">Submit</button>
        </form>
    <!-- end of form  -->
    <?php }else{ ?>
    
		<div class="row">
     	 <div class="span4">  
        	<h4>Screen Name is Set To <a href="https://twitter.com/<?php echo $_SESSION['sc_name']; ?>" target="_blank"> <?php echo $_SESSION['sc_name']; ?> </a> </h4>
            <p>Click <a href="index.php?reset=1"> Here </a> to reste</p>
            </div>
         </div>
	<?php	} ?>	
 
     <?php if(isset($_SESSION['sc_name'])){?> 
    
		<div class="row">
            <?php 
			$tweet 			= new TwitterOAuth(CONSUMERKEY, CONSUMERSECRET, OUTHTOKEN, OUTHSECRET);
			$followers_ids 	= $tweet->get('https://api.twitter.com/1.1/followers/ids.json?screen_name='.$_SESSION['sc_name'].'&count='.FEEDSLIMIT);
			// print_r($followers_ids);
			if(!empty($followers_ids->ids))
			{ 
				$t_arr 	     = array();
				$i = 0 ; 
				foreach($followers_ids->ids as $id)
				{
					$name = $tweet->get('https://api.twitter.com/1.1/statuses/user_timeline.json?user_id='.$id.'&count=1'); 
					
					//	using [0] to get the most recent tweet and to avoide use of loop
					if(!empty($name) && !empty($name[0]))
					{ 
						$t_arr[$i]['tweetby'] = $name[0]->user->name;
						$t_arr[$i]['msg'] 	 = $name[0]->text;
						$t_arr[$i]['link'] 	= 'http://twitter.com/#!/'. $_SESSION['sc_name'] .'/status/'. $name[0]->id_str;
						$time 			     = explode('+',$name[0]->created_at);
						$t_arr[$i]['time']	= $time[0];
						$t_arr[$i]['img']	 = $name[0]->user->profile_image_url; 
						$i++; 
						 
					} 
				}
				
				// ordering from newest to oldes
				function date_compare($a, $b)
				{
					$t1 = strtotime($a['time']);
					$t2 = strtotime($b['time']);
					return $t2 - $t1;
				}    
				usort($t_arr, 'date_compare');
				 
				// displaying tweets of the followers
				foreach($t_arr as $single_twitt)
				{
					echo ' <div class="span1">
							<img src="'.$single_twitt['img'].'" class="pull-left" alt="Profile Image">
							</div>
							<div class="span8">
							<p>'.$single_twitt['tweetby'].' , ' .  $single_twitt['time'] . '</p>
							<p> <a href="'.$single_twitt['link'].'" target="_blank">'.$single_twitt['msg'].'</a></p></div>
						   '; 
				}
				
				
			}else{
				 echo '<h4 class="span4">No followers found</h4>';
				}
			  } 
			  ?> 
  </div>
  <!-- /container --> 
  <div class="modal-footer">
   
    
  </div>
    
  <script src="js/jquery-1.10.1.js"></script> 
  <script src="js/bootstrap.js"></script>    
</div>
</body>
</html>
