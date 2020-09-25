<?php $this->session = \Config\Services::session(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Theme Made By www.w3schools.com - No Copyright -->
  <title>Covid-19 Tracker</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
  <style>
    body {
      font: 20px Montserrat, sans-serif;
      line-height: 1.8;  
    }
    p {font-size: 16px;}
    .margin {margin-bottom: 45px;}

    .container-fluid {
      padding-top: 20px;
      padding-bottom: 20px;
    }
    .navbar {
      padding-top: 15px;
      padding-bottom: 15px;
      border: 0;
      border-radius: 0;
      margin-bottom: 0;
      font-size: 12px;
      letter-spacing: 5px;
    }
    .navbar-nav  li a:hover {
      color: #1abc9c !important;
    }
    .bg-orange {
    background: #FF9800;
}
.bg-danger {
    background: #FF5722;
}
.bg-green {
    background: #8BC34A;
}
.small-box {
    padding: 50px 0px;
    color: #fff;
}


/* Center the loader */
#loader {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 150px;
  height: 150px;
  margin: -75px 0 0 -75px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Add animation to "page content" */
.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-100px; opacity:0 } 
  to { bottom:0px; opacity:1 }
}

@keyframes animatebottom { 
  from{ bottom:-100px; opacity:0 } 
  to{ bottom:0; opacity:1 }
}

#myDiv {
  display: none;
  text-align: center;
}

  </style>
