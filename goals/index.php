<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="google-signin-client_id" content="219956410475-n3rfhtl3giomu4j10lg1g7o7u8sqb0c1.apps.googleusercontent.com">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->
        <script src="https://apis.google.com/js/platform.js" async defer></script>
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/t/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.11,b-1.1.2,b-flash-1.1.2,b-html5-1.1.2,b-print-1.1.2/datatables.min.css"/>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="../lib/BootstrapDatepicker/css/bootstrap-datepicker.css">
		<link rel="stylesheet" type="text/css" href="../lib/BootstrapCombobox/css/bootstrap-combobox.css">
		<link rel="stylesheet" type="text/css" href="../lib/BootstrapNotify/animate.css">


        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
        
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
		<div class="container" id="loginContainer">		  
			<div class="panel panel-default">
				<div class="panel-heading">Store Tools - Sales Report - Login</div>
					<div class="panel-body">
						<p class="center-block">Please use your store email address to login.</p>
						<div class="btn g-signin2 center-block" data-onsuccess="onSignIn"></div>
					</div>
				</div>
			</div>
        <div class="container" id="mainContainer" style="display:none;">
	        <nav class="navbar navbar-default">
				<div class="container-fluid">
					<a class="navbar-brand" href="#">Store Tools - Sales Report</a>
					<ul class="nav navbar-nav navbar-right">
						<li><p class="navbar-text" id="store-name"></p></li>
						<li><a class="btn" href="#" onclick="signOut();">[ Sign out ]</a></li>
					</ul>
				</div>
			</nav>
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" id="tabStatistics" class="active">			<a href="#DivStatistics" 	aria-controls="home" role="tab" data-toggle="tab">Statistics</a></li>
				<li role="presentation" id="tabFiles" style=" display:none;">		<a href="#DivFiles" 	aria-controls="home" role="tab" data-toggle="tab">Upload Files</a></li>
				<li role="presentation" id="tabGoals" style=" display:none;">		<a href="#DivGoals" 	aria-controls="home" role="tab" data-toggle="tab">Goals</a></li>
			</ul>
			<div class="tab-content">				
				<!-- View Report Tab -->
				<div role="tabpanel" class="tab-pane fade in active" id="DivStatistics">
					<div id="messages-vr" name="messages-vr" class="messages-vr"></div>		
					<form  method="post" enctype="multipart/form-data" class="form-inline navbar navbar-default formulario-vr" id="formulario-vr"> 
						<div class="row">
							<div class="col-md-12" >
								&nbsp;			
							</div>
						</div>	
						<div class="row">
							<div class="col-md-offset-1" >
								<div class="col-md-3">
									Initial Date
								</div>
								<div class="col-md-3">
									End Date
								</div>
								<div class="col-md-3">
									Type Report					
								</div>	
								<div class="col-md-1">
									&nbsp;			
								</div>					
							</div>
						</div>
						<div class="row">
							<div class="col-md-offset-1" >
								<div id="Init_date-container" class="col-md-3">
									<div class="input-group date">
										<input class="form-control" type="text" id="Init_date" name="Init_date" value="<?php echo date('m/d/Y', time() - 60 * 60 * 24); ?>" placeholder="Init Date">
										<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
									</div>
								</div>
								<div id="End_date-container" class="col-md-3">
									<div class="input-group date">
										<input class="form-control" type="text" id="End_date" name="End_date" value="<?php echo date('m/d/Y', time() - 60 * 60 * 24); ?>" placeholder="End Date">
										<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
									</div>
								</div>
								<div class="col-md-3">
									<div class="btn-group" data-toggle="buttons">
										<label class="btn btn-primary active">
											<input type="radio" name="typeRep" id="typeRep" value="1" autocomplete="off" checked />Day
										</label>
										<label class="btn btn-primary">
											<input type="radio" name="typeRep" id="typeRep" value="2" autocomplete="off">Week
										</label>
										<label class="btn btn-primary">
											<input type="radio" name="typeRep" id="typeRep" value="3" autocomplete="off">Month
										</label>
									</div>							
								</div>	
								<div class="col-md-1">
									<input type="button" id="btn-generateReport" value="Generate" name="btn-upload" class="btn btn-primary">					
								</div>					
							</div>
						</div>
						<div class="row">
							<div class="col-md-12" >
								&nbsp;			
							</div>
						</div>
					</form>

					<div class="modal fade" id="AddStoreComment">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h3 class="modal-title">Comments for<span id="NameStoreComment"></span></h3>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<div class="container-fluid">
										<input type="hidden" id="MDLVR_Store" name="MDLVR_Store">
										<div class="row">
											<div class="col-md-4">
												<strong>Message:</strong>
											</div>
											<div class="col-md-8">
												<textarea id="MDLVR_Message" name="MDLVR_Message" rows="4" cols="50"></textarea>
											</div>
										</div>	
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" id="btn-save_MDLVR" class="btn btn-primary">Save</button>
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>

					<table id="view-report-table" class="table table-striped">
						<thead>
							<tr>
								<th>Date</th>
								<th>Store</th>
								<th>Manager</th>
								<th>S</th>
								<th>Sales</th>
								<th>Accesories</th>
								<th>Gross Profit</th>
								<th>Hours</th>
								<th>Budget</th>
								<th>Plus/Minus</th>
								<th>OT</th>
								<th>MGR Req Hours</th>
								<th>MGR HRS</th>
								<th>Plus/ Minus</th>
								<th>Comments</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div><!-- /View Report Tab -->

				<!-- Upload Sales Tab -->	
				<div role="tabpanel" class="tab-pane fade" id="DivFiles">	
					<div id="messages-us" name="messages-us" class="messages-us"></div>			
					<form  method="post" enctype="multipart/form-data" class="form-inline  navbar navbar-default formulario-us">
