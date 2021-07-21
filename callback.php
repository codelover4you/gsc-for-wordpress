<?php
// require_once 'googleapi/vendor/autoload.php';
// session_start();
// $client = new Google\Client();
// $client->setAuthConfig('client_credentials.json');
// $client->addScope(Google_Service_Webmasters::WEBMASTERS);
// $client->setRedirectUri("http://localhost/gsc/callback.php");
// echo "<pre>";
// print_r($client);


include_once "googleapi/templates/base.php";
session_start();

//require_once realpath(dirname(__FILE__) . '/../src/Google/autoload.php');
require_once 'googleapi/vendor/autoload.php';
// $client_id = '985763157863-3er7g5u1faisohtb4hs7cp9b4mrpenkn.apps.googleusercontent.com'; // credofusion
$client_id = '989592660313-5rqkbdi3mjrp2rcnlpvp105uvatvkdb9.apps.googleusercontent.com';

// $client_secret = 'YCkuyQ4KYtlXknYIHGO6UrTh'; // credofusion
$client_secret = 'aKQwNi-TSC3ztmm555cjHN5r';
$redirect_uri = 'http://localhost/gsc/callback.php';

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("https://www.googleapis.com/auth/webmasters");

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
  $_SESSION['access_token'] = $client->getAccessToken();

    $q = new \Google_Service_Webmasters_SearchAnalyticsQueryRequest();

    $q->setStartDate('2021-05-01');
    $q->setEndDate('2021-06-20');
    $q->setDimensions(['date','page']);
    $q->setSearchType('web');
    try {
       $service = new Google_Service_Webmasters($client);
       $u = $service->searchanalytics->query('https://debate-motions.info/', $q);
	   
	   // print_r($u);
	   
	   $cArr = [];
	   $iArr = [];
       echo '<table border=1>';
       echo '<tr>
          <th>#</th><th>Clicks</th><th>CTR</th><th>Imp</th><th>Date</th><th>Page</th><th>Avg. pos</th>';
          for ($i = 0; $i < count($u->rows); $i++) {
			
			$cArr[] = array("date"=>strtotime($u->rows[$i]->keys[0]), "clicks"=>"{$u->rows[$i]->clicks}", "impressions"=>"{$u->rows[$i]->impressions}");
			$iArr[] = array("date"=>"{$u->rows[$i]->keys[0]}", "impressions"=>"{$u->rows[$i]->impressions}");
			
			
            echo "<tr><td>$i</td>";
            echo "<td>{$u->rows[$i]->clicks}</td>";
            echo "<td>{$u->rows[$i]->ctr}</td>";
            echo "<td>{$u->rows[$i]->impressions}</td>";
            echo "<td>{$u->rows[$i]->keys[0]}</td>";
			echo "<td>{$u->rows[$i]->keys[1]}</td>";
            echo "<td>{$u->rows[$i]->position}</td>";

            /* foreach ($u->rows[$i] as $k => $value) {
                //this loop does not work (?)
            } */
            echo "</tr>";
          }             
        echo '</table>';
     } catch(\Exception $e ) {
        echo $e->getMessage();
     }  
}

// echo "<pre>";
// print_r($cArr);
// echo "</pre>";

// $nArr = [];
// foreach($cArr as $v){
	// $date = $v['date'];
	// if (!array_key_exists($date, $nArr)){
		// $nArr[$date]['date'] = date("Y, m, d",$v['date']);
		// $nArr[$date]['clicks'] = $v['clicks'];
		// $nArr[$date]['impressions'] = $v['impressions'];
	// }else{
		// $nArr[$date]['clicks'] += $v['clicks'];
		// $nArr[$date]['impressions'] += $v['impressions'];
	// }
// }

// $sortArr = asort($nArr);
// // echo "<pre>";
// // print_r($nArr);
// // echo "</pre>";

// $dataArr = array();
// foreach($nArr as $data_val){
	// //print_r($data_val);
	// $dataArr[] = "[new Date(".$data_val['date']."),".$data_val['clicks'].",".$data_val['impressions']."]";  
// }


// echo "<pre>";
// print_r($dataArr);
// echo "</pre>";
?>

<div class="request">
<?php 
    if (isset($authUrl)) {
      echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
    } else {
      echo <<<END
     <form id="url" method="GET" action="{$_SERVER['PHP_SELF']}">
       <input name="url" class="url" type="text">
       <input type="submit" value="Shorten">
     </form>
     <a class='logout' href='?logout'>Logout</a>
END;
}
?>
</div>