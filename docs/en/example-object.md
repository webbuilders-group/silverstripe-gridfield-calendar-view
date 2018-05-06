Example Calendar Object
=================
```php
class ExampleCalendarObject extends DataObject
{
    private static $db = array(
        'Title'=>'Varchar(255)',
        'Summary'=>'Varchar(1000)',
        'IsAllDay'=>'Boolean',
        'StartDateTimeField'=>'SS_Datetime',
        'EndDateTimeField'=>'SS_Datetime'
    );
}
```
