var fs = require('fs');

// Copy full calendar to client
fs
    .createReadStream('node_modules/fullcalendar/index.global.min.js')
    .pipe(fs.createWriteStream('javascript/fullcalendar.min.js'));

    // Copy full calendar moment to client
    fs
        .createReadStream('node_modules/@fullcalendar/moment/index.global.min.js')
        .pipe(fs.createWriteStream('javascript/fullcalendar-moment.min.js'));
