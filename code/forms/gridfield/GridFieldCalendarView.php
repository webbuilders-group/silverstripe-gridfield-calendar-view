<?php
class GridFieldCalendarView implements GridField_HTMLProvider, GridField_URLHandler {
    private $_startDateField;
    private $_endDateField;
    private $_togglePosition;
    private $_titleField;
    private $_summaryField;
    private $_allDayField;
    
    
    /**
     * Constructor
     * @param {string} $startDateField Name of the Start Date field
     * @param {string} $endDateField Name of the End Date field
     * @param {string} $togglePosition Position of the toggle controls
     * @param {string} $titleField Name of the field to be used for the title in the calendar
     * @param {string} $summaryField Name of the field to be used for the summary in the calendar
     * @param {string} $allDayField Name of the field to be used to determin if the field is an all day event result must be boolean like
     */
    public function __construct($startDateField, $endDateField, $togglePosition='buttons-before-left', $titleField='Title', $summaryField='Summary', $allDayField='IsAllDay') {
        $this->_startDateField=$startDateField;
        $this->_endDateField=$endDateField;
        $this->_togglePosition=$togglePosition;
        $this->_titleField=$titleField;
        $this->_summaryField=$summaryField;
        $this->_allDayField=$allDayField;
    }
    
    /**
     * Returns a map where the keys are fragment names and the values are pieces of HTML to add to these fragments.
     * @return {array}
     */
    public function getHTMLFragments($gridField) {
        $dataList=$gridField->getList();
        
        
        $calendarData=new ArrayData(array(
                                        'FeedLink'=>$gridField->Link('calendar-data-feed')
                                    ));
        
        
        $toggleData=new ArrayData(array());
        

        Requirements::css(SS_GFCV_BASE.'/javascript/externals/fullcalendar/fullcalendar.min.css');
        Requirements::css(SS_GFCV_BASE.'/css/GridFieldCalendarView.css');

        Requirements::javascript(SS_GFCV_BASE.'/javascript/externals/fullcalendar/moment.min.js');
        Requirements::javascript(SS_GFCV_BASE.'/javascript/externals/fullcalendar/fullcalendar.min.js');
        Requirements::javascript(SS_GFCV_BASE.'/javascript/GridFieldCalendarView.js');
        
        return array(
                    'after'=>$calendarData->renderWith('GridFieldCalendarView'),
                    $this->_togglePosition=>$toggleData->renderWith('GridFieldCalendarView_toggle')
                );
    }
    
    /**
     * Sets the start date/time field name
     * @param {string} $field Name of the field to be used for the start date/time, this must match the model it cannot be a getter
     * @return {GridFieldCalendarView}
     */
    public function setStartDateField($field) {
        $this->_startDateField=$field;
        
        return $this;
    }
    
    /**
     * Gets the start date/time field used
     * @return {string}
     */
    public function getStartDateField($field) {
        return $this->_startDateField;
    }
    
    /**
     * Sets the end date/time field name
     * @param {string} $field Name of the field to be used for the end date/time
     * @return {GridFieldCalendarView}
     */
    public function setEndDateField($field) {
        $this->_endDateField=$field;
        
        return $this;
    }
    
    /**
     * Gets the end date/time field used
     * @return {string}
     */
    public function getEndDateField() {
        return $this->_endDateField;
    }
    
    /**
     * Sets the position of the list/calendar toggle button
     * @param {string} $location Location for the component to be added to the GridField
     * @return {GridFieldCalendarView}
     */
    public function setTogglePosition($location) {
        $this->_togglePosition=$location;
        
        return $this;
    }
    
    /**
     * Gets the position of the list/calendar toggle button
     * @return {string}
     */
    public function getTogglePosition() {
        return $this->_togglePosition;
    }
    
    /**
     * Sets the title field name
     * @param {string} $field Name of the field to be used for the title in the calendar
     * @return {GridFieldCalendarView}
     */
    public function setTitleField($field) {
        $this->_titleField=$field;
        
        return $this;
    }
    
    /**
     * Gets the title field used
     * @return {string}
     */
    public function getTitleField() {
        return $this->_titleField;
    }
    
