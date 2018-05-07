<div class="calendar-view-mode-toggle">
    <ul>
        <li<% if not $Default %> class="active"<% end_if %>>
            <a href="#" class="calendar-view-list icon-button font-icon-list" title="List View" data-view-mode="default"><!-- --></a>
        </li>
        <li<% if $Default %> class="active"<% end_if %>>
            <a href="#" class="calendar-view-month" title="Calendar View" data-view-mode="calendar"><!-- --></a>
        </li>
    </ul>
</div>