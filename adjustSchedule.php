<?php 
include 'includes/nav.php';
include 'includes/db.php';
opendb("update schedule set cutting=DATE_ADD(finishing, INTERVAL -7 DAY) where cutting is null");
?>
<?php
?>
<script>

document.addEventListener('DOMContentLoaded', function() {

    /* initialize the external events
    -----------------------------------------------------------------*/
    var containerEl = document.getElementById('external-events-list');
    new FullCalendar.Draggable(containerEl, {
      itemSelector: '.fc-event',
      eventData: function(eventEl) {
        return {
          title: eventEl.innerText.trim()
        }
      }
    });

    //// the individual way to do it
    // var containerEl = document.getElementById('external-events-list');
    // var eventEls = Array.prototype.slice.call(
    //   containerEl.querySelectorAll('.fc-event')
    // );
    // eventEls.forEach(function(eventEl) {
    //   new FullCalendar.Draggable(eventEl, {
    //     eventData: {
    //       title: eventEl.innerText.trim(),
    //     }
    //   });
    // });

    /* initialize the calendar
    -----------------------------------------------------------------*/
    var schedule = 4;//4 means holidays
    var calendarEl = document.getElementById('calendar');
    var calendarJ = document.getElementById('calendarJob');
	
	var calendar = new FullCalendar.Calendar(calendarEl, {	
		//schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
		schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
		//weekends: false,
		themeSystem: 'bootstrap',
		views: {
			dayHeaderFormat: { weekday: 'long' }						
		  },
		customButtons: {
		    completion: {
		      	text: 'Shipping',
		      	click: function(arg, a) {
		      		schedule = 3;
		        	$(arg['path'][1]['childNodes']).removeClass('active');
		        	$(a).addClass('active');
		        	calendar.refetchEvents();
		      	}
		    },
		    wrapping: {
		      	text: 'Completion',
		      	click: function(arg, a) {
		      		schedule = 2;
		        	$(arg['path'][1]['childNodes']).removeClass('active');
		        	$(a).addClass('active');
		        	calendar.refetchEvents();		        
		      	}
		    },
		    finishing: {
		      	text: 'Finishing',
		      	click: function(arg,a) {
		      		schedule = 1;		        
		        	$(arg['path'][1]['childNodes']).removeClass('active');
		        	$(a).addClass('active');
		        	calendar.refetchEvents();
		      	}
		    },
		    cutting: {
		      	text: 'Cutting',
		      	click: function(arg,a) {
		      		schedule = 0;		        
		        	$(arg['path'][1]['childNodes']).removeClass('active');
		        	$(a).addClass('active');
		        	calendar.refetchEvents();
		      	}
		    }
		},
		headerToolbar: {
			left: 'prev,next completion,wrapping,finishing,cutting',
			center: 'title',
			right: 'listWeek'//,timeGridDay,dayGridMonth,timeGridWeek 
		  },
		editable: true, //Calendar events can be moved or not
		droppable: false, // this allows things to be dropped onto the calendar
		drop: function(arg) {
			// is the "remove after drop" checkbox checked?
			//if (document.getElementById('drop-remove').checked) {
			  // if so, remove the element from the "Draggable Events" list
			  arg.draggedEl.parentNode.removeChild(arg.draggedEl);
			//}
		},
		dateClick: function(info) {
			//console.log(schedule);
			if(schedule==4){
				alert('Please select a schedule first');
				return;
			}
			//Boxes information
			myData = { mode: "getTotalDay", date: info.dateStr, schID: schedule };
			//console.log(myData);
			$.post("calendarActions.php",
				myData, 
				   function(data, status, jqXHR) {
					   	$('#lblTitle').html('Date: '+info.dateStr);
					   	$('#lblDesc').html('Date totals:');
					   	var totals = JSON.parse(jqXHR['responseText']);
					   	
					   	//console.log(totals);
					   	$('#iBoxes').val(totals.cc);
					   	$('#iFronts').val(totals.fronts);
					   	$('#iItems').val(totals.pieces);
					});		
			//Orders data
			myData = { mode: "getDateOrdDetails", date: info.dateStr, schID: schedule};
			$.post("calendarActions.php",
				myData, 
				   function(data, status, jqXHR) {
				   		$('#divOrdersbyDate').empty();
					   	var orders = JSON.parse(jqXHR['responseText']);
						var htmlCol1 = '<div class="col-6" id="external-events-list">'+
										'<table class="table table-sm text-center table-borderless">'+
											'<thead>'+
												'<tr>'+
													'<th>Order</th>'+
												'</tr>'+
											'</thead'+
											'<tbody>';
						var htmlCol2 = '<div class="col-6">'+
										'<table class="table table-sm text-center">'+
											'<thead>'+
												'<tr>'+
													'<th>Boxes</th><th>Fronts</th><th>Items</th>'+
												'</tr>'+
											'</thead'+
											'<tbody>';
						//Loop Orders
					   	orders.forEach(function(obj){		
					   		htmlCol1 += '<tr><td><div class="fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event ';
					   		switch(obj.CLid) {	
					   			case '1':
							    	htmlCol1 +='bg-designer">';
							    break;
							    case '2':
							    	htmlCol1 +='bg-builder">';
							    break;
							    case '3':
							    	htmlCol1 +='bg-span">';
							    break;
					   		}		   		
					   		htmlCol1 += '<div class="fc-event-main">'+obj.title+'</div>'+
					   				'</div></td></tr>';
					   		htmlCol2 +=	'<tr>'+
									'<td>'+obj.cc+'</td><td>'+obj.fronts+'</td><td>'+obj.pieces+'</td>'+
								'</tr>';
					   	});
					   	htmlCol1 += '</div>';
					   	htmlCol2 += '</tbody>'+
						'</table>';
					   	$('#divOrdersbyDate').append(htmlCol1);
					   	$('#divOrdersbyDate').append(htmlCol2);
					   	$('#txtInfo').hide();
					   	$('#divDayDetails').show();
					   	$('#divOrdDetails').hide();					   	
					});		
		},
		dayMaxEvents: true,
		slotMinTime: "06:00:00",
		slotMaxTime: "19:30:00",
		businessHours: {
			  // days of week. an array of zero-based day of week integers (0=Sunday)
			  daysOfWeek: [ 1, 2, 3, 4, 5 ], // Monday - Friday
			  startTime: '08:00', // a start time
			  endTime: '16:30', // an end time
		},
		/*events: {
				url: 'calendarActions.php',
				method: 'POST',
				//extraParams: {mode: 'wrappingSch'},
				extraParams: {mode: 'hollidaySch'},
				failure: function() {
				  document.getElementById('script-warning').style.display = 'block';
				}
		},*/
		events: function(fetchInfo, successCallback, failureCallback) {
			myData = { mode: "getSchedule", schID: schedule};
			$.post("calendarActions.php",
			myData, 
		       function(data, status, jqXHR) {
            		if(status == "success"){
            	    	var orders = JSON.parse(jqXHR['responseText']);
            		   	successCallback(orders);
            	    }
		    });
		},
		eventOverlap: function(stillEvent, movingEvent) {
			
			return stillEvent.display!='background';
		},
		eventDrop: function(info) {
			//Getting current and new dates
			date = new Date(info.event.start.toISOString());
			oldDate = new Date(info.oldEvent.start.toISOString());
			var deliveryDate;
			if(schedule!='3'){
				//Getting delivery date
				myData = {mode: 'getLimitDate', schID:schedule, oid:info.event.id}
				$.ajax({
						url: 'calendarActions.php',
						type: 'POST',
						data: myData, 
						success: function(data, status, jqXHR) {
								deliveryDate= new Date(jqXHR['responseText']);
								if(date > deliveryDate){
									//console.log('New date: '+date+' Delivery: '+deliveryDate);
									alert("New date can't be scheduled beyond the next stage deadline. \nPlease verify the schedule for the next stage.");
									info.revert();
									return;
								}else{
									if (confirm("Are you sure about this change?")) {
										myData = { mode: "updateDate", schID: schedule, date: date.getFullYear()+'-' + (date.getMonth()+1) + '-'+date.getDate(), oid:info.event.id, oldDate:oldDate.getFullYear()+'-' + (oldDate.getMonth()+1) + '-'+oldDate.getDate()};
										$.ajax({
											url: 'calendarActions.php',
											type: 'POST',
											data: myData, 
											success: function(data, status, jqXHR) {
													//console.log(jqXHR['responseText']);
											}	
										});
										
									}else{
											info.revert();
									}
								}
						}
				});	
			}else{
				alert('Shipping date is fixed, you cannot update it.');
				info.revert();
			}
		},
		eventClick: function(info) {
			myData = { mode: "getOrderRooms",  oid: info.event.id};
			$.post("calendarActions.php",
				myData, 
		       	function(data, status, jqXHR) {      
		       		//Header
		       		$('#divDayDetails').hide();
		       		$('#lblTitle').html('<a target="_blank" href="Order.php?OID='+info.event.id+'">'+info.event.title+'</a>');
		       		$('#lblDesc').html('');
		       		$('#txtInfo').hide();
		       		//Body
		       		var jsonOrder = JSON.parse(jqXHR['responseText']);
		       		//console.log(jsonOrder);
					$('#ordContent').empty();
					if(calendar2)
						calendar2.destroy();
		       		//calendar job
		       		
		       		var calendar2 = new FullCalendar.Calendar(calendarJ, {	
						schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
						views: { weekday: 'short' },
						themeSystem: 'bootstrap',
						titleFormat:{month: 'long'},
						headerToolbar: {
							left: 'prev,next',
							center: '',
							right: 'title'//,timeGridDay,dayGridMonth,timeGridWeek 
						  },
						editable: false, //Calendar events can be moved or not
						droppable: false,
						events:function(fetchInfo, successCallback, failureCallback) {
							myData = { mode: "getJobFullSch", oid: info.event.id};
							$.post("calendarActions.php",
							myData, 
						       function(data, status, jqXHR) {
				            		if(status == "success"){
				            	    	var roomStages = JSON.parse(jqXHR['responseText']);
				            	    	//console.log(roomStages);
				            		   	successCallback(roomStages);
				            	    }
						    });
						}
					})
		       		calendar2.render();
		       		
					var flag = true;
					var html = '';
					var tcc=0; 
					var tfr=0;
					var tpc=0;
					jsonOrder.forEach(function(obj){
						if(flag){
							$('#completionDate').val(obj.dateRequired);
							flag = false;
							html = 	'<table class="table table-sm text-center m-0">'+
									'<thead>'+
										'<tr>'+
											'<th></th><th>Boxes</th><th>Fronts</th><th>Items</th>'+
										'</tr>'+
									'</thead'+
									'<tbody>';
						}
						//totals
						tcc += parseInt(obj.cc);
						tfr += parseInt(obj.fronts);
						tpc += parseInt(obj.pieces);
						html +=	'<tr>'+
									'<td>'+obj.name+'</td><td>'+obj.cc+'</td><td>'+obj.fronts+'</td><td>'+obj.pieces+'</td>'+
								'</tr>';
					});
					html +=	'<tr>'+
									'<td><h6>Totals</h6></td><td><h6>'+tcc+'</h6></td><td><h6>'+tfr+'</h6></td><td><h6>'+tpc+'</h6></td>'+
								'</tr>';
					html += '</tbody>'+
						'</table>';
					$('#ordContent').append(html);				
					$('#divOrdDetails').show();
		        });

		    // change the border color
		    info.el.style.borderColor = 'red';
		}
    });
    calendar.render();
	
  });
  
	

