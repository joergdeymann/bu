import { ProjectInputs } from './ProjectInputs.js';
import { ProjectCalendar } from './ProjectCalendar.js';
import { ProjectPrice } from './ProjectPrice.js';
import { Query } from './Query.js';
import { CustomerList } from './CustomerList.js';
import { EquipmentList } from './EquipmentList.js';
import { EquipmentPrice } from './EquipmentPrice.js';
import { UnterkunftList } from './UnterkunftList.js';
import { Login } from './Login.js';
import { Setup } from './Setup.js';
import { ProjectJob } from './ProjectJob.js';
import { JobHierachy } from './JobHierachy.js';
import { ProjectSave } from './ProjectSave.js';
import { EventList } from './EventList.js';

window.debug= true;

async function init() {
    
    let login=new Login();
    window.login=login;
    let logged=await login.getSession();
    if (!logged) return;

    let opt=new Setup();
    window.opt=opt;

    
    let job_hierarchy=new JobHierachy();
    await job_hierarchy.get();

    let job = new ProjectJob();
    window.job = job;



    let calendar=new ProjectCalendar();
    calendar.renderCalendarAll();
    window.calendar=calendar;

    await job.outputHeadlines() ;
    // job.renderJobHeadline();
    
    let project=new ProjectInputs();
    window.project=project;

    let projectPrice=new ProjectPrice();
    window.projectPrice=projectPrice;
    
    let customer=new CustomerList();
    window.customerList=customer;

    let equipmentList=new EquipmentList();
    equipmentList.addCalendar(calendar);
    window.equipmentList=equipmentList;

    let equipmentPrice=new EquipmentPrice();
    window.equipmentPrice=equipmentPrice;
    
    let unterkunftList=new UnterkunftList();
    window.unterkunftList=unterkunftList;

    let projectSave = new ProjectSave();
    window.projectSave=projectSave;

    let eventList = new EventList();
    window.eventList=eventList;

}


init();

