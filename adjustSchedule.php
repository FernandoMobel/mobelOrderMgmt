<?php include 'includes/nav.php';?>
<?php include 'includes/db.php';?>
<?php
/*$sql = "SELECT orderCode as title, schedComplDt as start, CASE WHEN orderCode like 'SKB%' THEN \"red\" WHEN orderCode like 'DIY%' THEN \"green\" ELSE \"blue\" END as color FROM mobelSch2020 where completedDt is null and schedComplDt is not null order by receivedDt desc";
 $result = opendb($sql);
 $dbdata = array();
 while ( $row = $result->fetch_assoc())  {
	$dbdata[]=$row;
  }
 $jobs = json_encode($dbdata);
 echo "<p id=\"jsonJobs\" hidden>".$jobs."</p>";*/
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

    var calendarEl = document.getElementById('calendar');
	var jsonJobs = $("#jsonJobs").text();
	
    var calendar = new FullCalendar.Calendar(calendarEl, {	
		schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',	
		//weekends: false,
		themeSystem: 'bootstrap',
		views: {
			dayHeaderFormat: { weekday: 'long' }						
		  },
		headerToolbar: {
			left: 'prev,next today',
			center: 'title',
			right: 'dayGridMonth,timeGridWeek,timeGridDay listWeek'
		  },
		editable: true, //Calendar events can me moved or not
		droppable: true, // this allows things to be dropped onto the calendar
		drop: function(arg) {
			// is the "remove after drop" checkbox checked?
			if (document.getElementById('drop-remove').checked) {
			  // if so, remove the element from the "Draggable Events" list
			  arg.draggedEl.parentNode.removeChild(arg.draggedEl);
			}
		},
		dateClick: function(info) {
			myData = { mode: "getTotalDay", date: info.dateStr};
			$.post("calendarActions.php",
				myData, 
				   function(data, status, jqXHR) {
						alert('The total for '+ info.dateStr+' is: '+jqXHR['responseText']);
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
		events: {
				url: 'calendarActions.php',
				method: 'POST',
				extraParams: {mode: 'wrappingSch'},
				//extraParams: {mode: 'getScheduleMain'},
				failure: function() {
				  document.getElementById('script-warning').style.display = 'block';
				}
		},
		eventOverlap: function(stillEvent, movingEvent) {
			
			return stillEvent.display!='background';
		},
		eventDrop: function(info) {
			//alert(info.event.id + "  was dropped on " + info.event.start.toISOString());
			date = new Date(info.event.start.toISOString());
			oldDate = new Date(info.oldEvent.start.toISOString());
			if (confirm("Are you sure about this change?")) {
				myData = { mode: "updateDate",  date: date.getFullYear()+'-' + (date.getMonth()+1) + '-'+date.getDate(), oid:info.event.id, oldDate:oldDate.getFullYear()+'-' + (oldDate.getMonth()+1) + '-'+oldDate.getDate()};
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
    });
    calendar.render();
	
  });
  
	

</script>
<style>

  #external-events {
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

  #loading {
    display: none;
    position: absolute;
    top: 10px;
    right: 10px;
  }

</style>
</head>
<div class="container">
	<div id='wrap'>

		<div id='external-events' hidden>
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

		<div id='loading'>loading...</div>

		<div id='calendar'></div>

	</div>
</div>
<?php include 'includes/foot.php';?>