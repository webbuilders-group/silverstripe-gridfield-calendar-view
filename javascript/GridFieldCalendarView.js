(function($) {
    $.entwine('ss', function($) {
        $('.ss-gridfield .calendar-view-mode-toggle li a').entwine({
            onclick: function(e) {
                //Remove all active
                this.parent().siblings('.active').removeClass('active');
                
                //Mark this as the active one
                this.parent().addClass('active');
                
                
                //Switch the view mode
                if(this.attr('data-view-mode')=='calendar') {
                    this.closest('.ss-gridfield').find('.ss-gridfield-table').hide();
                    this.closest('.ss-gridfield').find('.ss-gridfield-calendar').show().redraw();
                }else {
                    this.closest('.ss-gridfield').find('.ss-gridfield-calendar').hide();
                    this.closest('.ss-gridfield').find('.ss-gridfield-table').show();
                }
                
                
                return false;
            }
        });
        
        
        $('.ss-gridfield .ss-gridfield-calendar').entwine({
            GridFieldID: null,
            Rendered: false,
            
            redraw: function() {
                var self=this;
                
                //If already rendered bail
                if(this.getRendered()) {
                    return;
                }
                
                
                var gridField=this.closest('.ss-gridfield');
                this.setGridFieldID(gridField.attr('id'));
                
                
                /**** Bootstrap Calendar ****/
                var calendar=self.find('.calendar-display');
                var stateField=gridField.find('.gridstate');
                calendar.fullCalendar({
                    editable: false,
                    eventLimit: true,
                    events: {
                        url: self.attr('data-calendar-feed'),
                        type: 'POST',
                        startParam: 'start-date',
                        endParam: 'end-date',
                        data: function() {
                            var dataObj={
                                        SecurityID: self.closest('form').find('input[name=SecurityID]').val()
                                    };
                            
                            dataObj[stateField.attr('name')]=stateField.val();
                            
                            return dataObj;
                        },
                        error: function() {
                            jQuery.noticeAdd({text: 'Error loading calendar, please try again later', type: 'error', stayTime: 5000, inEffect: {left: '0', opacity: 'show'}});
                        },
                        className: 'cms-panel-link'
                    },
                    buttonIcons: {
                        prev: ' font-icon-left-open-big',
                        next: ' font-icon-right-open-big',
                    },
                    
                    /**
                     * Handles when the view is destroyed
                     * @param {object} view Calendar View Object
                     * @param {object} element jQuery object representing the view
                     */
                    viewDestroy: function(view, element) {
                        //Remove all calendar tips
                        $('.gridfield-calendar-tip').remove();
                    },
                    
                    /**
                     * Handles when the the loading state for the calendar changes
                     * @param {boolean} isLoading Whether or not the event calendar is loading or not
                     */
                    loading: function(isLoading) {
                        self.closest('form').toggleClass('loading', isLoading);
                    },
                    
                    /**
                     * Shows/Creates a tooltip when the mouse is over an event item
                     * @param {object} event Calendar Event Object
                     * @param {MouseEvent} jsEvent JavaScript Mouse Event
                     * @param {object} view Calendar View Object
                     */
                    eventMouseover: function(event, jsEvent, view) {
                        var tip=$('#'+self.getGridFieldID()+'_calendar_tt'+event._id);
                        if(tip.length==0) {
                            tip=$('<div class="gridfield-calendar-tip"></div>');
                            tip.attr('id', self.getGridFieldID()+'_calendar_tt'+event._id);
                            tip.addClass(event.className.join(' '));
                            tip.append($('<p class="evt-title"/>').text(event.title));
                            
                            //Figure out the event range format
                            var dateTimeStr=false;
                            if(event.end) {
                                var startMonth=event.start.format('MMM D');
                                var startTime=event.start.format('h:mma');
                                var endMonth=event.end.format('MMM D');
                                var endTime=event.end.format('h:mma');
                                
                                if(startMonth==endMonth) {
                                    dateTimeStr=startMonth;
                                    
                                    if(event.allDay==false) {
                                        dateTimeStr+=' @ '+startTime+' - '+endTime;
                                    }
                                }else {
                                    dateTimeStr=startMonth+' - '+endMonth;
                                    
                                    if(event.allDay==false) {
                                        if(startTime==endTime) {
                                            dateTimeStr+=' @ '+startTime;
                                        }else {
                                            dateTimeStr+=' @ '+startTime+' - '+endTime;
                                        }
                                    }
                                }
                            }else {
                                dateTimeStr=event.start.format('MMM D @ h:mma');
                            }
                            
                            if(dateTimeStr) {
                                tip.append($('<p class="evt-time-range"/>').text(dateTimeStr));
                            }
                            
                            if(event.abstractText) {
                                tip.append('<hr />');
                                tip.append($('<p class="evt-abstract"/>').text(event.abstractText));
                            }
                            
                            //Append to the dom
                            $(document.body).append(tip);
                        }
                        
                        
                        var element=$(jsEvent.currentTarget);
                        var elementPos=element.offset();
                        tip
                            .css('left', ((elementPos.left+(element.outerWidth()/2))-161)+'px')
                            .css('top', (elementPos.top+element.outerHeight())+'px')
                            .show();
                    },
                    
                    /**
                     * Hides the tooltip when the mouse leaves an event item
                     * @param {object} event Calendar Event Object
                     * @param {MouseEvent} jsEvent JavaScript Mouse Event
                     * @param {object} view Calendar View Object
                     */
                    eventMouseout: function(event, jsEvent, view) {
                        var tip=$('#'+self.getGridFieldID()+'_calendar_tt'+event._id);
                        if(tip.length>0) {
                            tip.hide();
                        }
                    }
                });
                
                this.setRendered(true);
            }
        });
    });
})(jQuery);
