Example Calendar Object
=================
```php
class ExampleCalendarObject extends DataObject
{
    private static $db = [
        'Title' => 'Varchar(255)',
        'Summary' => 'Varchar(1000)',
        'IsAllDay' => 'Boolean',
        'StartDateTimeField' => 'Datetime',
        'EndDateTimeField' => 'Datetime',
    ];
}
```