<<<<<<< HEAD
<<<<<<< HEAD
						<div class="form-group">							
							<div class="row">
								<div class="col-md-4">
									<label for="salesReport">Sales by Location</label>
								</div>
								<div class="col-md-8">
									<input type="file" class="form-control" name="LocationSales" id="LocationSales">
=======
=======
>>>>>>> origin/master
						<div class="form-group">
							<div class="row">
								<div class="col-md-4">
									<label for="salesReport">Date Sales</label>
								</div>
								<div id="date-sales-container" class="col-md-8">
									<div class="input-group date">
										<input class="form-control" type="text" id="date-sales" name="date-sales">
										<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label for="salesReport">Sales File</label>
								</div>
								<div class="col-md-8">
									<input type="file" class="form-control" name="salesReport" id="salesReport">
<<<<<<< HEAD
>>>>>>> origin/master
=======
>>>>>>> origin/master
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
<<<<<<< HEAD
<<<<<<< HEAD
									<label for="EmployeeSales">Sales by Employee/label>
								</div>
								<div class="col-md-8">
									<input type="file" class="form-control" name="EmployeeSales" id="EmployeeSales">
								</div>
							</div>							
=======
=======
>>>>>>> origin/master
									<label for="accesoriesReport">Accesories File</label>
								</div>
								<div class="col-md-8">
									<input type="file" class="form-control" name="accesoriesReport" id="accesoriesReport">
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label for="locationPayroll">Location Payroll</label>
								</div>
								<div class="col-md-8">
									<input type="file" class="form-control" name="locationPayroll" id="locationPayroll">
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label for="AutoPunchOut">Auto Punch Out</label>
								</div>
								<div class="col-md-8">
									<input type="file" class="form-control" name="AutoPunchOut" id="AutoPunchOut">
								</div>
							</div>