</script>
<style>

  /*#external-events {
    position: fixed;
    left: 20px;
    top: 80px;
    width: 150px;
    padding: 0 10px;
    border: 1px solid #ccc;
    background: #eee;
    text-align: center;
  }

  #external-events h4 {
    font-size: 16px;
    margin-top: 0;
    padding-top: 1em;
  }

  #external-events .fc-event {
    margin: 3px 0;
    cursor: move;
  }

  #external-events p {
    margin: 1.5em 0;
    font-size: 11px;
    color: #666;
  }

  #external-events p input {
    margin: 0;
    vertical-align: middle;
  }

  #calendar-wrap {
    margin-left: 200px;
  }

  #calendar {
    max-width: 1100px;
    margin: 0 auto;
	background: #fff;
  }
  #calendar {
    height: 100%;
  }*/
  .fc-h-event .fc-event-main {
  	color: black;
  }

  .bg-span{
	background-color: #7abaff;
	border-color: #7abaff;
  }

  .bg-builder{
  	background-color: #86cfda;
  	border-color: #86cfda;
  }

  .bg-designer{
  	background-color: #dee2e6;
  	border-color: #dee2e6;
  }

  .bg-stage1{
	background-color: #00c434;
  }

  .bg-stage2{
	background-color: #e6ed18;
  }

  .bg-stage3{
	background-color: #edb90c;
  }

  .bg-stage4{
	background-color: #ed260c;
  }

  #divOrdDetails {
  	display: none;
  }

  #divDayDetails {
  	display: none;
  }

  #loading {
    display: none;
    position: absolute;
    top: 10px;
    right: 10px;
  }

