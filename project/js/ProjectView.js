import { Login } from './Login.js';
import { Setup } from './Setup.js';

import { ProjectCalendar } from './ProjectCalendar.js';
import { ProjectJobDefinition } from './ProjectJobDefinition.js';
import { EventTimeList } from './EventTimeList.js';
import { EventFrame } from './EventFrame.class.js';


window.debug= true;

async function init() {
    
    let login=new Login();
    window.login=login;
    let logged=await login.getSession();
    if (!logged) return;

    let opt=new Setup();
    window.opt=opt;

    
    let job = new ProjectJobDefinition();
    window.job = job;



    let calendar=new ProjectCalendar();
    calendar.renderCalendarAll();
    window.calendar=calendar;
    

    let eventTimeList = new EventTimeList();
    window.eventTimeList=eventTimeList;

    let eventFrame = new EventFrame();
    window.eventFrame=eventFrame;

    await calendar.timeline.get();
    
    eventFrame.renderList();
    
}

init();