</head>
<body onload="myFunction()">

  <!-- Navbar -->
  <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>                        
        </button>
        <a class="navbar-brand" href="#">Covid-19 Tracker</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#">Demo</a></li>
        </ul>
      </div>
    </div>
  </nav>

  
  <div id="loader"></div>
	<div style="display:none;" id="myDiv" class="animate-bottom">

  
  <!-- First Container -->
  <div class="container-fluid bg-1 text-center">
    <h3 class="margin">Covid-19 Tracker</h3>
    <?php if(!empty($this->session->getFlashdata('error'))){ ?>

      <div class="alert alert-danger">
  <strong>Error!</strong>  <?php echo $this->session->getFlashdata('error');?>
</div>
    <?php } ?>
    <?php /* if(!empty($this->session->getFlashdata('success'))){ ?>
      <div class="alert alert-success">
  <strong>Success!</strong>  <?php echo $this->session->getFlashdata('success');?>  
</div>

<?php } */ ?>  
  </div>

  <div class="container  text-center">
    <h3 class="margin"></h3> 
    <div class="row">


      <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-primary">
          <div class="inner">
            <p>Total</p>
            <h3><?php //print_r($rsLastMonth);
            if(!empty($rsLastMonth)){
               echo $rsLastMonth[0]['total_new_infections'] + 
               $rsLastMonth[0]['total_new_deaths'] + 
               $rsLastMonth[0]['total_new_recovered'];
            } ?></h3>
            <p>cases for the last one month</p>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-primary">
          <div class="inner">
          <p>Total</p>
            <h3><?php //print_r($rsLastWeek);
            if(!empty($rsLastWeek)){
               echo $rsLastWeek[0]['total_new_infections'] + 
               $rsLastWeek[0]['total_new_deaths'] + 
               $rsLastWeek[0]['total_new_recovered'];
            } ?></h3>
            <p>cases for the last one week</p>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-primary">
          <div class="inner">
          <p>Total</p>
            <h3><?php //print_r($rsLastDay);
            if(!empty($rsLastDay)){
               echo $rsLastDay[0]['total_new_infections'] + 
               $rsLastDay[0]['total_new_deaths'] + 
               $rsLastDay[0]['total_new_recovered'];
            } ?></h3>
            <p>cases for the last day</p> 
          </div>
        </div>
      </div>
      
      <!-- ./col -->  
      <!-- ./col -->
    </div>

  </div>

  <!-- Second Container -->
  <div class="container  text-center">
    <br>
    <h3 class="margin">Covid-19 data pointer</h3> 
    <div class="col-md-12">
      <form id="filter_form" method="post" action="<?=base_url();?>/get-filter-data" class="form-inline">
      <div class="col-sm-4">
      <div class="form-group">
    <label for="startDate">From:</label>
    <input type="text" name="startDate" value="<?=$startDate;?>" class="form-control datepicker" 
    id="startDate" data-date-format="mm/dd/yyyy" required>
  </div>
      </div>
      <div class="col-sm-4">
      <div class="form-group">
    <label for="endDate">To:</label>
    <input type="text" name="endDate" value="<?=$endDate;?>" class="form-control datepicker" 
    id="endDate" required>
  </div>
      </div>
      <div class="col-sm-4">
      <div class="form-group">
    <label for="countryCode">Country</label> 
    <select class="form-control" id="countryCode" name="countryCode" required>
      <option value="IN" <?php if($countryCode=='IN'){echo 'selected';} ?>>India</option>
      <option value="US" <?php if($countryCode=='US'){echo 'selected';} ?>>USA</option>
    </select>
  </div>
      </div>
      </form>
    </div>
    <div class="clearfix"></div>
    <br>
    <div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-primary">
          <div class="inner">
            <h3><?php
            if(!empty($rsDataByFilterRange)){
               echo $rsDataByFilterRange[0]['total_new_infections'] + 
               $rsDataByFilterRange[0]['total_new_deaths'] + 
               $rsDataByFilterRange[0]['total_new_recovered'];
            } ?></h3>
            <p>Total affected</p>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-orange">
          <div class="inner">
            <h3><?php
            if(!empty($rsDataByFilterRange)){
               echo $rsDataByFilterRange[0]['total_new_infections'];
            } ?></h3>
            <p>Total new cases</p>
          </div>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-danger">
          <div class="inner">
            <h3><?php
            if(!empty($rsDataByFilterRange)){
               echo $rsDataByFilterRange[0]['total_new_deaths'];
            } ?></h3>
            <p>Total death</p>
          </div>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php
            if(!empty($rsDataByFilterRange)){
               echo $rsDataByFilterRange[0]['total_new_recovered'];
            } ?></h3>
            <p>Total recovered</p>
          </div>
          </div>
      </div>
      <!-- ./col -->  
      <!-- ./col -->
    </div>

  </div>
	</div>
  
  

  <!-- Footer -->
  <footer class="container-fluid bg-4 text-center" id="footer">  
    <p>Covid19 Tracker Made By <a href="www.linkedin.com/in/bhagabat-behera-the-fullstack-software-developer">Bhagabat Behera</a></p> 
  </footer> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" ></script>
<script>

$('#startDate').change(function(){
      var startDate=$(this).val();
      if(startDate==''){
        $('#startDate').focus();
      }else{
        $('.datepicker-dropdown').hide();
        $('#filter_form').submit();
      }
      
      });

      $('#endDate').change(function(){
      var endDate=$(this).val();
      if(endDate==''){
        $('#endDate').focus();
      }else{
        $('.datepicker-dropdown').hide();
        $('#filter_form').submit();  
      }
      
      });

      $('#countryCode').change(function(){
      var countryCode=$(this).val();
      if(countryCode==''){
        $('#countryCode').focus();
      }else{
        $('.datepicker-dropdown').hide();
        $('#filter_form').submit();
      }
      
      });


   $('.datepicker').datepicker({
    format: 'yyyy-mm-dd'
   });

  

var myVar;

function myFunction() {
  document.getElementById("footer").style.marginTop = "30%";
  myVar = setTimeout(showPage, 3000);
}

function showPage() {
  document.getElementById("footer").style.marginTop = "20px";
  document.getElementById("loader").style.display = "none";
  document.getElementById("myDiv").style.display = "block";
}
</script>
 

</body>
</html>