</style>
</head>
<div class="container-fluid">	
	<div class="row">
		<!--div id='wrap'-->
			<div class="col-4 d-print-none px-0">				
				<div class="card sticky-top">
				  	<div class="card-body p-1">
				  		<div class="row">
				  			<div class="col-lg-11">
					  			<h5 id="lblTitle" class="card-title">Details</h5>						    	
							</div>
							<div class="col-lg-1">
								<a title="Order Types" id="popOrderType" tabindex="0" role="button" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-container="body" data-html="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-palette" viewBox="0 0 16 16">
									  <path d="M8 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm4 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM5.5 7a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm.5 6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
									  <path d="M16 8c0 3.15-1.866 2.585-3.567 2.07C11.42 9.763 10.465 9.473 10 10c-.603.683-.475 1.819-.351 2.92C9.826 14.495 9.996 16 8 16a8 8 0 1 1 8-8zm-8 7c.611 0 .654-.171.655-.176.078-.146.124-.464.07-1.119-.014-.168-.037-.37-.061-.591-.052-.464-.112-1.005-.118-1.462-.01-.707.083-1.61.704-2.314.369-.417.845-.578 1.272-.618.404-.038.812.026 1.16.104.343.077.702.186 1.025.284l.028.008c.346.105.658.199.953.266.653.148.904.083.991.024C14.717 9.38 15 9.161 15 8a7 7 0 1 0-7 7z"/>
									</svg>
								</a>
							</div>
				  		</div>
				    	
				    	<div class="dropdown-divider mb-3"></div>
				    	<h6 id="lblDesc" class="card-subtitle mb-2 text-muted"></h6>
				    	<p id="txtInfo" class="card-text">First select a Schedule. </br></br>Once jobs are visible, choose some date or job in the calendar to see a detailed explanation about what is happening.</p>
				    	<div class="container" id="divDayDetails">
				    		<div class="row">
				    			<div class="col-12">
								    <div class="input-group mb-3">
									  	<div class="input-group-prepend">
									    	<span class="input-group-text" id="boxes">Boxes</span>
									  	</div>
									  	<input id="iBoxes" type="text" class="form-control" disabled>
									</div>
								</div>
								<div class="col-12">
								    <div class="input-group mb-3">
									  	<div class="input-group-prepend">
									    	<span class="input-group-text" id="fronts">Fronts</span>
									  	</div>
									  	<input id="iFronts" type="text" class="form-control" disabled>
									</div>
								</div>
								<div class="col-12">
								    <div class="input-group mb-3">
									  	<div class="input-group-prepend">
									    	<span class="input-group-text" id="items">Items</span>
									  	</div>
									  	<input id="iItems" type="text" class="form-control" disabled>
									</div>
								</div>
							</div>
							<div id="divOrdersbyDate" class="row">

							</div>
						</div>
						<div class="container" id="divOrdDetails">
							<div>	
								<!--div class="row">
									<div class="col">
										<div class="form-group">
											<div class="input-group mb-3">					
												<div class="input-group-prepend">
													<span class="input-group-text">Completion Date</span>
												</div>
												<input readonly id="completionDate" type="text" maxlength="10" class="form-control datepicker text-center">
											</div>		
										</div>								
									</div>									
								</div-->
								<div id="ordContent" class="container"></div>
							</div>
						</div>
				  	</div>
				</div>
			  	<div class="card">
				  	<div class="card-body">
				  		<div id='calendarJob' class="bg-white"></div>
				  	</div>
				</div>
				
				<div id='external-events1' hidden>
					<h4>Jobs</h4>
					<div id='external-events-list'>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
						  <div class='fc-event-main'>SKB-3140</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
						  <div class='fc-event-main'>SKB-3141</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
						  <div class='fc-event-main'>P715455</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
						  <div class='fc-event-main'>P715458</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
						  <div class='fc-event-main'>MS#20/178</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event bg-success'>
						  <div class='fc-event-main'>SKB-3140 Cutting</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event bg-success'>
						  <div class='fc-event-main'>SKB-3141 Cutting</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event bg-danger'>
						  <div class='fc-event-main'>P715455 Sanding</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event bg-warning'>
						  <div class='fc-event-main'>P715458 Pressing</div>
						</div>
						<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event bg-info'>
						  <div class='fc-event-main'>MS#20/178</div>
						</div>
					</div>

					<p>
						<input type='checkbox' id='drop-remove' />
						<label for='drop-remove'>remove after drop</label>
					</p>
				</div>
				<div class="container">
					<div class="row">
						<div class="col bg-stage1">CUTTING</div>
						<div class="col bg-stage2">FINISHING</div>
						<div class="col bg-stage3">COMPLETION</div>
						<div class="col bg-stage4">SHIPPING</div>
					</div>
				</div>
			</div>
			<div class="col-8 pr-0">
				<div id='loading'>loading...</div>

				<div id='calendar' class="bg-white"></div>
				
			</div>		
		<!--/div-->		
	</div>
