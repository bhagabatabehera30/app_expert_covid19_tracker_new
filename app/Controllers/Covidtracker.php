<?php namespace App\Controllers;

use GuzzleHttp\Client;
//use CodeIgniter\Controller;
Use App\Models\CoviddataModel;

class Covidtracker extends BaseController
{

	protected $coviddata;
  

  public function __construct()
  {
    date_default_timezone_set('Asia/Kolkata');
    $this->session = \Config\Services::session();
    $this->session->start();
    $this->request = \Config\Services::request();  
    $this->db= \Config\Database::connect();    
    $this->coviddata = new CoviddataModel();            
  }

  public function index()
  {
    $countryCode=$this->request->getPost('countryCode');
  $startDate=$this->request->getPost('startDate');
  $endDate=$this->request->getPost('endDate');

$td = strtotime("today");
$last_week = strtotime("-1 week +1 day",$td);
$last_week_date = date("Y-m-d",$last_week); 

$last_month= strtotime("-1 month +1 day",$td);
$last_month_date = date("Y-m-d",$last_month); 
$today_date=date('Y-m-d');

$yes_dt = strtotime("yesterday");
$yesterday = date("Y-m-d",$yes_dt); 

  if(isset($countryCode) && !empty($countryCode)){
    $countryCode=$countryCode;
  }else{
    $countryCode='IN';  
  }

  if(isset($startDate) && !empty($startDate)){
    $startDate=$startDate;
  }else{
    $startDate=$last_month_date;
  }

  if(isset($endDate) && !empty($endDate)){
    $endDate=$endDate;
  }else{
    $endDate=$today_date;
  }

  if($countryCode=='IN'){
    $contry='India';
  }
  if($countryCode=='US'){
    $contry='USA';
  }

$client = new Client([
  // Base URI is used with relative requests
  'base_uri' => 'http://api.coronatracker.com/v3/analytics/',
  // You can set any number of default request options.
  'timeout'  => 10,  
]);

try {
  /**
   * Handle the Guzzle connection exception here -----
   */
  $response = $client->get('newcases/country',
  [
    'query' => ['countryCode' => $countryCode, 'startDate' => $startDate, 'endDate' => $endDate]
  ]); 

  try {
    /**
     * We use Guzzle to make an HTTP request somewhere in the
     */ 
    $res_status=$response->getStatusCode();
    $rse_msg=$response->getReasonPhrase();
     if($res_status==200 && $rse_msg=='OK'){
    if ($response->getBody()) {
      $res_data=$response->getBody(); 
      $res_data_arr=json_decode($res_data,true);
      //print_r($res_data_arr);
      $tot_data_inserted=0;
      foreach($res_data_arr as $value){
       $country=$value['country'];
        $last_updated=$value['last_updated'];
        $last_updated_date=date('Y-m-d', strtotime($value['last_updated']));
        $new_infections=$value['new_infections'];
        $new_deaths=$value['new_deaths'];
        $new_recovered=$value['new_recovered'];  

        $checkData=$this->coviddata->where('DATE(last_updated)', $last_updated_date)
        ->where('country', $contry)
        ->first();
        //echo  $this->db->getLastQuery(); 
        //exit();
        if(empty($checkData)){  
          $insert['country'] = $country;
          $insert['last_updated'] = $last_updated;
          $insert['new_infections'] = $new_infections;
          $insert['new_deaths'] = $new_deaths;
          $insert['new_recovered'] = $new_recovered;
         // print_r($insert);   
          $res = $this->coviddata->insert($insert); 
          $tot_data_inserted++;   
        }
      }
      
      $query=$this->db->table('tbl_covid19_data')
      ->selectSum('new_infections','total_new_infections')
     ->selectSum('new_deaths','total_new_deaths')
      ->selectSum('new_recovered','total_new_recovered')
      ->where('DATE(last_updated)', $yesterday)
      ->where('country', $contry)
      ->get();
      $rsLastDay=$query->getResultArray();
      $data['rsLastDay']=$rsLastDay;
      //echo  $this->db->getLastQuery();  
      //print_r($rsLastDay);

      $query2=$this->db->table('tbl_covid19_data')
      ->selectSum('new_infections','total_new_infections')
     ->selectSum('new_deaths','total_new_deaths')
      ->selectSum('new_recovered','total_new_recovered')
      ->where('DATE(last_updated) >= ', $last_week_date)
      ->where('DATE(last_updated) < ', $today_date)
      ->where('country', $contry)
      ->get();
      $rsLastWeek=$query2->getResultArray();
      $data['rsLastWeek']=$rsLastWeek; 
      //echo  $this->db->getLastQuery();  
      //print_r($rsLastWeek); 

      $query3=$this->db->table('tbl_covid19_data')
      ->selectSum('new_infections','total_new_infections')
     ->selectSum('new_deaths','total_new_deaths')
      ->selectSum('new_recovered','total_new_recovered')
      ->where('DATE(last_updated) >= ', $last_month_date)
      ->where('DATE(last_updated) < ', $today_date)
      ->where('country', $contry)
      ->get();
      $rsLastMonth=$query3->getResultArray();
      $data['rsLastMonth']=$rsLastMonth; 
       //echo  $this->db->getLastQuery();  
     // print_r($rsLastMonth); 

      $query4=$this->db->table('tbl_covid19_data')
      ->selectSum('new_infections','total_new_infections')
     ->selectSum('new_deaths','total_new_deaths')
      ->selectSum('new_recovered','total_new_recovered')
      ->where('DATE(last_updated) >= ', $startDate)
      ->where('DATE(last_updated) < ', $endDate)
      ->where('country', $contry)
      ->get();
      $rsDataByFilterRange=$query4->getResultArray();
      $data['rsDataByFilterRange']=$rsDataByFilterRange; 
       //echo  $this->db->getLastQuery();  
     // print_r($rsDataByFilterRange);   

      $data['startDate']=$startDate;
      $data['endDate']=$endDate;
      $data['countryCode']=$countryCode; 

      
      if($tot_data_inserted>0){
        $message="Total $tot_data_inserted number of new rows inserted into our the database.
        Then the data is fetching from database.";
       }
      if($tot_data_inserted==0){
       $message="The new inserted rows is zero. The existing data is fetching from database.";
      }
      $this->session->setFlashdata('success', $message); 
      unset($_SESSION['error']);
      return view('home', $data);
    }
     }else{
      $data['rsLastDay']=array();
      $data['rsLastWeek']=array();
      $data['rsLastMonth']=array();
      $data['rsDataByFilterRange']=array(); 
      $data['startDate']=$startDate;
      $data['endDate']=$endDate;
      $data['countryCode']=$countryCode; 
      $message='Some error occurs in api response.';
      $this->session->setFlashdata('error', $message);
      unset($_SESSION['success']);
      return view('home', $data);
     }
} catch (\GuzzleHttp\Exception\RequestException $e) {
  if ($e->hasResponse()) {
    $resErrMsg = $e->getResponse();
   $msg_code=$resErrMsg->getStatusCode(); // HTTP status code;
   $msg_prhase=$resErrMsg->getReasonPhrase(); // resErrMsg message;
   $msg_body=(string) $resErrMsg->getBody(); // Body, normally it is JSON;
   $msg_arr=array('status'=>$msg_code, 'msg_prhase'=>$msg_prhase, 'message'=>$msg_body);
   $res_msg_json=json_encode($msg_arr);
   $data['rsLastDay']=array();
  $data['rsLastWeek']=array();
  $data['rsLastMonth']=array();
  $data['rsDataByFilterRange']=array(); 
  $data['startDate']=$startDate;
  $data['endDate']=$endDate;
  $data['countryCode']=$countryCode; 
   $message='Error from api end point :- '.$res_msg_json;
   $this->session->setFlashdata('error', $message);
   unset($_SESSION['success']);  
   return view('home', $data);
}
} 
}
 catch (\GuzzleHttp\Exception\ConnectException $e) {
 
  $data['rsLastDay']=array();
  $data['rsLastWeek']=array();
  $data['rsLastMonth']=array();
  $data['rsDataByFilterRange']=array(); 
  $data['startDate']=$startDate;
  $data['endDate']=$endDate;
  $data['countryCode']=$countryCode; 

  $message='Failed to connect to api.coronatracker.com port 80: Timed out. Please reload again.';
  $this->session->setFlashdata('error', $message);
  unset($_SESSION['success']);
  return view('home', $data);
}
  }

  

public function get_filter_data()
{		
  $countryCode=$this->request->getPost('countryCode');
  $startDate=$this->request->getPost('startDate');
  $endDate=$this->request->getPost('endDate');

$td = strtotime("today");
$last_week = strtotime("-1 week +1 day",$td);
$last_week_date = date("Y-m-d",$last_week); 

$last_month= strtotime("-1 month +1 day",$td);
$last_month_date = date("Y-m-d",$last_month); 
$today_date=date('Y-m-d');

$yes_dt = strtotime("yesterday");
$yesterday = date("Y-m-d",$yes_dt); 

  if(isset($countryCode) && !empty($countryCode)){
    $countryCode=$countryCode;
  }else{
    $countryCode='IN';  
  }

  if(isset($startDate) && !empty($startDate)){
    $startDate=$startDate;
  }else{
    $startDate=$last_month_date;
  }

  if(isset($endDate) && !empty($endDate)){
    $endDate=$endDate;
  }else{
    $endDate=$today_date;
  }

  if($countryCode=='IN'){
    $contry='India';
  }
  if($countryCode=='US'){
    $contry='USA';
  }

$client = new Client([
  // Base URI is used with relative requests
  'base_uri' => 'http://api.coronatracker.com/v3/analytics/',
  // You can set any number of default request options.
  'timeout'  => 10,  
]);

try {
  /**
   * Handle the Guzzle connection exception here -----
   */
  $response = $client->get('newcases/country',
  [
    'query' => ['countryCode' => $countryCode, 'startDate' => $startDate, 'endDate' => $endDate]
  ]); 

  try {
    /**
     * We use Guzzle to make an HTTP request somewhere in the
     */ 
    $res_status=$response->getStatusCode();
    $rse_msg=$response->getReasonPhrase();
     if($res_status==200 && $rse_msg=='OK'){
    if ($response->getBody()) {
      $res_data=$response->getBody(); 
      $res_data_arr=json_decode($res_data,true);
      //print_r($res_data_arr);
      $tot_data_inserted=0;
      foreach($res_data_arr as $value){
       $country=$value['country'];
        $last_updated=$value['last_updated'];
        $last_updated_date=date('Y-m-d', strtotime($value['last_updated']));
        $new_infections=$value['new_infections'];
        $new_deaths=$value['new_deaths'];
        $new_recovered=$value['new_recovered'];  

        $checkData=$this->coviddata->where('DATE(last_updated)', $last_updated_date)
        ->where('country', $contry)
        ->first();
        //exit();  
        if(empty($checkData)){  
          $insert['country'] = $country;
          $insert['last_updated'] = $last_updated;
          $insert['new_infections'] = $new_infections;
          $insert['new_deaths'] = $new_deaths;
          $insert['new_recovered'] = $new_recovered;
         // print_r($insert);   
          $res = $this->coviddata->insert($insert); 
          $tot_data_inserted++;   
        }
      }

      $query=$this->db->table('tbl_covid19_data')
      ->selectSum('new_infections','total_new_infections')
     ->selectSum('new_deaths','total_new_deaths')
      ->selectSum('new_recovered','total_new_recovered')
      ->where('DATE(last_updated)', $yesterday)
      ->where('country', $contry)
      ->get();
      $rsLastDay=$query->getResultArray();
      $data['rsLastDay']=$rsLastDay;
       //echo  $this->db->getLastQuery();  
      //print_r($rsLastDay);

      $query2=$this->db->table('tbl_covid19_data')
      ->selectSum('new_infections','total_new_infections')
     ->selectSum('new_deaths','total_new_deaths')
      ->selectSum('new_recovered','total_new_recovered')
      ->where('DATE(last_updated) >= ', $last_week_date)
      ->where('DATE(last_updated) < ', $today_date)
      ->where('country', $contry)
      ->get();
      $rsLastWeek=$query2->getResultArray();
      $data['rsLastWeek']=$rsLastWeek; 
       //echo  $this->db->getLastQuery();  
      //print_r($rsLastWeek); 

      $query3=$this->db->table('tbl_covid19_data')
      ->selectSum('new_infections','total_new_infections')
     ->selectSum('new_deaths','total_new_deaths')
      ->selectSum('new_recovered','total_new_recovered')
      ->where('DATE(last_updated) >= ', $last_month_date)
      ->where('DATE(last_updated) < ', $today_date)
      ->where('country', $contry)
      ->get();
      $rsLastMonth=$query3->getResultArray();
      $data['rsLastMonth']=$rsLastMonth; 
      // echo  $this->db->getLastQuery();  
     // print_r($rsLastMonth); 

      $query4=$this->db->table('tbl_covid19_data')
      ->selectSum('new_infections','total_new_infections')
     ->selectSum('new_deaths','total_new_deaths')
      ->selectSum('new_recovered','total_new_recovered')
      ->where('DATE(last_updated) >= ', $startDate)
      ->where('DATE(last_updated) < ', $endDate)
      ->where('country', $contry)
      ->get();
      $rsDataByFilterRange=$query4->getResultArray();
      $data['rsDataByFilterRange']=$rsDataByFilterRange; 
       //echo  $this->db->getLastQuery();  
     // print_r($rsDataByFilterRange);    

      $data['startDate']=$startDate;
      $data['endDate']=$endDate;
      $data['countryCode']=$countryCode; 

      
      if($tot_data_inserted>0){
        $message="Total $tot_data_inserted number of new rows inserted into our the database.
        Then the data is fetching from database.";
       }
      if($tot_data_inserted==0){
       $message="The new inserted rows is zero. The existing data is fetching from database.";
      }
      $this->session->setFlashdata('success', $message); 
      unset($_SESSION['error']);
      return view('home', $data);
    }
     }else{
      $data['rsLastDay']=array();
      $data['rsLastWeek']=array();
      $data['rsLastMonth']=array();
      $data['rsDataByFilterRange']=array(); 
      $data['startDate']=$startDate;
      $data['endDate']=$endDate;
      $data['countryCode']=$countryCode; 
      $message='Some error occurs in api response.';
      $this->session->setFlashdata('error', $message);
      unset($_SESSION['success']);
      return view('home', $data);
     }
} catch (\GuzzleHttp\Exception\RequestException $e) {
  if ($e->hasResponse()) {
    $resErrMsg = $e->getResponse();
   $msg_code=$resErrMsg->getStatusCode(); // HTTP status code;
   $msg_prhase=$resErrMsg->getReasonPhrase(); // resErrMsg message;
   $msg_body=(string) $resErrMsg->getBody(); // Body, normally it is JSON;
   $msg_arr=array('status'=>$msg_code, 'msg_prhase'=>$msg_prhase, 'message'=>$msg_body);
   $res_msg_json=json_encode($msg_arr);
   $data['rsLastDay']=array();
  $data['rsLastWeek']=array();
  $data['rsLastMonth']=array();
  $data['rsDataByFilterRange']=array(); 
  $data['startDate']=$startDate;
  $data['endDate']=$endDate;
  $data['countryCode']=$countryCode; 
   $message='Error from api end point :- '.$res_msg_json;
   $this->session->setFlashdata('error', $message);
   unset($_SESSION['success']);  
   return view('home', $data);
}
} 
}
 catch (\GuzzleHttp\Exception\ConnectException $e) {
 
  $data['rsLastDay']=array();
  $data['rsLastWeek']=array();
  $data['rsLastMonth']=array();
  $data['rsDataByFilterRange']=array(); 
  $data['startDate']=$startDate;
  $data['endDate']=$endDate;
  $data['countryCode']=$countryCode; 

  $message='Failed to connect to api.coronatracker.com port 80: Timed out. Please reload again.';
  $this->session->setFlashdata('error', $message);
  unset($_SESSION['success']);
  return view('home', $data);
}

}





}