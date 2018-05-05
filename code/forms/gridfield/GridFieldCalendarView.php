<?php

/**
 * The main calendar component to add to your gridfield
 *
 * @category  CategoryName
 * @package   PackageName
 * @author    Ed Chipman <support@webbuildersgroup.com>
 * @author    Mo <morven@ilateral.co.uk>
 * @copyright 2016 Webbuilders Group
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link      https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view
 */
class GridFieldCalendarView implements GridField_HTMLProvider, GridField_URLHandler
{

    /**
     * The name of the db field used for the start date
     * 
     * @var string
     */
    private $_startDateField;

    /**
     * The name of the field used for the end date
     * 
     * @var string
     */
    private $_endDateField;

    /**
     * The location on the gridfield to add the toggle controls
     * 
     * @var string
     */
    private $_togglePosition;

    /**
     * The name of the field (DB, Casted, etc) used to generate a title
     * to appear on the calendar
     * 
     * @var string
     */
    private $_titleField;

    /**
     * The name of the field (DB, Casted, etc) used for to provide more detailed
     * info about this event
     * 
     * @var string
     */
    private $_summaryField;

    /**
     * The name of the field (DB, Casted, etc) used to determine if the event
     * shows as "All Day"
     * 
     * @var string
     */
    private $_allDayField;

    /**
     * The name of the field (DB, Casted, etc) used for the event colour
     * 
     * @var string
     */
    private $_colourField;

    /**
     * Default options for the FullCalendar instance
     * 
     * @var array
     */
    private $_default_options = array(
        "header" => array(
            "left" => 'title',
            "center" => '',
            "right" => 'today prev,next'
        ),
        "footer" => false
    );

    /**
     * Overwrite the default options with your own settings
     * 
     * @var array
     */
    private $_custom_options = array();

    /**
     * Constructor
     *
     * @param {string} $startDateField Name of the Start Date field
     * @param {string} $endDateField   Name of the End Date field
     * @param {string} $togglePosition Position of the toggle controls
     * @param {string} $titleField     Field name used for the title
     * @param {string} $summaryField   Field name used for the summary
     * @param {string} $allDayField    Field name used to determin if field is an
     *                                 all day event result must be boolean like
     */
    public function __construct(
        $startDateField,
        $endDateField,
        $togglePosition='buttons-before-left',
        $titleField='Title',
        $summaryField='Summary',
        $allDayField='IsAllDay'
    ) {
        $this->_startDateField = $startDateField;
        $this->_endDateField = $endDateField;
        $this->_togglePosition = $togglePosition;
        $this->_titleField = $titleField;
        $this->_summaryField = $summaryField;
        $this->_allDayField = $allDayField;
    }

    /**
     * Returns a map where the keys are fragment names and the values are pieces
     * of HTML to add to these fragments.
     * 
     * @param GridField $gridField The current gridfield
     * 
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        $dataList = $gridField->getList();
        $controller = Controller::curr();
        
        // Get the current query string and and to the request
        // if available
        $request = $controller->getRequest();
        $request_vars = $request->getVars();

        if (array_key_exists("url", $request_vars)) {
            unset($request_vars["url"]);
        }

        $params = http_build_query($request_vars);

        if (!empty($params)) {
            $params = "?" . $params;
        }

        $options = json_encode(
            array_merge(
                $this->_default_options,
                $this->getCustomOptions()
            )
        );

        $calendarData= ArrayData::create(
            array(
            'FeedLink' => $gridField->Link('calendar-data-feed') . $params
            )
        );

        Requirements::customScript(
            <<<JS
            var gridfield_calendar_data = $options
JS
        );
        $css_base = SS_GFCV_BASE.'/css';
        $js_base = SS_GFCV_BASE.'/javascript';

        Requirements::css($css_base.'/fullcalendar.min.css');
        Requirements::css($css_base.'/GridFieldCalendarView.css');

        Requirements::javascript($js_base.'/moment.min.js');
        Requirements::javascript($js_base.'/fullcalendar.min.js');
        Requirements::javascript($js_base.'/GridFieldCalendarView.js');

        return array(
            'after' => $calendarData
                ->renderWith('GridFieldCalendarView'),
            $this->_togglePosition => $gridField
                ->renderWith('GridFieldCalendarView_toggle')
        );
    }

    /**
     * Sets the start date/time field name
     *
     * @param {string} $field Field name to be used for the start date/time, this
     *                        must match the model it cannot be a getter
     * 
     * @return self
     */
    public function setStartDateField($field)
    {
        $this->_startDateField=$field;
        return $this;
    }

    /**
     * Gets the start date/time field used
     *
     * @return {string}
     */
    public function getStartDateField()
    {
        return $this->_startDateField;
    }

    /**
     * Sets the end date/time field name
     *
     * @param {string} $field Name of the field to be used for the end date/time
     * 
     * @return {GridFieldCalendarView}
     */
    public function setEndDateField($field)
    {
        $this->_endDateField = $field;
        return $this;
    }

    /**
     * Gets the end date/time field used
     *
     * @return {string}
     */
    public function getEndDateField()
    {
        return $this->_endDateField;
    }

    /**
     * Sets the position of the list/calendar toggle button
     *
     * @param {string} $location Location the component will be added to GridField
     *
     *  @return {GridFieldCalendarView}
     */
    public function setTogglePosition($location)
    {
        $this->_togglePosition = $location;
        return $this;
    }