    /**
     * Sets the summary field name
     * @param {string} $field Name of the field to be used for the summary in the calendar
     * @return {GridFieldCalendarView}
     */
    public function setSummaryField($field) {
        $this->_summaryField=$field;
        
        return $this;
    }
    
    /**
     * Gets the summary field used
     * @return {string}
     */
    public function getSummaryField() {
        return $this->_summaryField;
    }
    
    /**
     * Sets the all day field name
     * @param {string} $field Name of the field to be used to determin if the field is an all day event result must be boolean like
     * @return {GridFieldCalendarView}
     */
    public function setAllDayField($field) {
        $this->_allDayField=$field;
        
        return $this;
    }
    
    /**
     * Gets the all day field used
     * @return {string}
     */
    public function getAllDayField() {
        return $this->_allDayField;
    }
    
    /**
     * Return URLs to be handled by this grid field component, in an array the same form as $url_handlers.
     * @return {array}
     */
    public function getURLHandlers($gridField) {
        return array(
                    'calendar-data-feed'=>'handleCalendarFeed'
                );
    }
    
    /**
     * Handles retrieving the data for the calendar
     * @param {GridField} $gridField GridField instance
     * @param {SS_HTTPRequest} $request HTTP Request Object
     * @return {string} Response JSON
     */
    public function handleCalendarFeed(GridField $gridField, SS_HTTPRequest $request) {
        //Validate Security Token
        if(!SecurityToken::inst()->checkRequest($request)) {
            return $this->httpError(403, 'Security Token Expired or Invalid');
        }
        
        
        //Figure out the start date
        $startTS=strtotime($request->postVar('start-date'));
        if($request->postVar('start-date') && $startTS!==false) {
            //Push the date into the next month if the first visible day on the calendar is not 1
            if(date('j', $startTS)!=1) {
                $startDate=date('Y-m-01', strtotime(date('Y-m-01', $startTS).' next month'));
            }else {
                $startDate=date('Y-m-d', $startTS);
            }
        }else {
            $startDate=date('Y-m-01');
        }
        
        
        //Figure out the end date
        $endTS=strtotime($request->postVar('end-date'));
        if($request->postVar('end-date') && $endTS!==false) {
            //Push the date into the previous month if the last visible day on the calendar is less then 28
            if(date('j', $endTS)<28) {
                $endDate=date('Y-m-t', strtotime(date('Y-m-01', $endTS).' previous month'));
            }else {
                $endDate=date('Y-m-d', $endTS);
            }
        }else {
            $endDate=date('Y-m-t');
        }
        
        
        //Fetch the month's events
        $list=$gridField->getList();
        if($deletedManip=$gridField->getConfig()->getComponentByType('GridFieldDeletedManipulator')) {
            $list=$deletedManip->getManipulatedData($gridField, $list);
        }
        
        $events=$list
                    ->filter($this->_startDateField.':GreaterThanOrEqual', date('Y-m-01 00:00:00', strtotime($startDate)))
                    ->filter($this->_startDateField.':LessThanOrEqual', date('Y-m-'.date('t').' 11:59:59', strtotime($endDate)))
                    ->sort($this->_startDateField);
        
        
        //Build the response data
        $result=array();
        foreach($events as $event) {
            $result[]=array(
                            'title'=>$event->{$this->_titleField},
                            'abstractText'=>$event->{$this->_summaryField},
                            'allDay'=>(bool) $event->{$this->_allDayField},
                            'start'=>date('c', strtotime($event->{$this->_startDateField})),
                            'end'=>date('c', strtotime($event->{$this->_endDateField})),
                            'url'=>Controller::join_links($gridField->Link('item'), $event->ID, 'edit'),
                            'className'=>($event->getIsDeletedFromStage() ? 'deleted-event':null)
                        );
        }
        
        
        //Serialize to json
        $result=json_encode($result);
        
        
        //Respond with the resulting json
        $response=Controller::curr()->getResponse();
        $response->addHeader('Content-Type', 'application/json; charset=utf-8');
        return $result;
    }
}
?>