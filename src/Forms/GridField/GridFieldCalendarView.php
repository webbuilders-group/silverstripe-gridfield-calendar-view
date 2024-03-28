<?php
namespace WebbuildersGroup\GridFieldCalendarView\Forms\GridField;

use InvalidArgumentException;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Forms\GridField\AbstractGridFieldComponent;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridField_StateProvider;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridState_Data;
use SilverStripe\Security\SecurityToken;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use WebbuildersGroup\GridFieldDeletedItems\Forms\GridFieldDeletedManipulator;

class GridFieldCalendarView extends AbstractGridFieldComponent implements GridField_HTMLProvider, GridField_URLHandler, GridField_StateProvider
{
    use Injectable;

    private $_startDateField;
    private $_endDateField;
    private $_togglePosition;
    private $_titleField;
    private $_summaryField;
    private $_allDayField;
    private $_defaultViewMode = 'default';

    /**
     * Default options for the FullCalendar instance
     * @var array
     */
    private $default_options = [
        "headerToolbar" => [
            "left" => 'title',
            "center" => '',
            "right" => 'today prev,next',
        ],
        "footerToolbar" => false,
    ];

    /**
     * Overwrite the default options with your own settings
     * @var array
     */
    private $custom_options = [];

    /**
     * Constructor
     * @param string $startDateField Name of the Start Date field
     * @param string $endDateField Name of the End Date field
     * @param string $togglePosition Position of the toggle controls
     * @param string $titleField Name of the field to be used for the title in the calendar
     * @param string $summaryField Name of the field to be used for the summary in the calendar
     * @param string $allDayField Name of the field to be used to determin if the field is an all day event result must be boolean like
     */
    public function __construct($startDateField, $endDateField, $togglePosition = 'buttons-before-left', $titleField = 'Title', $summaryField = 'Summary', $allDayField = 'IsAllDay')
    {
        $this->_startDateField = $startDateField;
        $this->_endDateField = $endDateField;
        $this->_togglePosition = $togglePosition;
        $this->_titleField = $titleField;
        $this->_summaryField = $summaryField;
        $this->_allDayField = $allDayField;
    }

    /**
     * Returns a map where the keys are fragment names and the values are pieces of HTML to add to these fragments.
     * @param GridField $gridField Grid field the fragments are being added to
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        $controller = Controller::curr();

        // Get the current query string and and to the request if available
        $request = $controller->getRequest();
        $request_vars = $request->getVars();

        if (array_key_exists("url", $request_vars)) {
            unset($request_vars["url"]);
        }

        $params = http_build_query($request_vars);

        if (!empty($params)) {
            $params = '?' . $params;
        }

        $options = json_encode(array_merge(
            $this->default_options,
            $this->getCustomOptions()
        ));

        $calendarData = ArrayData::create([
            'FeedLink' => Controller::join_links($gridField->Link('calendar-data-feed'), $params),
        ]);


        $this->extraCalendarData($gridField, $calendarData, $request);


        $calendarData->CalendarOptions = $options;


        Requirements::css('webbuilders-group/silverstripe-gridfield-calendar-view:css/GridFieldCalendarView.css');

        Requirements::javascript('webbuilders-group/silverstripe-gridfield-calendar-view:javascript/fullcalendar.min.js');
        Requirements::javascript('webbuilders-group/silverstripe-gridfield-calendar-view:javascript/fullcalendar-moment.min.js');
        Requirements::javascript('webbuilders-group/silverstripe-gridfield-calendar-view:javascript/GridFieldCalendarView.js');

        $fragments = [
            'after' => $calendarData->renderWith([get_class($this), self::class]),
        ];

        if ($this->_togglePosition != 'after') {
            $fragments[$this->_togglePosition] = $gridField->customise(['DefaultViewMode' => $this->_defaultViewMode])->renderWith(self::class . '_toggle');
        } else {
            $fragments['after'] = $gridField->customise(['DefaultViewMode' => $this->_defaultViewMode])->renderWith(self::class . '_toggle')->getValue() . $fragments['after']->getValue();
        }

        return $fragments;
    }

    /**
     * Sets the start date/time field name
     * @param string $field Name of the field to be used for the start date/time, this must match the model it cannot be a getter
     * @return static
     */
    public function setStartDateField($field)
    {
        $this->_startDateField = $field;
        return $this;
    }

    /**
     * Gets the start date/time field used
     * @return string
     */
    public function getStartDateField()
    {
        return $this->_startDateField;
    }

    /**
     * Sets the end date/time field name
     * @param string $field Name of the field to be used for the end date/time
     * @return static
     */
    public function setEndDateField($field)
    {
        $this->_endDateField = $field;
        return $this;
    }

    /**
     * Gets the end date/time field used
     * @return string
     */
    public function getEndDateField()
    {
        return $this->_endDateField;
    }

    /**
     * Sets the position of the list/calendar toggle button
     * @param string $location Location for the component to be added to the GridField
     * @return static
     */
    public function setTogglePosition($location)
    {
        $this->_togglePosition = $location;
        return $this;
    }

    /**
     * Gets the position of the list/calendar toggle button
     * @return string
     */
    public function getTogglePosition()
    {
        return $this->_togglePosition;
    }

    /**
     * Sets the title field name
     * @param string $field Name of the field to be used for the title in the calendar
     * @return static
     */
    public function setTitleField($field)
    {
        $this->_titleField=$field;
        return $this;
    }

    /**
     * Gets the title field used
     * @return string
     */
    public function getTitleField()
    {
        return $this->_titleField;
    }

