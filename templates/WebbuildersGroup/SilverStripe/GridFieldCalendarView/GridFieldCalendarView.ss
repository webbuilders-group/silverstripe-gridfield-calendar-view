<% require css("webbuilders-group/silverstripe-gridfield-calendar-view: node_modules/fullcalendar/dist/fullcalendar.min.css") %>
<% require css("webbuilders-group/silverstripe-gridfield-calendar-view: client/dist/css/GridFieldCalendarView.css") %>

<% require javascript("webbuilders-group/silverstripe-gridfield-calendar-view: node_modules/moment/min/moment.min.js") %>
<% require javascript("webbuilders-group/silverstripe-gridfield-calendar-view: node_modules/fullcalendar/dist/fullcalendar.min.js") %>
<% require javascript("webbuilders-group/silverstripe-gridfield-calendar-view: client/dist/js/GridFieldCalendarView.js") %>

<div class="ss-gridfield-calendar" data-calendar-feed="$FeedLink">
    <div class="calendar-display"><!-- --></div>
</div>