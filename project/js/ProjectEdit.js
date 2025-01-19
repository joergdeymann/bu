import { ProjectInputs } from './ProjectInputs.js';
import { ProjectCalendar } from './ProjectCalendar.js';
import { ProjectPrice } from './ProjectPrice.js';
// import { ProjectOvertime } from './ProjectOvertime.js';
// import { Query } from './Query.js';
import { CustomerList } from './CustomerList.js';
import { EquipmentList } from './EquipmentList.js';
import { EquipmentPrice } from './EquipmentPrice.js';
import { UnterkunftList } from './UnterkunftList.js';
import { Login } from './Login.js';
import { Setup } from './Setup.js';
import { ProjectJobDefinition } from './ProjectJobDefinition.js';
import { JobHierachy } from './JobHierachy.js';
import { ProjectSave } from './ProjectSave.js';
import { EventList } from './EventList.js';

import { DB_Address } from './DB_Address.js';
import { DB_Project } from './DB_Project.js';
import { DB_Customer } from './DB_Customer.js';
import { IF_ProjectNew } from './IF_ProjectNew.js';
import { DB_Article } from './DB_Article.js';
import { DB_EventPrice } from './DB_EventPrice.js';
import { DB_ProjectJob } from './DB_ProjectJob.js';
import { DB_TimeEquipmentList } from './DB_TimeEquipmentList.js';
import { DB_TimeJob } from './DB_TimeJob.js';
import { DB_TimeWorker } from './DB_TimeWorker.js';
import { DB_ProjectEdit } from './DB_ProjectEdit.class.js';
import { DB_Dayrate } from './DB_Dayrate.js';
// import { DB_WorkPrice } from './DB_WorkPrice.js';


window.debug= true;

async function init() {
    let eventFrame=null;
    window.eventFrame=eventFrame;
    
    let login=new Login();
    window.login=login;
    let logged=await login.getSession();
    if (!logged) return;

    let opt=new Setup();
    window.opt=opt;

    let project=new ProjectInputs();
    window.project=project;
    
    let job_hierarchy=new JobHierachy();
    await job_hierarchy.get();

    let job = new ProjectJobDefinition();
    window.job = job;

    let db_projectEdit=new DB_ProjectEdit();      
    window.db_projectEdit=db_projectEdit;
    await db_projectEdit.loadValues(36);                        // ### get it from session or form ?



    // ##########################################
    // Hier muss ich das Datum des Projekts haben
    // ##########################################
    let calendar=new ProjectCalendar();
    calendar.setDate(db_projectEdit.getDateInRange());
    calendar.renderCalendarAll();
    window.calendar=calendar;

    await job.outputHeadlines() ;
    // job.renderJobHeadline();


    let if_projectNew = new IF_ProjectNew();
    window.if_projectNew=if_projectNew;

    let projectPrice=new ProjectPrice();
    window.projectPrice=projectPrice;

    db_projectEdit.fillNewForm(); // choosen time_worker_id
    
    
 
    // let projectOvertime=new ProjectOvertime();
    // window.projectOvertime=projectOvertime;
    
    let customer=new CustomerList();
    window.customerList=customer;

    let equipmentList=new EquipmentList();
    equipmentList.addCalendar(calendar);
    window.equipmentList=equipmentList;
    
    let equipmentPrice=new EquipmentPrice();
    window.equipmentPrice=equipmentPrice;

    let db_timeEquipmentList = new DB_TimeEquipmentList();
    window.db_timeEquipmentList=db_timeEquipmentList;
    
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

    let db_projectJob = new DB_ProjectJob();
    window.db_projectJob=db_projectJob;

    let db_customer = new DB_Customer();
    window.db_customer=db_customer;

    // let if_projectNew = new IF_ProjectNew();
    // window.if_projectNew=if_projectNew;

    let db_articlePrice = new DB_Article();
    window.db_articlePrice=db_articlePrice;

    let db_eventPrice = new DB_EventPrice();
    window.db_eventPrice=db_eventPrice;

    let db_timeJob = new DB_TimeJob();
    window.db_timeJob=db_timeJob;
 
    let db_timeWorker = new DB_TimeWorker();
    window.db_timeWorker=db_timeWorker;

    let db_dayrate = new DB_Dayrate();
    window.db_dayrate=db_dayrate;

    // let db_workPrice = new DB_WorkPrice();
    // window.db_workPrice=db_workPrice;
    await if_projectNew.get();
    db_projectEdit.fillNewFormLast(); 
    db_timeEquipmentList.addHTML();

}


init();

