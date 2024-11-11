import { ProjectInputs } from './ProjectInputs.js';
import { ProjectCalendar } from './ProjectCalendar.js';
import { Request } from './Request.js';
import { CustomerList } from './CustomerList.js';
import { EquipmentList } from './EquipmentList.js';
import { UnterkunftList } from './UnterkunftList.js';
import { Login } from './Login.js';



async function init() {
    let login=new Login();
    window.login=login;
    let logged=await login.getSession();
    if (!logged) return;

    let calendar=new ProjectCalendar();
    calendar.renderCalendarAll();
    calendar.renderJobHeadline();

    window.calendar=calendar;
    
    let project=new ProjectInputs();
    window.project=project;
    
    let customer=new CustomerList();
    window.customerList=customer;

    let equipmentList=new EquipmentList();
    equipmentList.addCalendar(calendar);
    window.equipmentList=equipmentList;
    
    let unterkunftList=new UnterkunftList();
    window.unterkunftList=unterkunftList;
}


async function XPHP() {
    let p=new Request("SELECT * FROM `bu_project_jobs`  ORDER BY father; ");
    let data=await p.getData();
    console.log(data);
}

// XPHP();
init();

