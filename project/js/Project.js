import { ProjectCalendar } from './ProjectCalendar.js';


function calendarNext(event,day) {
    calendar.setCalendarInformation(date,day)
}


function init() {
    // project=new Project();
    let calendar=new ProjectCalendar();
    window.calendar=calendar;

    document.getElementById("calendar").innerHTML=calendar.render.renderCalendar();
    calendar.render.addCaledarSetupListener();
}


init();

