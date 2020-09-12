<?php
 /*
 This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

/**
 * Peak and plate Predictor
 * Simple programming exercise
 * Author: Edison Guerrero
 * Date: 20200912
 * Version: 0.1
 * License gpl-3.0
 *
 */

 /**
  * Class Predictor
  * Predictor is a class designed to predict when a vehicle
  * is able or not to be on the road.
  *
  * It requires the definition of peak days, plate digit policy,
  * hours scheduled and the input of plate and datetime to be evaluated.
  *
  */
class Predictor{

  /**
  * policy : Array that Holds the realtionship between day and plates as rules.
  * Array index means the day while value is an array containing lats digit of
  * vehicle plates with restricted conditions.
  */
  private $policy=[[],[],[],[],[],[],[],[],[],[]];

  /**
  * params : Array that Holds the input definitios for DATE, TIME and PLATE.
  * Array index means the paramter to be evaluated.
  */
  private $params=["DATE"=>"","TIME"=>"","PLATE"=>""];

  /**
  * schedule : Array that Holds the pair of hours start and end of restriction.
  * Array shall contains at least one set of a arrays with a pair of values
  * start and end.
  */
  private $schedule=[];

  /**
  * Function : validate_input
  * Scope : private
  * Validates via regular expressions an input by type.
  *
  * @param string $date input.
  * @param string $type input.
  *
  * @return int
  */
  private function validate_input($input,$type){
    switch ($type) {
      case 'date':
        $regex="/[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])/";
        break;
      case 'time':
        $regex="/(2[0-3]|[01][0-9]):[0-5][0-9]/";
        break;
      case 'plate':
        $regex="/[A-Z]{3}[0-9]{4}/";
        break;
      default:
        $regex="//";
        break;
    }
    return (preg_match($regex, $input));
  }

  /**
  * Function : validate_policy
  * Scope : private
  * Validates data via structure and types for the policy input set.
  *
  * @return bool
  */
  private function validate_policy(){
    $error=0;
    foreach($this->policy as $policy=>$rules){

      if(is_integer($policy) && is_array($rules)){
        foreach ($rules as $rule=>$value) {
          if(!(is_integer($rule) && is_integer($value))){
            $error++;
          }
        }
      }else{
        $error++;
      }
    }
    return ($error===0);
  }

  /**
  * Function : validate_schedule
  * Scope : private
  * Validates data via structure and input types for the schedule set.
  *
  * @return bool
  */
  private function validate_schedule(){
    $error=0;
    if(count($this->schedule)>0){
      foreach ($this->schedule as $frame) {
        if(count($frame)===2){
          foreach($frame as $time){
            if(!$this->validate_input($time,'time')){
              $error++;
            }
          }
        }else{
          $error++;
        }
      }
    }
    return ($error===0);
  }

  /**
  * Function : time_to_minutes
  * Scope : private
  * Converts time to minutes of day.
  *
  * @param string $time input.
  *
  * @return int
  */
  private function time_to_minutes($time){
    $fragments=explode(":",$time);
    return (intval($fragments[0],10)*60)+intval($fragments[1],10);
  }

  /**
  * Function : eval_prediction
  * Scope : private
  * Evals variables involved to determine coincidence in restriction.
  *
  * @return bool
  */
  private function eval_prediction(){
    $restricted=false;
    $scheduledTime=[];
    $timeToEval=$this->time_to_minutes($this->param{"TIME"});
    $dayToEval=date('w',strtotime( $this->param{"DATE"}." ".$this->param{"TIME"} ) );
    $lastPlateDigit=intval( substr( $this->param{"PLATE"} , 6 , 1 ));

    if(array_key_exists($dayToEval,$this->policy) && in_array($lastPlateDigit,$this->policy[$dayToEval])){
      foreach($this->schedule as $frame=>$time){
        $scheduledTime[$frame]=[];
        foreach($time as $index=>$value){
          $scheduledTime[$frame][$index]=$this->time_to_minutes($value);
        }
      }
      foreach($scheduledTime as $frame){
        if($timeToEval >= $frame[0] && $timeToEval <= $frame[1]){
          $restricted=true;
          break;
        }
      }
    }
    return $restricted;
  }

  /**
  * Function : __construct
  * Scope : public
  * Sets default timezone on constrution time.
  *
  * @return void
  */
  public function __construct() {
    date_default_timezone_set('America/Guayaquil');
  }

  /**
  * Function : __destruct
  * Scope : public
  * Kills script execution on destruction time.
  *
  * @return void
  */
  public function __destruct() {
    exit(0);
  }

  /**
  * Function : set_param
  * Scope : public
  * Sets values to params on private scope.
  *
  * @param string $param input.
  *
  * @param string $value input.
  *
  * @return void
  */
  public function set_param($param,$value){
    if(array_key_exists( $param , $this->params )){
      $this->param{$param}=$value;
    }else{
      echo "Invalid parameter entered.";
    }
  }

  /**
  * Function : set_policy
  * Scope : public
  * Sets rules to policy on private scope.
  *
  * @param array $policy input.
  *
  * @return void
  */
  public function set_policy($policy){
    $this->policy=$policy;
  }

  /**
  * Function : set_schedule
  * Scope : public
  * Sets frametimes to schedule on private scope.
  *
  * @param array $schedule input.
  *
  * @return void
  */
  public function set_schedule($schedule){
    $this->schedule=$schedule;
  }

  /**
  * Function : predict
  * Scope : public
  * Call validations cascade and evals a prediction
  * to print on the standard output the results.
  *
  * @return void
  */
  public function predict(){
    if(!$this->validate_input($this->param{"DATE"},'date')){
      echo "Invalid date format, please use YYYY/MM/DD format.\n";
    }elseif(!$this->validate_input($this->param{"TIME"},'time')){
      echo "Invalid time format, please use the 24 hours HH:MM format.\n";
    }elseif(!$this->validate_input($this->param{"PLATE"},'plate')){
      echo "Invalid plate format, please use AAA9999 format.\n";
    }elseif(!$this->validate_policy()){
      echo "Invalid policy definition.\n";
    }elseif(!$this->validate_schedule()){
      echo "Invalid schedule definition.\n";
    }else{
      echo "\nThe vehicle ".$this->param{"PLATE"}." is".(($this->eval_prediction())?"":" not")." restricted to be on the road on ". $this->param{"DATE"} ." ". $this->param{"TIME"} ."\n";
    }
  }
}

$predictor = New Predictor();
$predictor->set_schedule([["07:00","09:30"],["16:00","19:30"]]);
$predictor->set_policy([[],[1,2],[3,4],[5,6],[7,8],[9,0],[]]);
$predictor->set_param("DATE", "2020/09/11");
$predictor->set_param("TIME", "12:59");
$predictor->set_param("PLATE", "PCM6029");
$predictor->predict();
