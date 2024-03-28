# Change Log

## [3.0.0](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/3.0.0) (2024-03-28)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/2.2.1...3.0.0)
- Breaking change you can no longer simply extend the calendar using `gridfield_calendar_data`. You must use jQuery entwine and get the default options using `this.getCalendarOptions()` on the `.ss-gridfield .ss-gridfield-calendar` element, make your changes and call `this.setCalendarOptions()` passing in the modified options before calling `this._super()`.

## [2.2.1](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/2.2.1) (2023-11-08)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/2.2.0...2.2.1)

## [2.2.0](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/2.2.0) (2023-11-07)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/2.1.3...2.2.0)

## [2.1.3](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/2.1.3) (2023-11-03)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/2.1.2...2.1.3)

## [2.1.2](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/2.1.2) (2023-11-02)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/2.1.1...2.1.2)

## [2.1.1](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/2.1.1) (2023-11-02)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/2.1.0...2.1.1)

## [2.1.0](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/2.1.0) (2023-11-01)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/2.0.1...2.1.0)

## [2.0.1](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/2.0.1) (2019-03-26)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/2.0.0...2.0.1)

## [2.0.0](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/2.0.0) (2019-01-19)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/1.0.0...2.0.0)

## [1.0.0](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/1.0.0) (2018-03-26)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/0.1.3...1.0.0)

## [0.1.3](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/0.1.3) (2018-01-09)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/0.1.2...0.1.3)

## [0.1.2](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/0.1.2) (2017-02-16)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/0.1.1...0.1.2)

## [0.1.1](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/0.1.1) (2016-12-19)
[Full Changelog](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/compare/0.1.0...0.1.1)

**Fixed bugs:**

- The method 'getisdeletedfromstage' does not exist on 'CalendarEvent' [\#2](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/issues/2)

## [0.1.0](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/tree/0.1.0) (2016-10-06)
**Implemented enhancements:**

- Remember the view state [\#1](https://github.com/webbuilders-group/silverstripe-gridfield-calendar-view/issues/1)
