General Usage
=================
In the most basic usage you will need to have an object that can be rendered as a item on a calendar ([see an example here](example-object.md)). Then you need to add the ``GridFieldCalendarView`` component to your GridField's config. For example:

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
        new GridFieldCalendarView(
            'StartDateTimeField', //Must be the name of the field in the model not a getter method
            'EndDateTimeField'
        )
    );
```

There are also more parameters that you can use to control more aspects of how the object is displayed as well as where the toggle controls appear. For example:

```php
/* ... */
$myGridField
    ->getConfig()
    ->addComponent(
        new GridFieldCalendarView(
            'StartDateTimeField', //This must be the name of the field in the model not a getter method
            'EndDateTimeField',
            'buttons-before-left', //This is the position of the toggle controls by default it is the buttons bar buttons-before-left
            'Title', //This is what field/getter the Title of the object is pulled from
            'Summary', //This is what field/getter the summary for the object is pulled from
            'IsAllDay' //This is a boolean like field that will return whether the object covers the whole day or not
        )
    );
```
