import { ProjectInputs } from './ProjectInputs.js';
import { ProjectCalendar } from './ProjectCalendar.js';
import { ProjectPrice } from './ProjectPrice.js';
// import { Query } from './Query.js';
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

import { DB_Address } from './DB_Address.js';
import { DB_Project } from './DB_Project.js';
import { DB_Customer } from './DB_Customer.js';
import { DB_Article } from './DB_Article.js';
import { DB_EventPrice } from './DB_EventPrice.js';


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

    let db_address = new DB_Address();
    window.db_address=db_address;

    let db_project = new DB_Project();
    window.db_project=db_project;

    let db_customer = new DB_Customer();
    window.db_customer=db_customer;

    let db_articlePrice = new DB_Article();
    window.db_articlePrice=db_articlePrice;

    let db_eventPrice = new DB_EventPrice();
    window.db_eventPrice=db_eventPrice;

}


init();