<<<<<<< HEAD
>>>>>>> origin/master
=======
>>>>>>> origin/master
							<div class="row">
								<div class="col-md-12">
									<input type="button" id="btn-upload" value="Upload" name="btn-upload" class="btn btn-primary">
								</div>
							</div>
						</div>
					</form>
				</div><!-- /Upload Sales Tab -->

				<!-- Store Goals Tab -->
				<div role="tabpanel" class="tab-pane fade" id="DivGoals">	
					<div id="messages-sg" name="messages-sg" class="messages-sg"></div>		
					
					<style>
						.sortable
						{
							border: 1px solid #eee;
							/*min-width: 350px;*/
							min-height: 280px;
							list-style-type: none;
							/*margin: 0;
							padding: 5px 0 0 0;
							float: left;
							margin-right: 10px;*/
						}
						.sortable li
						{
							/*margin: 0 5px 5px 5px;
							padding: 5px;
							min-width: 340px;
							font-size: 12px;
							width: 120px;*/
						}
					</style>
					<input id="txtSearch" name="txtSearch" value="" size="20" type="text">
					<input id="btnSearch" name="btnSearch" value="Search" type="button">
					<div id="GOAL_stores">
					<!--	<ul id="sortable1" class="sortable1 connectedSortable">
					store 1
						<li class="ui-state-default">Item 1</li>
						<li class="ui-state-default">Item 2</li>
						<li class="ui-state-default">Item 3</li>
						<li class="ui-state-default">Item 4</li>
						<li class="ui-state-default">Item 5</li>
					</ul>

					<ul id="sortable2" class="sortable2 connectedSortable">
					store 2
						<li class="ui-state-highlight">Item 1</li>
						<li class="ui-state-highlight">Item 2</li>
						<li class="ui-state-highlight">Item 3</li>
						<li class="ui-state-highlight">Item 4</li>
						<li class="ui-state-highlight">Item 5</li>
					</ul> -->
					</div>
					

					


					<div class="modal fade" id="EditStoreGoal">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h3 class="modal-title">Edit Store Goals For <span id="NameStoreGoal"></span></h3>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<div class="container-fluid">
										<input type="hidden" id="MDLSG_Id" name="MDLSG_Id">
										<div class="row">
											<div class="col-md-4">
												<strong>Manager:</strong>
											</div>
											<div class="col-md-8">
												<span id="MDLSG_Manager"></span>
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<strong>Hours:</strong>
											</div>
											<div class="col-md-8">
												<span id="MDLSG_Hours" name="MDLSG_Hours"></span>
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<strong>Manager Hours:</strong>
											</div>
											<div class="col-md-8">
												<input type="text" id="MDLSG_MgrHrs" name="MDLSG_MgrHrs">
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<strong>Emplyee Hours:</strong>
											</div>
											<div class="col-md-8">
												<input type="text" id="MDLSG_EmpHrs" name="MDLSG_EmpHrs">
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" id="btn-save_MDLSG" class="btn btn-primary">Save</button>
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>


					<table id="store-goals-table" class="table table-striped">
						<thead>
							<tr>
								<th>Id</th>
								<th>Month</th>
								<th>Store</th>
								<th>Manager</th>
								<th>Hours</th>
								<th>Manager Hours</th>
								<th>Employee Hours</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div><!-- /Store Goals Tab-->
			</div>
		</div>
		
        <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.12.0.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
		<script src="https://cdn.datatables.net/t/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.11,b-1.1.2,b-flash-1.1.2,b-html5-1.1.2,b-print-1.1.2/datatables.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="../lib/BootstrapNotify/bootstrap-notify.js"></script>
        <script src="../lib/BootstrapDatepicker/js/bootstrap-datepicker.js"></script>
        <script src="../lib/BootstrapCombobox/js/bootstrap-combobox.js"></script>
        <script type="text/javascript" src="js/main.js?v=2"></script>
        <script type="text/javascript">
			$(document).ready(function()
			{
				$('#date-sales-container .input-group.date').datepicker({
				    autoclose: true,
				    todayHighlight: true,
				    toggleActive: true
				}); 
				$('#Init_date-container .input-group.date').datepicker({
				    autoclose: true,
				    todayHighlight: true,
				    toggleActive: true
				}); 
				$('#End_date-container .input-group.date').datepicker({
				    autoclose: true,
				    todayHighlight: true,
				    toggleActive: true
				}); 
				$('#cmdMonth').combobox();
			});
        </script>
        

    </body>
</html>
