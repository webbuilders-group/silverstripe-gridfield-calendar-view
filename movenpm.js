var fs = require('fs');

// Copy full calendar to client
fs
    .createReadStream('node_modules/fullcalendar/dist/fullcalendar.min.js')
    .pipe(fs.createWriteStream('javascript/fullcalendar.min.js'));
fs
    .createReadStream('node_modules/fullcalendar/dist/fullcalendar.min.css')
    .pipe(fs.createWriteStream('css/fullcalendar.min.css'));

// Copy moment to client
fs
    .createReadStream('node_modules/moment/min/moment.min.js')
    .pipe(fs.createWriteStream('javascript/moment.min.js'));