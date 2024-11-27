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
import { Windows } from './Windows.js';
window.debug= true;
console.log("CHECKPOINT");

function initPopup() {
    // w.create();

}

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


}



showPopup();
init();

async function showPopup() {
    const w=new Windows();
    window.w = w;

    w.addStyle(
        {
            width: "150px",
            height: "150px" ,
            "max-width":"50w",
            "max-height":"50vw",
            background: "linear-gradient(to top right,rgb(0, 168, 99),rgba(0, 230, 160,1))",
            display: "flex",
            "justify-content": "center",
            "align-items":"center",
            "font-size": "1.5rem",
            transform: "translate(0, 200vh) !important",

        }
    )

    w.setContent("<center>Erfolgreich gespeichert</center>");
    await w.start("BottomToCenter");
    await w.start("opacity");

    // await new Promise(e => setTimeout(e,5));
    // await w.start();
    // w.addAnimation("opacity")
    // await w.start(); 
    // w.hide();   
}

