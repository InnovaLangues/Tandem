<?php
require_once dirname(__FILE__) . '/classes/lang.php';
require_once dirname(__FILE__) . '/classes/constants.php';
require_once dirname(__FILE__) . '/classes/gestorBD.php';
require_once dirname(__FILE__) . '/classes/utils.php';
require_once 'IMSBasicLTI/uoc-blti/lti_utils.php';



$user_obj = isset($_SESSION[CURRENT_USER]) ? $_SESSION[CURRENT_USER] : false;
$course_id = isset($_SESSION[COURSE_ID]) ? $_SESSION[COURSE_ID] : false;

if (!$user_obj) {
//Tornem a l'index
	header('Location: index.php');
	die();
} else {	
	$gestorBD = new GestorBD();  	
	$usersRanking = $gestorBD->getUsersRanking($course_id);	
}


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<link href="css/tandem-waiting-room.css" rel="stylesheet">
<style>
.green-for-english{
	color:#7E9F0B;
}
.purple-for-spanish{
	color:#4F2F78;
}
</style>
</head>
<body>
<div class="container" style='margin-top:20px'>
	<div class='row'>
		<div class='col-md-8'>
			<button class="btn btn-success" type='button' onclick="window.location ='portfolio.php';">
				<?php echo $LanguageInstance->get('Go to your portfolio') ?></button>
		</div>
		<div class='col-md-4'>
	  	<p class='text-right'>
			<a href="#" title="<?php echo $LanguageInstance->get('tandem_logo')?>"><img src="css/images/logo_Tandem.png" alt="<?php echo $LanguageInstance->get('tandem_logo')?>" /></a>					
	  	</p>
  	</div>
	</div>
  	<div class="row">
	  	<div class='col-md-6'>
	  		<h1 class='title'><?php echo $LanguageInstance->get('Users ranking');?></h1>
	  	</div>
	  	<div class='col-md-6'>
	  		<div class='welcomeMessage text-right'>
				<?php 
				$getUserRankingPosition = $gestorBD->getUserRankingPosition($user_obj->id,$_SESSION['lang'],$course_id);			
				$positionInRankingTxt =  $LanguageInstance->get('Hello %1');
				$positionInRankingTxt = str_replace("%1",$gestorBD->getUserName($user_obj->id),$positionInRankingTxt);
				if($getUserRankingPosition > 0)
					$positionInRankingTxt .= $LanguageInstance->get(', your position in the ranking is ')."<b>".$getUserRankingPosition."</b>";			
				echo $positionInRankingTxt;			
			?>
			</div>
	  	</div>
  	</div>
  	<div class='row'>
  		<div class='col-md-12'>
  			<div class="alert alert-info" role="alert"><?php echo $LanguageInstance->get('top_10_ranking_message')?></div>
  		</div>  		
  	</div>
  	<div class='row'>
	  <div class="col-md-6">
	  <h3 class='green-for-english'><?php echo $LanguageInstance->get('Ranking for learners of English');?></h3>
  		<table class="table table-striped">
  		<tr>
		  	<th class='text-center'><?php echo $LanguageInstance->get('Position');?></th>
		  	<th><?php echo $LanguageInstance->get('User');?></th>
		  	<th><?php echo $LanguageInstance->get('Points');?></th>
		  	<?php if ($user_obj->instructor== 1 || $user_obj->admin==1) {echo "<th>".$LanguageInstance->get('Total time')."</th>";}	  	 
		  	?>
  		</tr>
 	<?php
	  if(!empty($usersRanking['en'])){
	  	$cont = 1;
	  	foreach($usersRanking['en'] as $f){
		  			  	
			  	$class='';
			  	if($cont <= 3) $class = 'class="success"';
			  	if($cont > 3 && $cont <= 10) $class = 'class="warning"';	
			  	echo "<tr $class>";
			  	echo "<td class='text-center'>".$cont."</td>";	  		  	
			  	echo "<td>".$f['user']."</td>";			  	
			  	echo "<td>".$f['points']."</td>";
			  	if( ($user_obj->instructor == 1) ||($user_obj->admin == 1)) {$obj = secondsToTime($f['total_time']);$time = '';
                        if ($obj['h']>0) {
                            $time .= ($obj['h']<10?'0':'').$obj['h'].':';
                        }
                        $time .= ($obj['m']<10?'0':'').$obj['m'].':';
                        $time .= ($obj['s']<10?'0':'').$obj['s'];
		  	 	echo "<td>".$time."</td>";
		  	 	}
	  		  	echo "</tr>";	
			  	$cont++;			
	  	}
	  }
  	?>
  </table>
  </div>
  <div class='col-md-6'>
    <h3 class='purple-for-spanish'><?php echo $LanguageInstance->get('Ranking for learners of Spanish');?></h3>
  	<table class="table table-striped">
  	<tr>
	  	<th class='text-center'><?php echo $LanguageInstance->get('Position');?></th>
	  	<th><?php echo $LanguageInstance->get('User');?></th>
	  	<th><?php echo $LanguageInstance->get('Points');?></th>
	  	<?php
		  	 if( ($user_obj->instructor == 1) ||($user_obj->admin == 1) ){
		  	 	echo "<th>".$LanguageInstance->get('Total time')."</th>";	
		  	 }	  	 
		?>
  	</tr>
 	<?php
	  if(!empty($usersRanking['es'])){
	  	$cont = 1;
	  	foreach($usersRanking['es'] as $f){
		  	$class='';
		  	if($cont <= 3) $class = 'class="success"';
		  	if($cont > 3 && $cont <= 10) $class = 'class="warning"';	
		  	echo "<tr $class>";
		  	echo "<td class='text-center'>".$cont."</td>";
		  	//we only want to show the name of the top 3 , the rest just ....	  	
		  	echo "<td>".$f['user']."</td>";		  	
		  	echo "<td>".$f['points']."</td>";
		  	if( ($user_obj->instructor == 1) || ($user_obj->admin == 1) ){
		  		$obj = secondsToTime($f['total_time']);
                        $time = '';
                        if ($obj['h']>0) {
                            $time .= ($obj['h']<10?'0':'').$obj['h'].':';
                        }
                        $time .= ($obj['m']<10?'0':'').$obj['m'].':';
                        $time .= ($obj['s']<10?'0':'').$obj['s'];
		  	 		echo "<td>".$time."</td>";
		  	}
		  	echo "</tr>";	

		  	$cont++;
	  	}
	  }
  	?>
  </table>
  </div>
  </div>
</div>



</body>
</html>