    /**
     * Sets the summary field name
     * @param string $field Name of the field to be used for the summary in the calendar
     * @return static
     */
    public function setSummaryField($field)
    {
        $this->_summaryField = $field;
        return $this;
    }

    /**
     * Gets the summary field used
     * @return string
     */
    public function getSummaryField()
    {
        return $this->_summaryField;
    }

    /**
     * Sets the all day field name
     * @param string $field Name of the field to be used to determin if the field is an all day event result must be boolean like
     * @return static
     */
    public function setAllDayField($field)
    {
        $this->_allDayField = $field;
        return $this;
    }

    /**
     * Gets the all day field used
     * @return string
     */
    public function getAllDayField()
    {
        return $this->_allDayField;
    }

    /**
     * Overwrite the custom calendar options
     * @param array $data List of items to appear in the header
     * @return static
     */
    public function setCustomOptions($options)
    {
        $this->custom_options = $options;
        return $this;
    }

    /**
     * Gets the calendar options that are currently set
     * @return string
     */
    public function getCustomOptions()
    {
        return $this->custom_options;
    }

    /**
     * Sets the default view mode
     * @param array $view View mode to default to (default or calendar)
     * @return static
     * @throws InvalidArgumentException
     */
    public function setDefaultView($view)
    {
        if (!in_array($view, ['default', 'calendar'])) {
            throw new InvalidArgumentException('View mode "' . $view . '" is not an expected option, valid options are "default" and "calendar"');
        }

        $this->_defaultViewMode = $view;
        return $this;
    }

    /**
     * Gets the default view mode
     * @return string
     */
    public function getDefaultViews()
    {
        return $this->_defaultViewMode;
    }

    /**
     * Return URLs to be handled by this grid field component, in an array the same form as $url_handlers.
     * @return array
     */
    public function getURLHandlers($gridField)
    {
        return [
            'calendar-data-feed' => 'handleCalendarFeed',
        ];
    }

    /**
     * Handles retrieving the data for the calendar
     * @param GridField $gridField GridField instance
     * @param HTTPRequest $request HTTP Request Object
     * @return string Response JSON
     */
    public function handleCalendarFeed(GridField $gridField, HTTPRequest $request)
    {
        // Validate Security Token
        if (!SecurityToken::inst()->checkRequest($request)) {
            return Controller::curr()
                ->httpError(403, 'Security Token Expired or Invalid');
        }

        // Figure out the start date
        $startTS = strtotime($request->postVar('start-date'));

        if ($request->postVar('start-date') && $startTS !== false) {
            // Push the date into the next month if the first visible day on the calendar is not 1
            if (date('j', $startTS) !=1) {
                $startDate = date('Y-m-01', strtotime(date('Y-m-01', $startTS).' next month'));
            } else {
                $startDate = date('Y-m-d', $startTS);
            }
        } else {
            $startDate = date('Y-m-01');
        }

        // Figure out the end date
        $endTS = strtotime($request->postVar('end-date'));

        if ($request->postVar('end-date') && $endTS !== false) {
            // Push the date into the previous month if the last visible day on the calendar is less then 28
            if (date('j', $endTS) < 28) {
                $endDate = date(
                    'Y-m-t',
                    strtotime(date('Y-m-01', $endTS).' previous month')
                );
            } else {
                $endDate = date('Y-m-d', $endTS);
            }
        } else {
            $endDate = date('Y-m-t');
        }

        // Fetch the month's events
        $gridField->getConfig()->removeComponentsByType(GridFieldPaginator::class);
        $list = $gridField->getManipulatedList();

        $events = $list
            ->filter(array(
                $this->_startDateField.':GreaterThanOrEqual' => date(
                    'Y-m-01 00:00:00',
                    strtotime($startDate)
                ),
                $this->_startDateField.':LessThanOrEqual' => date(
                    'Y-m-t 23:59:59',
                    strtotime($endDate)
                )
            ))->sort($this->_startDateField);

        // Build the response data
        $result = [];
        foreach ($events as $event) {
            $deleted_event_class = null;

            if (($event->hasMethod('isOnLiveOnly') && $event->isOnLiveOnly()) || ($event->hasMethod('isArchived') && $event->isArchived())) {
                $deleted_event_class = 'deleted-event';
            }

            $data = [
                'id' => $event->ID,
                'title' => $event->{$this->_titleField},
                'allDay' => (bool) $event->{$this->_allDayField},
                'start' => date('c', strtotime($event->{$this->_startDateField})),
                'end' => date('c', strtotime($event->{$this->_endDateField})),
                'url' => $gridField->addAllStateToUrl(Controller::join_links($gridField->Link('item'), $event->ID, 'edit')),
                'extendedProps' => [
                    'abstractText' => $event->{$this->_summaryField},
                    'className' => $deleted_event_class,
                ],
            ];

            $event->invokeWithExtensions('updateGridFieldCalendarData', $data);

            $result[] = $data;
        }

        // Respond with the resulting json
        $response = Controller::curr()->getResponse();
        $response->addHeader('Content-Type', 'application/json; charset=utf-8');
        return json_encode($result);
    }

    /**
     * Initialise the default state in the given GridState_Data
     * @param GridState_Data $data The top-level state object
     */
    public function initDefaultState(GridState_Data $data): void
    {
        $data->GridFieldCalendarView->initDefaults(['view_mode' => $this->_defaultViewMode]);
    }

    /**
     * Allows for adding extra calendar data to the render process
     * @param GridField $gridField Grid Field the calendar data applies to
     * @param ArrayData $calendarData Calendar data to augment
     * @param HTTPRequest $request Current HTTP Request Object
     */
    protected function extraCalendarData(GridField $gridField, ArrayData $calendarData, HTTPRequest $request)
    {
    }
}
