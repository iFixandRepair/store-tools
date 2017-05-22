function onSignIn(googleUser) {
	var id_token 		= googleUser.getAuthResponse().id_token;
	var profile 		= googleUser.getBasicProfile();
	var options;
	var tableSG			= '';
	var tableVR			= '';	
	var TabActual 		= '';
	var TRange=null;
	function print_r(o)
	{
		var salida = '';
		for (var p in o) {
		salida += p + ': ' + o[p] + '\n';
		}
		console.log(salida);
	}


	/******************************************************************/
	/************ VALIDO CORREOS PERMITIDOS PARA PESTAÑAS *************/
	/******************************************************************/	
	if(profile.getEmail()=='webdeveloper@ifixandrepair.com' || profile.getEmail()=='systems@ifixandrepair.com') //Julian y Danilo
	{
		$("#tabFiles").show();
		$("#tabGoals").show();
	}
	if(profile.getEmail()!='pm2@ifixandrepair.com') // Agelica Jacome
	{
		$("#tabGoals").show();
	}
	if(profile.getEmail()!='ap2@ifixandrepair.com') // Pilar Castaño
	{
		$("#tabFiles").show();
	}
	/******************************************************************/
	/******************************************************************/

	$("#loginContainer").hide();
	$("#mainContainer").show();

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var target = $(e.target).attr("href") // activated tab
		if (TabActual == '#storeGoals') 
		{		
			tableSG.destroy();
		}
		TabActual = target;
		if(target=='#DivGoals')
		{
			LoadGoals();
		}
	});

	function findString (str) 
	{
		if (parseInt(navigator.appVersion)<4) return;
		var strFound;
		if (window.find) 
		{
			// CODE FOR BROWSERS THAT SUPPORT window.find
			strFound=self.find(str);
			print_r(self);
			if (!strFound) {
				strFound=self.find(str,0,1);
				while (self.find(str,0,1)) continue;
			}
		}
		else if (navigator.appName.indexOf("Microsoft")!=-1) 
		{
			alert('navigator.appName')
			alert(navigator.appName)
			// EXPLORER-SPECIFIC CODE
			if (TRange!=null) {
				TRange.collapse(false);
				strFound=TRange.findText(str);
				if (strFound) TRange.select();
			}
			if (TRange==null || strFound==0) 
			{
				TRange=self.document.body.createTextRange();
				strFound=TRange.findText(str);
				if (strFound) TRange.select();
			}
		}
		else if (navigator.appName=="Opera") 
		{
			alert('Opera')
			alert ("Opera browsers not supported, sorry...")
			return;
		}
		if (!strFound) alert ("String '"+str+"' not found!")
		return;
	}
	$("#btnSearch").click(
		function(){ 
	        var texto 	= $('#txtSearch').val();
			alert(texto)
		  	findString (texto);	    	   	
	});

	$("#btn-upload").click(
		function(){ 
		//información del formulario
        var formData 	= new FormData($(".formulario-us")[0]);
        var message 	= ""; 
        //hacemos la petición ajax  
        $.ajax({
            url			: 'services/upload-sales.php',  
            type 		: 'POST',
            // Form data
            //datos del formulario
            data 		: formData,
            //necesario para subir archivos via ajax
            cache 		: false,
            contentType : false,
            processData : false,
            //mientras enviamos el archivo
            beforeSend 	: function(){
                message = "Uploading Files, Please Wait...";
                showMessage(message,'info')        
            },
            //una vez finalizado correctamente
            success: function(data){  
                        	
            	data = JSON.parse(data);
                message = "<span class='error'>An error has occurred, Please Try Again.</span>";
            	tipoMsg = "danger";
            	if (typeof data.success !== 'undefined') 
            	{				  
                	if (data.success==1) 
	            	{	
	            		var message1 = 0;
	            		var message2 = 0;
	            		data = data.data;
	            		print_r(data);
	            		if (typeof data.LocationSales !== 'undefined') 
            			{

            				if (data.LocationSales==1) 
	            			{
            					tipoMsg 	= "success";
	            			}
	            			else
	            			{
	            				message1 	= "Error Processing, Check that the data does not exist in the system";
	            			}
            			}	
            			if (typeof data.EmployeeSales !== 'undefined') 
            			{
            				if (data.EmployeeSales==1) 
	            			{
            					tipoMsg 	= "success";
	            			}
	            			else
	            			{
	            				message2 	= "Error Processing, Check that the data does not exist in the system";
	            			}
            			}
            			if(message1==0 && message2==0 )	
            			{
            				message = "Files uploaded successfully";
            			}
            			else 
            			{
            				message = "Files uploaded, Some of the files had problems:<br>";
            				if(message1!=0)
            				{
            					message+='<br><b>Sales by Location:</b> '+message1;
            				}
            				if(message2!=0)
            				{
            					message+='<br><b>Sales by Employee:</b> '+message2;
            				}
            			}		
					}
                	else
	            	{				  
	                	message = "Error Uploading, Please Make sure the files are correct, and are not in the system";
            			tipoMsg = "danger";
					}
				}
				showMessage(message, tipoMsg);
				
                
            },
            //si ha ocurrido un error
            error: function(){
                message = "An error has occurred, Please Try Again.";
                showMessage(message,'danger');
            }
        });        
	});

	$("#btn-upload-SG").click(
		function(){ 
		//información del formulario
        var formData 	= new FormData($(".formulario-sg")[0]);
        var message 	= ""; 
        //hacemos la petición ajax  
        $.ajax({
            url			: 'services/upload-store-goals.php',  
            type 		: 'POST',
            // Form data
            //datos del formulario
            data 		: formData,
            //necesario para subir archivos via ajax
            cache 		: false,
            contentType : false,
            processData : false,
            //mientras enviamos el archivo
           beforeSend 	: function(){
                message = "Uploading File, Please Wait...";
                showMessage(message,'info')        
            },
            //una vez finalizado correctamente
            success: function(data)
            {
            	data = JSON.parse(data);
            	message = "An error has occurred, Please Try Again.";
            	tipoMsg = "danger";
            	if (typeof data.success !== 'undefined') 
            	{				  
            		message = "An error has occurred, Please Try Again.";
            		tipoMsg = "danger";
                	if (data.success==1) 
	            	{				  
	                	message = "File uploaded successfully.";	
	                	tipoMsg = "success";                	
	            		$('#store-goals-table').show();
						$('#FormUploadStoreGoals').hide();	 
						LoadGoals ()
					}
				}
                showMessage(message,tipoMsg);
            },
            //si ha ocurrido un error
            error: function(){
                message = "An error has occurred, Please Try Again.";
                showMessage(message,'danger');
            }
        });        
	});

	$("#btn-generateReport").click(
		function(){ 
		//información del formulario
        var date 	= $('#date').val()	
		var typeRep	= $('input[name="typeRep"]:checked').val();
        var message = ""; 
        
        if (typeof date == 'undefined' || date =='') 
        {
        	message = 'Please Fill the Date';
        	showMessage(message,'warning');
        }        
        else if (typeof typeRep == 'undefined' || typeRep =='') 
        {
        	message = 'Please Select a type of report';
        	showMessage(message,'warning');
        }
        else
	    {
			GenerarReporteVR (date,typeRep)
	             
	   	}
	});

	function showMessage(message,type)
	{
		/*info
		success
		warning
		danger*/
		//console.log("Tab: "+tab+message)
	   /*$("#messages-"+tab).html("").show();
	    $("#messages-"+tab).html(message);*/
	    icon = type;
	    if(icon=='success')
	    {
	    	icon = 'ok'
	    }
	    if(icon=='danger')
	    {
	    	icon = 'remove'
	    }
	    $.notify({
			// options
			icon: 'glyphicon glyphicon-'+icon+'-sign',
			//title: 'Bootstrap notify',
			message: message,
			//url: 'https://github.com/mouse0270/bootstrap-notify',
			//target: '_blank'
		},{
			// settings
			element: 'body',
			position: null,
			type: type,
			allow_dismiss: true,
			newest_on_top: true,
			showProgressbar: false,
			placement: {
				from: "top",
				align: "center"
			},
			offset: 20,
			spacing: 10,
			z_index: 1031,
			delay: 5000,
			timer: 1000,
			animate: {
				enter: 'animated fadeInDown',
				exit: 'animated fadeOutUp'
			},			
			icon_type: 'class',
			template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
				'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
				'<span data-notify="icon"></span> ' +
				'<span data-notify="title">{1}</span> ' +
				'<span data-notify="message">{2}</span>' +
				'<div class="progress" data-notify="progressbar">' +
					'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
				'</div>' +
				'<a href="{3}" target="{4}" data-notify="url"></a>' +
			'</div>' 
		});
	}

	function GenerarReporteVR (date,typeRep)
	{
		$("#GOAL_stores").html('');
		$.ajax({
	        url			: 'services/operations-vr.php',  
	        type 		: 'POST',
	        // Form data
	        //datos del formulario           
	        data 		: 
	        {
				opt 		: 'get' 
				,date 		: date
				,typeRep 	: typeRep
	        },
	        //mientras enviamos el archivo
	        beforeSend 	: function(){
	            message = "Generating Report, Please Wait...";
	            showMessage(message,'info')        
	        },
	        //una vez finalizado correctamente
	        success: function(data){
	        	data = JSON.parse(data);
	        	if (typeof data.data !== 'undefined') 
	        	{				  
	            	if (data.data!='') 
	            	{
	            		/*employee_id: 684
						employee_name: Cassandra Craven
						EMPL_GOAL_GP: 1000
						EMPL_ACTUAL_GP: 779
						EMPL_LAST_DATE: 2017-05-15
						EMPL_TREND: 160.9933
						EMPL_NEEDED: 32.2581
						store_id: 19
						store_name: Wiregrass - 19

						STOR_GOAL_GP: 1000
						STOR_ACTUAL_GP: 1608
						STOR_LAST_DATE: 2017-05-15
						STOR_TREND: 332.3200
						STOR_NEEDED: 32.2581*/
	            		data = data.data;	
	            		if(typeRep==1)
	            		{
	            			descTypeRep = 'Stores - Employees';
	            			var colorEncabezados = '#c0c0c0';						
		            		for (i = 0; i < data.length; ++i) 
		            		{
		            			registro = data[i];
		            			//print_r(registro);
		            			name_UL = registro.store_id;
		            			if ( $("#"+name_UL).length > 0 ) 
		            			{
									// hacer algo aquí si el elemento existe
									$("#"+name_UL).append('<li id="'+registro.employee_id+'" style="padding:5px"><div class="col-md-2">'+registro.employee_name+'</div><div class="col-md-2">'+registro.EMPL_LAST_DATE+'</div><div class="col-md-2">'+registro.EMPL_GOAL_GP+'</div><div class="col-md-2">'+registro.EMPL_ACTUAL_GP+'</div><div class="col-md-2">'+Math.round(registro.EMPL_TREND)+' %</div><div class="col-md-2">'+Math.round(registro.EMPL_NEEDED)+'</div></li>')
								}
								else
								{
									$("#GOAL_stores").append('<div class="col-md-12" style="background-color:'+colorEncabezados+'"><div class="col-md-2">Store Name</div><div class="col-md-2">Last Date Available</div><div class="col-md-2">Store Goal GP</div><div class="col-md-2">Actual GP</div><div class="col-md-2">Trend to Goal</div><div class="col-md-2">Needed Per Day</div></div>');
									$("#GOAL_stores").append('<div class="col-md-12"><div class="col-md-2">'+registro.store_name+'</div><div class="col-md-2">'+registro.STOR_LAST_DATE+'</div><div class="col-md-2">'+registro.STOR_GOAL_GP+'</div><div class="col-md-2">'+registro.STOR_ACTUAL_GP+'</div><div class="col-md-2">'+Math.round(registro.STOR_TREND)+' %</div><div class="col-md-2">'+Math.round(registro.STOR_NEEDED)+'</div>');
									$("#GOAL_stores").append('<div class="col-md-12"><ul id="'+name_UL+'" class="sortableVR connectedSortableVR col-md-12" style="list-style-type: none;"><li style="padding:5px;"><div style="background-color:'+colorEncabezados+'" class="col-md-2">Employee Name</div><div style="background-color:'+colorEncabezados+'" class="col-md-2">Last Date Available</div><div style="background-color:'+colorEncabezados+'" class="col-md-2">Employee Goal GP</div><div style="background-color:'+colorEncabezados+'" class="col-md-2">Actual GP</div><div style="background-color:'+colorEncabezados+'" class="col-md-2">Trend to Goal</div><div style="background-color:'+colorEncabezados+'" class="col-md-2">Needed Per Day</div></li></ul></div>');
									//$("#GOAL_stores").append('<div class="col-md-12"><table><tr><td>Store Name</td><td>Store Goal GP</td><td>Last Date Available</td><td>Trend to Goal</td><td>Needed Per DAy</td></tr><tr><td>'+registro.store_name+'</td><td>'+registro.STOR_GOAL_GP+'</td><td>'+registro.STOR_LAST_DATE+'</td><td>'+registro.STOR_TREND+' %</td><td>'+registro.STOR_NEEDED+'</td></tr><td colspan="5"><ul id="'+name_UL+'" class="sortableVR connectedSortableVR col-md-12"></ul></td></tr></table></div>');
								}
							}
	            		}	
	            		if(typeRep==2)
	            		{
	            			descTypeRep = 'Stores';
	            			var colorEncabezados = '#c0c0c0';	
	            			$("#GOAL_stores").append('<div class="col-md-12" style="background-color:'+colorEncabezados+'"><div class="col-md-2">Store Name</div><div class="col-md-2">Last Date Available</div><div class="col-md-2">Store Goal GP</div><div class="col-md-2">Actual GP</div><div class="col-md-2">Trend to Goal</div><div class="col-md-2">Needed Per Day</div></div>');
													
		            		for (i = 0; i < data.length; ++i) 
		            		{
		            			registro = data[i];
		            			name_UL = registro.store_id;
		            			if ( $("#"+name_UL).length == 0 ) 
		            			{
									$("#GOAL_stores").append('<div id="'+registro.store_id+'" class="col-md-12"><div class="col-md-2">'+registro.store_name+'</div><div class="col-md-2">'+registro.STOR_LAST_DATE+'</div><div class="col-md-2">'+registro.STOR_GOAL_GP+'</div><div class="col-md-2">'+registro.STOR_ACTUAL_GP+'</div><div class="col-md-2">'+Math.round(registro.STOR_TREND)+' %</div><div class="col-md-2">'+Math.round(registro.STOR_NEEDED)+'</div>');
								}
							}
	            		}	
	            		if(typeRep==3)
	            		{
	            			descTypeRep = 'Employees';
	            			var colorEncabezados = '#c0c0c0';	
	            			$("#GOAL_stores").append('<div class="col-md-12" style="background-color:'+colorEncabezados+'"><div class="col-md-2">Employee Name</div><div class="col-md-2">Last Date Available</div><div class="col-md-2">Store Goal GP</div><div class="col-md-2">Actual GP</div><div class="col-md-2">Trend to Goal</div><div class="col-md-2">Needed Per Day</div></div>');
													
		            		for (i = 0; i < data.length; ++i) 
		            		{
		            			registro = data[i];
		            			name_UL = registro.employee_id;
		            			if ( $("#"+name_UL).length == 0 ) 
		            			{
									$("#GOAL_stores").append('<div id="'+registro.employee_id+'" class="col-md-12"><div class="col-md-2">'+registro.employee_name+'</div><div class="col-md-2">'+registro.EMPL_LAST_DATE+'</div><div class="col-md-2">'+registro.EMPL_GOAL_GP+'</div><div class="col-md-2">'+registro.EMPL_ACTUAL_GP+'</div><div class="col-md-2">'+Math.round(registro.EMPL_TREND)+' %</div><div class="col-md-2">'+Math.round(registro.EMPL_NEEDED)+'</div></div>');
								}
							}
	            		}	
	            		message = "Store Goals Loaded Successfully.";
	            		tipoMsg = "success";


						/*$( function() {
						    $( ".sortableVR" ).sortable({
						      connectWith: ".connectedSortableVR"
						    }).disableSelection();
					  	} );*/
	            		
					}
					else
					{
						message = "Please upload Store Goals...";
	            		tipoMsg = "danger";
						$('#store-goals-table').hide();
						$('.formulario-sg').show();
					}
				}
				else
				{
					message = "An error has occurred, Please Try Again.";
					tipoMsg = "danger";
				}
				showMessage(message,tipoMsg);
	        },
	        //si ha ocurrido un error
	        error: function(){
	            message = "An error has occurred, Please Try Again.";
	            showMessage(message,'danger');
	        }
	    }); 
	}

	function LoadGoals ()
	{
		$.ajax({
            url			: 'services/operations.php',  
            type 		: 'POST',
            data 		: { 
            				method 	: 'goals'
            				,opt 	: 'load' 
            			},
            beforeSend 	: function()
            {
                message = "Loading Store Goals, Please Wait...";
                showMessage(message,'info')        
            },
            //una vez finalizado correctamente
            success: function(data)
            {
            	data = JSON.parse(data);
            	if (typeof data.data !== 'undefined') 
            	{				  
                	if (data.data!='') 
	            	{
	            		data=data.data;
	            		message = "Store Goals Loaded Successfully.";
                		showMessage(message,'success'); 
                		/*$("#divSearch").append('<form id="f1" name="f1" action="javascript:findString(document.getElementById(t1).value)" >');
						$("#divSearch").append('<input id="t1" name="t1" value="text" size="20" type="text">');
						$("#divSearch").append('<input name="b1" value="Find" type="submit">');
						$("#divSearch").append('</form>');*/

	            		for (i = 0; i < data.length; ++i) 
	            		{
	            			registro = data[i];
	            			name_UL = registro.store_id;
	            			if ( $("#"+name_UL).length > 0 ) 
	            			{
								// hacer algo aquí si el elemento existe
								$("#"+name_UL).append('<li  id="'+registro.employee_id+'"class="ui-state-default">'+registro.employee_name+'</li>')
							}
							else
							{
								$("#GOAL_stores").append('<ul id="'+name_UL+'" class="sortable connectedSortable col-md-3">'+registro.rq_name+'</ul>')
							}
						}
						 $( function() {
					    $( ".sortable" ).sortable({
					      connectWith: ".connectedSortable"
					    }).disableSelection();
					  } );
	            		/* VALIDO SI LA LISTA DE LA TIENDA EXISTE */
	            				  
	                	/*tableSG = $('#store-goals-table').DataTable( 
		            	{
		            		aLengthMenu		: [[25, 50, 75, -1], [25, 50, 75, "All"]],
        					iDisplayLength	: 25,
					        data 			: data.data,
					        columns 		: 
					        [	
					            { "data": "Id",  "bVisible": false	},
					            { "data": "Month" 					},
					            { "data": "Store" 					},
					            { "data": "Manager" 				},
					            { "data": "Hours" 					},
					            { "data": "Hrs_mgr" 				},
					            { "data": "Hrs_emp" 				},
					            {
									"className"		: 'Edit-control',
									"orderable"		: false,
									"data"			: null,
									"defaultContent": '<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>'
								}
					        ]
					    } );
					    message = "Store Goals Loaded Successfully.";
                		showMessage(message,'success') 

					    $('#store-goals-table .Edit-control').on('click', function(){
				        	//print_r(tableSG.row(this).data());
					        var arrRow = tableSG.row(this).data();
					        //alert( 'You clicked on '+arrRow['Store']+'\'s row' );
					        $('#EditStoreGoal #MDLSG_Id').val(arrRow['Id']);
					        $('#EditStoreGoal #NameStoreGoal').html(arrRow['Store']);
					        $('#EditStoreGoal #MDLSG_Manager').html(arrRow['Manager']);
					        $('#EditStoreGoal #MDLSG_Hours').html(arrRow['Hours']);
					        $('#EditStoreGoal #MDLSG_MgrHrs').val(arrRow['Hrs_mgr']);
					        $('#EditStoreGoal #MDLSG_EmpHrs').val(arrRow['Hrs_emp']);

					        $('#EditStoreGoal #MDLSG_MgrHrs').keyup(function() {
					        	var MgrHrs = ($('#EditStoreGoal #MDLSG_MgrHrs').val()*1);
					        	var EmpHrs = ($('#EditStoreGoal #MDLSG_EmpHrs').val()*1);
					        	var valorSum = MgrHrs + EmpHrs; 
								$('#EditStoreGoal #MDLSG_Hours').html(valorSum);
							});
					        $('#EditStoreGoal #MDLSG_EmpHrs').keyup(function() {
					        	var MgrHrs = ($('#EditStoreGoal #MDLSG_MgrHrs').val()*1);
					        	var EmpHrs = ($('#EditStoreGoal #MDLSG_EmpHrs').val()*1);
					        	var valorSum = MgrHrs + EmpHrs; 
								$('#EditStoreGoal #MDLSG_Hours').html(valorSum);
							});
					        $('#EditStoreGoal').modal('show');

					        $("#btn-save_MDLSG").click(
							function()
							{ 
								var Id 			= $('#EditStoreGoal #MDLSG_Id').val()
				        		var MgrHrs 		= ($('#EditStoreGoal #MDLSG_MgrHrs').val()*1);
				        		var EmpHrs 		= ($('#EditStoreGoal #MDLSG_EmpHrs').val()*1);
								var Hours 		= MgrHrs + EmpHrs; 	
						        $.ajax({
						            url			: 'services/operations-sg.php',  
						            type 		: 'POST',
						            // Form data
						            //datos del formulario
						            data 		: 
						            {
			            				opt 	: 'update' 
			            				,Id 	: Id
			            				,Hours 	: Hours
			            				,MgrHrs : MgrHrs
			            				,EmpHrs : EmpHrs
						            },
						            //mientras enviamos el archivo
						           beforeSend 	: function(){
						                message = "Saving, Please Wait...";
						                showMessage(message,'info')        
						            },
						            //una vez finalizado correctamente
						            success: function(data)
						            {
						            	data 	= JSON.parse(data);
						            	message = "An error has occurred, Please Try Again.";
            							tipoMsg = "danger";
						            	if (typeof data.success !== 'undefined') 
						            	{	
						                	if (data.success==1) 
							            	{				  
							                	message = "File saved successfully.";
            									tipoMsg = "success";
							                	$('#EditStoreGoal').modal('hide');
												$( "#btn-save_MDLSG").off();							                	
												$( "#store-goals-table .Edit-control").off();
												tableSG.destroy();
												LoadGoals ()
											}
											else
											{
												message = "An error saving has occurred, Please Try Again.";
            									tipoMsg = "danger";
											}
										}		
										showMessage(message,tipoMsg);				                
						            },
						            //si ha ocurrido un error
						            error: function(){
						                message = "An error has occurred, Please Try Again.";
						                showMessage(message,'danger');
						            }
						        }); 							           
							});
					    } ); */ 
					}
					else
					{
						message = "Please upload Store Goals...";
               			showMessage(message,'info') 
						$('#store-goals-table').hide();
						$('.formulario-sg').show();
					}
				}         	 
            },
            //si ha ocurrido un error
            error: function(){
                message = "An error has occurred, Please Reload.";
                showMessage(message,'danger');
            }
        });  
	}
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
	auth2.signOut().then(function () {
		$("#mainContainer").hide();
		$("#loginContainer").show();
    });
	
}