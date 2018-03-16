Configuring
===========

GridField Calendar View uses [FullCalendar](https://fullcalendar.io/) to
render the calendar into the SilverStripe admin.

This comes with some default configuration options, but these options can be
overwritten using `GridFieldCalendarView::setCustomOptions()`.

You can pass `setCustomOptions` an array of configuration options, directly 
matching the ones available in the [FullCalendar Docs](https://fullcalendar.io/docs).

For example, if you wanted to add "month, week, day" buttons to the footer
of the calendar, you could do the following (which would add the buttons to 
the center of the footer):

```php
$myGridField = new GridField(
    'Events',
    'Events',
    $this->Events(),
    GridFieldConfig_RecordEditor::create(10)
);

$myGridField
    ->getConfig()
    ->addComponent($calendar = new GridFieldCalendarView(
        'StartDateTimeField',
        'EndDateTimeField'
    ));

$calendar->setCustomOptions(
    "footer" => array(
        "left" => '',
        "center" => 'month,agendaWeek,agendaDay',
        "right" => ''
    )
);
```