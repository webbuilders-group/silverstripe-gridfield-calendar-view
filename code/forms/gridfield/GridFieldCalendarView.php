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
    protected $startDateField;

    /**
     * The name of the field used for the end date
     * 
     * @var string
     */
    protected $endDateField;

    /**
     * The location on the gridfield to add the toggle controls
     * 
     * @var string
     */
    protected $togglePosition;

    /**
     * The name of the field (DB, Casted, etc) used to generate a title
     * to appear on the calendar
     * 
     * @var string
     */
    protected $titleField;

    /**
     * The name of the field (DB, Casted, etc) used for to provide more detailed
     * info about this event
     * 
     * @var string
     */
    protected $summaryField;

    /**
     * The name of the field (DB, Casted, etc) used to determine if the event
     * shows as "All Day"
     * 
     * @var string
     */
    protected $allDayField;

    /**
     * The name of the field (DB, Casted, etc) used for the event colour
     * 
     * @var string
     */
    protected $colourField;

    /**
     * @var boolean
     */
    protected $show_calendar_default = false;

    /**
     * @var string
     */
    protected $feed_start_format = 'Y-m-01 00:00:00';

    /**
     * @var string
     */
    protected $feed_end_format = 'Y-m-t 23:59:59';

    /**
     * Default options for the FullCalendar instance
     * 
     * @var array
     */
    protected $default_options = array(
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
    protected $custom_options = array();

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
        $togglePosition = 'buttons-before-left',
        $titleField='Title',
        $summaryField='Summary',
        $allDayField='IsAllDay'
    ) {
        $this->startDateField = $startDateField;
        $this->endDateField = $endDateField;
        $this->togglePosition = $togglePosition;
        $this->titleField = $titleField;
        $this->summaryField = $summaryField;
        $this->allDayField = $allDayField;
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
                $this->default_options,
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
            $this->getTogglePosition() => $gridField
                ->renderWith(
                    'GridFieldCalendarView_toggle',
                    array(
                        "Default" => $this->getShowCalendarDefault()
                    )
                )
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
        $this->startDateField=$field;
        return $this;
    }

    /**
     * Gets the start date/time field used
     *
     * @return {string}
     */
    public function getStartDateField()
    {
        return $this->startDateField;
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
        $this->endDateField = $field;
        return $this;
    }

    /**
     * Gets the end date/time field used
     *
     * @return {string}
     */
    public function getEndDateField()
    {
        return $this->endDateField;
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
        $this->togglePosition = $location;
        return $this;
    }

    /**
     * Gets the position of the list/calendar toggle button
     *
     * @return {string}
     */
    public function getTogglePosition()
    {
        return $this->togglePosition;
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
        $this->titleField = $field;
        return $this;
    }

    /**
     * Gets the title field used
     *
     * @return {string}
     */
    public function getTitleField()
    {
        return $this->titleField;
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
        $this->summaryField=$field;
        return $this;
    }

    /**
     * Gets the summary field used
     *
     * @return {string}
     */
    public function getSummaryField()
    {
        return $this->summaryField;
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
        $this->allDayField = $field;
        
        return $this;
    }
    
    /**
     * Gets the all day field used
     *
     * @return {string}
     */
    public function getAllDayField()
    {
        return $this->allDayField;
    }

    /**
     * Gets the calendar options that are currently set
     * 
     * @return {string}
     */
    public function getCustomOptions()
    {
        return $this->custom_options;
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
        $this->custom_options = $options;
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
     * Return a time string to use on the calendar feed (used to get the first
     * item in the list).
     *
     * @param string $start The start date
     * 
     * @return string
     */
    protected function getFeedStart($start)
    {
        //Figure out the start date
        $startTS = strtotime($start);

        if ($start && $startTS !== false) {
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

        return $startDate;
    }

    /**
     * Return a time string to use on the calendar feed (used to get the last
     * item in the list).
     *
     * @param string $end The end date
     * 
     * @return string
     */
    protected function getFeedEnd($end)
    {
        //Figure out the end date
        $endTS = strtotime($end);

        if ($end && $endTS !== false) {
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

        return $endDate;
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

        $startDate = $this->getFeedStart($request->postVar('start-date'));
        $endDate = $this->getFeedEnd($request->postVar('end-date'));
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
                    $this->getStartDateField() . ':GreaterThanOrEqual' => date(
                        $this->getFeedStartFormat(),
                        strtotime($startDate)
                    ),
                    $this->getStartDateField() . ':LessThanOrEqual' => date(
                        $this->getFeedEndFormat(),
                        strtotime($endDate)
                    )
                )
            )->sort($this->getStartDateField());

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
                'title' => $event->{$this->getTitleField()},
                'abstractText' => $event->{$this->getSummaryField()},
                'allDay' => (bool) $event->{$this->getAllDayField()},
                'start' => date('c', strtotime($event->{$this->getStartDateField()})),
                'end' => date('c', strtotime($event->{$this->getEndDateField()})),
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
     * Get the value of colourField
     * 
     * @return string
     */ 
    public function getColourField()
    {
        return $this->colourField;
    }

    /**
     * Set the value of colourField
     * 
     * @param string $colourField The name of the field to deisgnate colour
     *
     * @return self
     */ 
    public function setColourField($colourField)
    {
        $this->colourField = $colourField;

        return $this;
    }

    /**
     * Get the value of _show_calendar_default
     *
     * @return boolean
     */ 
    public function getShowCalendarDefault()
    {
        return $this->show_calendar_default;
    }

    /**
     * Set the value of _show_calendar_default
     *
     * @param boolean $show_calendar_default Should we show the calendar as default?
     *
     * @return self
     */ 
    public function setShowCalendarDefault($show_calendar_default)
    {
        $this->show_calendar_default = $show_calendar_default;

        return $this;
    }

    /**
     * Get the value of feed_start_format
     *
     * @return  string
     */ 
    public function getFeedStartFormat()
    {
        return $this->feed_start_format;
    }

    /**
     * Set the value of feed_start_format
     *
     * @param string $feed_start_format Format string for calendar feed start
     *
     * @return self
     */ 
    public function setFeedStartFormat($feed_start_format)
    {
        $this->feed_start_format = $feed_start_format;

        return $this;
    }

    /**
     * Get the value of feed_end_format
     *
     * @return  string
     */ 
    public function getFeedEndFormat()
    {
        return $this->feed_end_format;
    }

    /**
     * Set the value of feed_end_format
     *
     * @param string $feed_end_format String of end date format
     *
     * @return self
     */ 
    public function setFeedEndFormat($feed_end_format)
    {
        $this->feed_end_format = $feed_end_format;

        return $this;
    }
}