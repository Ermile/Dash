// Morede estefade baraye zaman bandi

(function(root) {
  "use strict";

  var Schedule = function Schedule(datetime, fn) {
    var diff = datetime - Date.now();
    if(diff < 0) return false;

    setTimeout(fn, diff);
  };

  Schedule.parseInputs = function parseInputs(date, time) {
    var dateArray = $(date).val().split('/'),
        timeArray = $(time).val().split(':');

    var datetime = dateArray.concat(timeArray)
                 .map(function(a) {
                   return parseInt(a, 10);
                 });

    return new pDate(datetime);
  };

  root.Schedule = Schedule;
})(this);