function onSignIn(googleUser) {
	var id_token 		= googleUser.getAuthResponse().id_token;
	var profile 		= googleUser.getBasicProfile();
	var options;
	var tableSG			= '';
	var tableVR			= '';	
	var TabActual 		= '';

	/*var o = data;
var salida = '';
for (var p in o) {
salida += p + ': ' + o[p] + '\n';
}
alert(salida);*/
function print_r(o)
{
	var salida = '';
	for (var p in o) {
	salida += p + ': ' + o[p] + '\n';
	}
	console.log(salida);
}

	$("#loginContainer").hide();
	$("#mainContainer").show();

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var target = $(e.target).attr("href") // activated tab
		if (TabActual == '#storeGoals') 
		{		
			tableSG.destroy();
		}
		TabActual = target;
		if(target=='#storeGoals')
		{
			LoadStoreGoalsTable();
		}
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
                message = "<span class='before'>Uploading Files, Please Wait...</span>";
                showMessage(message,'us')        
            },
            //una vez finalizado correctamente
            success: function(data){            	
            	data = JSON.parse(data);
               /* message = "<span class='success'>Files uploaded successfully.</span>";
                showMessage(message,'us');*/
                message = "<span class='error'>An error has occurred, Please Try Again.</span>";
            	if (typeof data.success !== 'undefined') 
            	{				  
            		message = "<span class='error'>An error has occurred, Please Try Again.</span>";
                	if (data.success==1) 
	            	{				  
	                	message = "<span class='success'>Files uploaded successfully.</span>";
					}
				}
                showMessage(message,'us');
            },
            //si ha ocurrido un error
            error: function(){
                message = "<span class='error'>An error has occurred, Please Try Again.</span>";
                showMessage(message,'us');
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
                message = "<span class='before'>Uploading File, Please Wait...</span>";
                showMessage(message,'sg')        
            },
            //una vez finalizado correctamente
            success: function(data)
            {
            	data = JSON.parse(data);
            	message = "<span class='error'>An error has occurred, Please Try Again.</span>";
            	if (typeof data.success !== 'undefined') 
            	{				  
            		message = "<span class='error'>An error has occurred, Please Try Again.</span>";
                	if (data.success==1) 
	            	{				  
	                	message = "<span class='success'>File uploaded successfully.</span>";	                	
	            		$('#store-goals-table').show();
						$('#FormUploadStoreGoals').hide();	 
						LoadStoreGoalsTable ()
					}
				}
                showMessage(message,'sg');
            },
            //si ha ocurrido un error
            error: function(){
                message = "<span class='error'>An error has occurred, Please Try Again.</span>";
                showMessage(message,'sg');
            }
        });        
	});

	$("#btn-generateReport").click(
		function(){ 
		//información del formulario
        var date_ini 	= $('#Init_date').val()
		var date_end 	= $('#End_date').val()		
		var typeRep		= $('input[name="typeRep"]:checked').val();
        var message 	= ""; 
        
        if (typeof date_ini == 'undefined' || date_ini =='') 
        {
        	message = 'Please Fill the Init Date';
        	showMessage(message,'vr');
        }
        else if (typeof date_end == 'undefined' || date_end =='') 
        {
        	message = 'Please Fill the End Date';
        	showMessage(message,'vr');
        }
        else if (typeof typeRep == 'undefined' || typeRep =='') 
        {
        	message = 'Please Select a type of report';
        	showMessage(message,'vr');
        }
        else
	    {
	    	if (tableVR != '') 
			{
        		tableVR.destroy();
			}
	        //hacemos la petición ajax  
	        $.ajax({
	            url			: 'services/operations-vr.php',  
	            type 		: 'POST',
	            // Form data
	            //datos del formulario           
	            data 		: 
	            {
					opt 		: 'get' 
					,date_ini 	: date_ini
					,date_end 	: date_end
					,typeRep 	: typeRep
	            },
	            //mientras enviamos el archivo
	            beforeSend 	: function(){
	                message = "<span class='before'>Generating Report, Please Wait...</span>";
	                showMessage(message,'vr')        
	            },
	            //una vez finalizado correctamente
	            success: function(data){            	
	            	data = JSON.parse(data);
	            	if (typeof data.data !== 'undefined') 
	            	{				  
	            		message = "<span class='error'>An error has occurred, Please Try Again.</span>";
	                	if (data.data!='') 
		            	{	
		            		if(typeRep==1)
		            		{
		            			descTypeRep = 'Daily';
		            		}	
		            		if(typeRep==2)
		            		{
		            			descTypeRep = 'Weekly';
		            		}	
		            		if(typeRep==3)
		            		{
		            			descTypeRep = 'Monthly';
		            		}		  
		                	tableVR = $('#view-report-table').DataTable( 
			            	{
			            		aLengthMenu		: [[25, 50, 75, -1], [25, 50, 75, "All"]],
	        					iDisplayLength	: 25,
						        data 			: data.data,
						        columns 		: 
						        [	
						            { "data": "Date" 				},
						            { "data": "Store" 				},
						            { "data": "Manager" 			},
						            { "data": "Sales" 				},
						            { "data": "GrossProfit" 		},
						            { "data": "Hours" 				},
						            { "data": "Budget" 				},
						            { "data": "Budget_plus/minus" 	},
						            { "data": "OT" 					},
						            { "data": "MGR_req_hrs" 		},
						            { "data": "MGR_HRS" 			},
						            { "data": "MGR_hrs_plus/minus"	}						           
						        ],
						        dom: 'Bfrtip',
						        buttons: [
						            {
						                extend: 'excelHtml5',
						                title: 'Sales Report '+descTypeRep+' '+date_ini.replace("/", "-")+ ' To '+date_end.replace("/", "-")
						            },
						            {
						                extend: 'pdfHtml5',
						                title: 'Sales Report '+descTypeRep+' '+date_ini.replace("/", "-")+ ' To '+date_end.replace("/", "-")
						            }
						        ]
						    } );
						    showMessage('','vr');
						}
						else
						{
							$('#view-report-table').hide();
						}
					}
	                showMessage('','vr');
	            },
	            //si ha ocurrido un error
	            error: function(){
	                message = "<span class='error'>An error has occurred, Please Try Again.</span>";
	                showMessage(message,'vr');
	            }
	        });        
	   	}
	});

	function showMessage(message,tab)
	{
		console.log("Tab: "+tab+message)
	    $("#messages-"+tab).html("").show();
	    $("#messages-"+tab).html(message);
	}

	function LoadStoreGoalsTable ()
	{
		$.ajax({
            url			: 'services/operations-sg.php',  
            type 		: 'POST',
            data 		: { opt : 'load' },
            beforeSend 	: function()
            {
                message = "<span class='before'>Loading Table, Please Wait...</span>";
                showMessage(message,'sg')        
            },
            //una vez finalizado correctamente
            success: function(data)
            {
            	data = JSON.parse(data);
            	if (typeof data.data !== 'undefined') 
            	{				  
            		message = "<span class='error'>An error has occurred, Please Try Again.</span>";
                	if (data.data!='') 
	            	{			  
	                	tableSG = $('#store-goals-table').DataTable( 
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
					    showMessage('','sg');

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
						                message = "<span class='before'>Uploading File, Please Wait...</span>";
						                showMessage(message,'sg')        
						            },
						            //una vez finalizado correctamente
						            success: function(data)
						            {
						            	data = JSON.parse(data);
						            	message = "<span class='error'>An error has occurred, Please Try Again.</span>";
						            	if (typeof data.success !== 'undefined') 
						            	{				  
						            		message = "<span class='error'>An error has occurred, Please Try Again.</span>";
						                	if (data.success==1) 
							            	{				  
							                	message = "<span class='success'>File updated successfully.</span>";
							                	$('#EditStoreGoal').modal('hide');
												$( "#btn-save_MDLSG").off();							                	
												$( "#store-goals-table .Edit-control").off();
												tableSG.destroy();
												LoadStoreGoalsTable ()
											}
										}
						                showMessage(message,'sg');
						            },
						            //si ha ocurrido un error
						            error: function(){
						                message = "<span class='error'>An error has occurred, Please Try Again.</span>";
						                showMessage(message,'sg');
						            }
						        }); 							           
							});
					    } ); 
					}
					else
					{
						$('#store-goals-table').hide();
						$('#FormUploadStoreGoals').show();
					}
				}
                showMessage('','sg');            	 
            },
            //si ha ocurrido un error
            error: function(){
                message = "<span class='error'>An error has occurred, Please Reload.</span>";
                showMessage(message,'sg');
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