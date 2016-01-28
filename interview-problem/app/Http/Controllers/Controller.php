<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Exception;

class Controller extends BaseController
{
    
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
    * @param  $starting_date , $days , $sessionsNum
    * @return json string
    */
    public function index($starting_date , $days , $sessionsNum) {
   		return $this->getSessionsDates_Json($starting_date , $days , $sessionsNum);
    }

    /**
    * take three parameters then return Sessions date 
    * @param  $starting_date , $days , $sessionsNum
    * @return json string
    */
    private function getSessionsDates_Json($starting_date , $days , $sessionsNum) {
	  	try {
	  		//define week days
	  		$daysNum =['Sat'  => 1 , 'Sun' => 2 , 'Mon' => 3 , 'Tue' => 4 , 'Wed' => 5 ,'The' => 6 , 'Fri' => 7];
	  		//define chapters 
	  		$chapters = 30;
	  		//final result
	  		$sessions = [];
	  		if(!is_numeric($sessionsNum)) {
	  			throw new Exception('Not Numiric Value');
	  		}
	  		$start_time = strtotime($starting_date);
	  		if($start_time == 0) {
	  			throw new Exception('Start Time is not Correct');
	  		}
	  		//construcr days in array 
	  		$days = explode(',' , $days);
	  		//sort days
	  		sort($days);
	  		//detrmine first session
	  		$start = date('D' , $start_time );
	  		$currentDay = $daysNum[$start];
	  		$currentSession = $start_time ;
	  		// j is iterator over $days
	  		$j = 1 ;
	  		//get first session so detrmine $j, $currentDay, $currentSession
	  		$daysSize = sizeof($days) ;
	  		for ($i=0; $i < $daysSize - 1; $i++) { 
	  			if($currentDay === $days[$i]) {// case: start day is a session
	  				$j = $i;
	  				break;
	  			}
	  			if($currentDay === $days[$daysSize - 1]) { // case: start day is last session in the week
	  				$j =  $daysSize - 1;
	  				break;
	  			}
	  			if($days[0]>$currentDay ){ // case: start day is before first session in the week
	  				$currentSession +=($days[0]-$currentDay) * 24*60*60 ;
	  				$currentDay = $days[0];
	  				$j = 0 ;
	  				break ;
	  			}
	  			if($days[$daysSize - 1]<$currentDay ){ // case: start day is before last session in the week
	  				$currentSession +=(7-$currentDay + $days[0]) * 24*60*60 ;
	  				$currentDay = $days[0];
	  				$j = 0 ;
	  				break ;
	  			}
	  			if($days[$i]<$currentDay && $days[$i+1]>$currentDay ) {  // case: start day is between tow sessions in the week
	  				$currentSession +=($days[$i+1]-$currentDay) * 24*60*60 ;
	  				$currentDay = $days[$i+1];
	  				$j = $i + 1 ;
	  				break;
	  			}
	  		}
	  		//sessions needed
	  		$totalSessions = $sessionsNum * $chapters;
	  		//increament $currentSession to get new Sessions
	  		// $new is days before next session
	  		$sessions[0] = date('d/m/Y' , $currentSession );
	  		for ($i = 1; $i < $totalSessions ; $i++) {
	  			$new = 0 ;
	  			if($j === $daysSize - 1) {
	  				$new =7- $days[$daysSize - 1] + $days[0]; // case : current session is last session in the week
	  				$j = -1 ;
	  			}
	  			else
	  				$new = $days[$j+1] -$days[$j] ;
	  			$j++;
	  			$currentSession += $new*24*60*60 ;
	  			$sessions[$i] = date('d/m/Y' , $currentSession );
	  		}
	  		$arr = array('Sessions' => $sessions);
	  		$json = json_encode($arr);
	  		return $json;
	  	} catch(Exception  $e) {
	  		return 'Caught exception: '.  $e->getMessage(); 
	  	}
    }
}
