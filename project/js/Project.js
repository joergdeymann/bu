import { ProjectCalendar } from './ProjectCalendar.js';
import { Request } from './Request.js';


function init() {
    let calendar=new ProjectCalendar();
    window.calendar=calendar;

    document.getElementById("calendar").innerHTML=calendar.render.renderCalendar();
    calendar.render.addCaledarSetupListener();
}


async function XPHP() {
    let p=new Request("SELECT * FROM `bu_project_jobs`  ORDER BY father; ");
    let data=await p.getData();
    console.log(data);
}

XPHP();
init();