</div>
<div id="popovercontent" hidden>
	<table class="table border-0 table-sm mx-auto text-center py-2">
		<!--tr><td class="table-warning p-0"><small><b>Service</b></small></td></tr>
		<tr><td class="table-danger p-0"><small><b>Service w/warranty</b></small></td></tr-->
		<tr><td class="bg-designer p-0"><small>Dealers</small></td></tr>
		<tr><td class="table-info p-0"><small>Builders</small></td></tr>
		<tr><td class="table-primary p-0"><small>Span</small></td></tr>
	</table>
</div> 
<?php include 'includes/foot.php';?>
<script>
$(document).ready(function () {	
	/*Tooltips and Popovers use a built-in sanitizer to sanitize options which accept HTML */
	$.fn.popover.Constructor.Default.whiteList.table = [];
    $.fn.popover.Constructor.Default.whiteList.tr = [];
    $.fn.popover.Constructor.Default.whiteList.td = [];
    $.fn.popover.Constructor.Default.whiteList.th = [];
    $.fn.popover.Constructor.Default.whiteList.div = [];
    $.fn.popover.Constructor.Default.whiteList.tbody = [];
    $.fn.popover.Constructor.Default.whiteList.thead = [];
	$("#popOrderType").popover({
    	html: true, 
		content: function() {
        	return $('#popovercontent').html();
        }
	});
});
</script>