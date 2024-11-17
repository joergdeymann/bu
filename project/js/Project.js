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


class Options {
    calendarShowNames= false;
    calendarOnlyMainColors= false;
    calendarDefaultOptionDate=true;
    calendarDefaultOptionTravel= true;
    calendarInputDate = true;
    calendarInputTravel = true;
}    

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

    let setup=new Setup();
    window.setup=setup;
}


async function XPHP() {
    let p=new Query("SELECT * FROM `bu_project_jobs`  ORDER BY father; ");
    let data=await p.getData();
    console.log(data);
}

// XPHP();
init();

