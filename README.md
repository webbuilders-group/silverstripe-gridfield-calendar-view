SilverStripe GridField Calendar View
=================
A component for viewing a GridField's data as a calendar, useful for things like event calendars. It also provides a toggle button that let's you switch between the default list view for a GridField and the Calendar view.

## Maintainer Contact
* Ed Chipman ([UndefinedOffset](https://github.com/UndefinedOffset))


## Requirements
* SilverStripe Framework 3.3+


## Installation
__Composer (recommended):__
```
composer require webbuilders-group/silverstripe-gridfield-calendar-view
```


If you prefer you may also install manually:
* Download the module from here https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/releases
* Extract the downloaded archive into your site root so that the destination folder is called gridfield-calendar-view, opening the extracted folder should contain _config.php in the root along with other files/folders
* Run dev/build?flush=all to regenerate the manifest


## Quick Start
To get started you need to have an object that can be rendered as a item on a calendar. Then you need to add the ``GridFieldCalendarView`` component to your GridField's config. For example:

```php
$myGridField=new GridField('Events', 'Events', $this->Events(), GridFieldConfig_RecordEditor::create(10));
$myGridField->getConfig()->addComponent(new GridFieldCalendarView(
        'StartDateTimeField', //This must be the name of the field in the model not a getter method
        'EndDateTimeField'
    ));
```

There are more options available, when adding the component for information on these see [the documentation](docs/index.md) for more information.
