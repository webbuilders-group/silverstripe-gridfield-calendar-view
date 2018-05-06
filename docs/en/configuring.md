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
    ->addComponent(
        $calendar = new GridFieldCalendarView(
            'StartDateTimeField',
            'EndDateTimeField'
        )
    );

$calendar->setCustomOptions(
    "footer" => array(
        "left" => '',
        "center" => 'month,agendaWeek,agendaDay',
        "right" => ''
    )
);
```

## Setting events with different colours

You can set the colour of events in the calendar but using
`GridFieldCalendarView::setColourField()`. This field can be a DB field, a casted
field or a conventional public param.

For example, you might have the following `DataObject`:

```php
class ExampleCalendarObject extends DataObject {
    private static $db=array(
        'Title'=>'Varchar(255)',
        'Summary'=>'Varchar(1000)',
        'IsAllDay'=>'Boolean',
        'StartDateTimeField'=>'SS_Datetime',
        'EndDateTimeField'=>'SS_Datetime',
        'Colour' => 'Varchar' // Will be used to set a colour hex (EG: #FF0000)
    );
}
```

Then, when you add your `GridFieldCalendarView`, you would assign the calendarfield
like so:

```php
$myGridField = new GridField(
    'Events',
    'Events',
    $this->Events(),
    GridFieldConfig_RecordEditor::create(10)
);

$myGridField
    ->getConfig()
    ->addComponent(
        $calendar = new GridFieldCalendarView(
            'StartDateTimeField',
            'EndDateTimeField'
        )
    );

// Tell GridFieldCalendarView that it needs to use the "Colour" field.
// This needs to return a hex value with the hash (EG: #FF0000) 
$calendar->setColourField("Colour");
```

## Showing the calendar by default

You can (if desired) set the calendar to appear by default (instead of the
GridField). You can do this by setting `GridFieldCalendarView::setShowCalendarDefault()`
to `true`. For example:

```php
// As per the example above
$calendar->setShowCalendarDefault(true);
```

**NOTE: Height issues with calendar as default view** Showing calendar by default
can cause some oddities regarding the height fo the calendar. This is most likely
because the SilverStripe UI isn't fully loaded when the calendar loads, so it
appears to load at a minimal required height.

If you want to use the calender view by default, you will most likely need to tweak
FullCalendar's settings a little, most likely:

* [aspectRatio](https://fullcalendar.io/docs/aspectRatio)
* [contentHeight](https://fullcalendar.io/docs/contentHeight)
* [height](https://fullcalendar.io/docs/height)

You can set `aspectRatio` (for example) doing the following:

```php
// Setup calendar, as per docs above
$calendar->setCustomOptions(
    [
        'aspectRation': 0.75 // Height will be about 1.25 greater than width
    ]
);
```