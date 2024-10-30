import { ProjectCalendar } from './ProjectCalendar.js';

/*
class Project  {
    #calendar;

    constructor() {
        this.#calendar=new ProjectCalendar();
    }



}
*/
// import { ProjectCalendar } from './ProjectCalendar.js';

function init() {
    // project=new Project();
    let calendar=new ProjectCalendar();
    document.getElementById("calendar").innerHTML=calendar.renderCalendar();

}
init();