    /**
     * Gets the position of the list/calendar toggle button
     *
     * @return {string}
     */
    public function getTogglePosition()
    {
        return $this->_togglePosition;
    }

    /**
     * Sets the title field name
     *
     * @param {string} $field Field name to be used for the title in the calendar
     *
     * @return self
     */
    public function setTitleField($field)
    {
        $this->_titleField=$field;
        return $this;
    }

    /**
     * Gets the title field used
     *
     * @return {string}
     */
    public function getTitleField()
    {
        return $this->_titleField;
    }

    /**
     * Sets the summary field name
     *
     * @param {string} $field Field name to be used for the summary in the calendar
     *
     * @return self
     */
    public function setSummaryField($field)
    {
        $this->_summaryField=$field;
        return $this;
    }

    /**
     * Gets the summary field used
     *
     * @return {string}
     */
    public function getSummaryField()
    {
        return $this->_summaryField;
    }
    
    /**
     * Sets the all day field name
     *
     * @param {string} $field Field name that determines if field is all day,
     *                        result must be boolean like
     *
     * @return self
     */
    public function setAllDayField($field)
    {
        $this->_allDayField=$field;
        
        return $this;
    }
    
    /**
     * Gets the all day field used
     *
     * @return {string}
     */
    public function getAllDayField()
    {
        return $this->_allDayField;
    }

    /**
     * Gets the calendar options that are currently set
     * 
     * @return {string}
     */
    public function getCustomOptions()
    {
        return $this->_custom_options;
    }

    /**
     * Overwrite the custom calendar options
     * 
     * @param {array} $options List of items to appear in the header
     *
     * @return self
     */
    public function setCustomOptions($options)
    {
        $this->_custom_options = $options;
        return $this;
    }

    /**
     * Return URLs to be handled by this grid field component, in an array the same
     * form as $url_handlers.
     * 
     * @param GridField $gridField The current GridField
     * 
     * @return {array}
     */
    public function getURLHandlers($gridField)
    {
        return array(
            'calendar-data-feed'=>'handleCalendarFeed'
        );
    }
    
    /**
     * Handles retrieving the data for the calendar
     * 
     * @param {GridField}      $gridField GridField instance
     * @param {SS_HTTPRequest} $request   HTTP Request Object
     * 
     * @return {string} Response JSON
     */
    public function handleCalendarFeed(GridField $gridField, SS_HTTPRequest $request)
    {
        //Validate Security Token
        if (!SecurityToken::inst()->checkRequest($request)) {
            return Controller::curr()
                ->httpError(403, 'Security Token Expired or Invalid');
        }

        //Figure out the start date
        $startTS = strtotime($request->postVar('start-date'));

        if ($request->postVar('start-date') && $startTS !== false) {
            //Push date into next month if first visible day on calendar is not 1
            if (date('j', $startTS) !=1) {
                $startDate = date(
                    'Y-m-01',
                    strtotime(date('Y-m-01', $startTS).' next month')
                );
            } else {
                $startDate = date('Y-m-d', $startTS);
            }
        } else {
            $startDate = date('Y-m-01');
        }

        //Figure out the end date
        $endTS = strtotime($request->postVar('end-date'));

        if ($request->postVar('end-date') && $endTS !== false) {
            //Push date into previous month if last visible day is less then 28
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

        //Fetch the month's events
        $list = $gridField->getList();
        $deletedManip = $gridField
            ->getConfig()
            ->getComponentByType('GridFieldDeletedManipulator');

        if ($deletedManip) {
            $list = $deletedManip->getManipulatedData($gridField, $list);
        }

        $events = $list
            ->filter(
                array(
                $this->_startDateField.':GreaterThanOrEqual' => date(
                    'Y-m-01 00:00:00',
                    strtotime($startDate)
                ),
                $this->_startDateField.':LessThanOrEqual' => date(
                    'Y-m-t 23:59:59',
                    strtotime($endDate)
                )
                )
            )->sort($this->_startDateField);

        //Build the response data
        $results = array();
        foreach ($events as $event) {
            $deleted_event_class = null;

            if ($event->hasMethod('getIsDeletedFromStage')
                && $event->getIsDeletedFromStage()
            ) {
                $deleted_event_class = 'deleted-event';
            }
            
            $result = array(
                'title' => $event->{$this->_titleField},
                'abstractText' => $event->{$this->_summaryField},
                'allDay' => (bool) $event->{$this->_allDayField},
                'start' => date('c', strtotime($event->{$this->_startDateField})),
                'end' => date('c', strtotime($event->{$this->_endDateField})),
                'url' => Controller::join_links(
                    $gridField->Link('item'),
                    $event->ID,
                    'edit'
                ),
                'className' => $deleted_event_class
            );

            if ($this->getColourField()) {
                $result["color"] = $event->{$this->getColourField()};
            }

            $results[] = $result;
        }

        //Serialize to json
        $results = json_encode($results);

        //Respond with the resulting json
        $response = Controller::curr()->getResponse();
        $response->addHeader('Content-Type', 'application/json; charset=utf-8');
        return $results;
    }

    /**
     * Get the value of _colourField
     * 
     * @return string
     */ 
    public function getColourField()
    {
        return $this->_colourField;
    }

    /**
     * Set the value of _colourField
     * 
     * @param string $_colourField The name of the field to deisgnate colour
     *
     * @return self
     */ 
    public function setColourField($_colourField)
    {
        $this->_colourField = $_colourField;

        return $this;
    }
}