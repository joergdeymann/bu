import { Request } from './Request.js';

export class EquipmentList {
    
    
    constructor() {
        this.setElements();
        this.addEvents();
    }

    addCalendar(calendar) {
        this.calendar=calendar;
    }


    /**
     * 
     * --- !!! MUST CHANGES !!! --
     * 
     * Change the right Side customerXX ind your ids given in HTML
     *  
     */
    setElements() {
        this.list=document.getElementById("equipment-list");
        this.listContainer=this.list.parentElement;
        this.input=document.getElementsByName("equipmentName[]")[0];
        this.inputId=document.getElementsByName("equipmentId[]")[0];
    }

    filterList() {
        return this.data.filter(e=>e.name && e.name.toLowerCase().includes(this.input.value.toLowerCase()));  
    }

    /**
     * 
     * --- !!! MUST CHANGES !!! --
     * 
     * 1. firma -> get it from SESSION variabble
     * 2. Change the whole SELECT Request 
     *  
     */

    async load() { 
        let von=this.calendar.newEntry.start;
        let bis=this.calendar.newEntry.end;
        let firma=login.companyId; 

        let date=`0`;
        if (von == '' && bis != '') {
            date=`CASE WHEN MAX(CASE WHEN eq.von <= '${bis}' THEN 1 ELSE 0 END) = 1 THEN 1 ELSE 0 END`;
        } else if (von != '' && bis == '') {
            date=`CASE WHEN MAX(CASE WHEN eq.bis >= '${von}' THEN 1 ELSE 0 END) = 1 THEN 1 ELSE 0 END`;
        } else if (von != '' && bis != '') {
            date=`CASE WHEN MAX(CASE WHEN eq.bis >= '${von}' AND eq.von <= '${bis}' THEN 1 ELSE 0 END) = 1 THEN 1 ELSE 0 END`;
        }

        let p=new Request(`SELECT art.recnum, art.name, art.netto AS art_netto, art.mwst, art.auftraggeber,eq.von,eq.bis, MAX(pj.name) AS pj_name, ${date} AS inuse FROM bu_artikel art LEFT JOIN bu_project_equipment eq ON eq.equipment_recnum = art.recnum LEFT JOIN bu_projekt pj ON pj.recnum = eq.project_recnum WHERE art.auftraggeber = ${firma} AND art.leistung = 1 GROUP BY art.recnum, art.name, art.netto, art.mwst, art.auftraggeber ORDER BY art.auftraggeber;`);         
        this.data=await p.get();
        
        this.render();
        this.listContainer.classList.remove("d-none");
        // this.addInputEvent();
    }

    /**
     * 
     * --- !!! MUST CHANGES !!! --
     * 
     * 1. Headline <h1>
     * 2. row.recnum maybe ind row,id -> dlook at Database
     * 3. row.firma -> choose other fields
     *  
     */
    render() {
        let html="<h1>Was bringst du mit?</h1>";
        html+=/*html*/`<div class="selector-headline" onclick="equipmentList.clearField()">Zurücksetzten</div>`

        for(let row of this.filterList()) {
            // Version 1 = nicht auswählbar: let click=+row.inuse?'class="red"':`onclick="equipmentList.selectEquipment(${row.recnum})"`;
            
            let click=`onclick="equipmentList.selectEquipment(${row.recnum})"`+ (+row.inuse?' class="red"':``);
            let info=+row.inuse?`<br>(${this.getGermanDate(row.von)} - ${this.getGermanDate(row.bis)})`:``;
            html+=/*html*/`<div ${click}>${row.name} ${info}</div>`;
        }
        this.list.innerHTML=html;
    }

    addEvents() {
        this.listContainer.querySelector(".blocker").addEventListener("mousedown",event => {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";

            event.preventDefault();
            event.stopPropagation();
        })
        this.list.addEventListener("mousedown",event=> {
            event.preventDefault();
            event.stopPropagation();
        })
        this.addInputEvent();

    }

    addInputEvent() {
        this.removeInputEvent();

        this.input.addEventListener("input",this.handleInputEvent);
        this.input.classList.add("listener");
    }


    handleInputEvent= () => {
        if (!this.listContainer.classList.contains("d-none")) {
            this.render();
        }
    }

    removeInputEvent() {
        
        let element=document.querySelector("input.listener");
        if (element == null) {
            console.log("Input Listener war schon weg");
            return;
        }
        element.classList.remove("listener");
        element.removeEventListener("input",this.handleInputEvent);
    }

    setWindow(event) {
        if(this.listContainer.classList.contains("d-none")) { 
            event.target.closest(".input-container").insertAdjacentElement("afterend", document.getElementById("popup"));
            let parent=event.target.parentElement;
            this.input=parent.querySelector('input[name="equipmentName[]"]');
            this.inputId =parent.querySelector('input[name="equipmentId[]"]');
        }


        this.toggleWindow();
    }

    async toggleWindow() {
        if(this.listContainer.classList.contains("d-none")) { 
            await this.load();
            this.input.style.zIndex=2;
            this.addInputEvent();

        } else {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";
        };
        if (this.filterList().length>5) this.input.focus(); // On demanmd
    }

    selectEquipment(id) {
        let customer=this.data.find(e => e.recnum==id);
        this.input.value=customer.name;
        this.inputId.value=customer.recnum;
        this.toggleWindow();
    }
    
    getGermanDate(d) {
        if (d =='' ) return '';
        let date = new Date(d);
        return `${String(date.getDate()).padStart(2, "0")}.${String(date.getMonth() + 1).padStart(2, "0")}.${date.getFullYear()}`;
    }

    addInputField(event) {
        let newContainer = document.createElement("div");
        newContainer.classList.add("input-container");
        newContainer.classList.add("equipment");
        newContainer.innerHTML=/*html*/`
            <input type="hidden" name="equipmentId[]">
            <input type="text" name="equipmentName[]"  placeholder="Was bringst du mit">
            <button class="small" type="button" onclick="equipmentList.setWindow(event)">&#128315;</button>
        `; 
        
        event.target.closest(".input-container").insertAdjacentElement("beforebegin", newContainer);
        

    }

    removeInputField(event) {
        let list=document.querySelectorAll(".input-container.equipment");
        let len=list.length;
        if (len>0) {
            list[len-1].remove();
        }
        // event.target.parentElement.priviousElementSibling.remove();

    }

    clearField() {
        let inputId = document.activeElement.parentElement.querySelector('input[name="equipmentId[]"]');
        let input=document.activeElement.parentElement.querySelector('input[name="equipmentName[]"]');

        input.value="";
        inputId.value="";
        this.toggleWindow();
    } 


}
