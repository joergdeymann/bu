import { Query } from './Query.js';

export class EquipmentList {
    filteredList=[]
    
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
        this.inputDisplay =this.input.closest(".input-container").querySelector('.right');
    }

    filterList() {
        
        return this.filteredList=this.data.filter(e=>e.name && e.name.toLowerCase().includes(this.input.value.toLowerCase()));  
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
        const variables = {
            to: this.calendar.newEntry.end,
            from: this.calendar.newEntry.start,
            customerId: calendar.newEntry.customerId,
            companyId: login.companyId
        };
        
        // let date=`0`;
        // if (from == '' && to != '') {
        //     date=`CASE WHEN MAX(CASE WHEN eq.from <= '${to}' THEN 1 ELSE 0 END) = 1 THEN 1 ELSE 0 END`;
        // } else if (from != '' && to == '') {
        //     date=`CASE WHEN MAX(CASE WHEN eq.to >= '${from}' THEN 1 ELSE 0 END) = 1 THEN 1 ELSE 0 END`;
        // } else if (from != '' && to != '') {
        //     date=`CASE WHEN MAX(CASE WHEN eq.to >= '${from}' AND eq.from <= '${to}' THEN 1 ELSE 0 END) = 1 THEN 1 ELSE 0 END`;
        // }
        // ${calendar.newEntry.customerId}
        // ${login.companyId}
        let request=`
            SELECT
                art.id,
                art.name,
                art.price,
                art.vat,
                ep.price AS customerPrice,
                MIN(CASE 
                        WHEN left(te.from,10) <= @to AND left(te.to,10) >= @from 
                        THEN te.from 
                        ELSE NULL 
                    END) AS 'from',
                MAX(CASE 
                        WHEN left(te.from,10) <= @to AND left(te.to,10) >= @from 
                        THEN te.to 
                        ELSE NULL 
                    END) AS 'to',
                CASE 
                    WHEN COUNT(CASE 
                                WHEN left(te.from,10) <= @to AND left(te.to,10) >= @from AND @to != '' AND @from != ""
                                THEN 1 
                                ELSE NULL 
                            END) > 0 
                    THEN 1 
                    ELSE 0 
                END AS inUse
            FROM
                bu_article art
            LEFT JOIN 
                bu_equipment_price ep
                ON art.id = ep.articleId
                AND ep.customerId = @customerId
            LEFT JOIN 
                bu_time_equipment te
                ON te.articleId = art.id
                AND te.companyId = art.companyId
            WHERE
                art.companyId = @companyId
                AND art.usage = 1
            GROUP BY
                art.id, art.name, art.price, art.vat, ep.price
            ORDER BY
                art.companyId;
       `;

        
        let p=new Query(this.replaceAt(request,variables));
        this.data=await p.get();
        
        this.render();
        this.listContainer.classList.remove("d-none");
        // this.addInputEvent();
    }

    replaceAt(sql,variables) {
        return  sql.replace(/@\w+/g, match => {
            const varName = match.slice(1); // Entferne das '@'
            const value = variables[varName];
            
            if (value === undefined) return match; 
            return typeof value === "string" ? `"${value}"` : value;
        });    
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
            
            let click=`onclick="equipmentList.selectEquipment(${row.id})"`+ (+row.inUse?' class="red"':``);
            let info=+row.inUse?`<br>(${this.getGermanDate(row.from)} - ${this.getGermanDate(row.to)})`:``;
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

        this.input.addEventListener("change",event=> {
            this.inputId.value="";
            this.inputDisplay.innerText="";            
        })

        this.input.addEventListener("input",this.handleInputEvent);
        this.input.classList.add("listener");
        this.input.addEventListener("focus", this.handleFocusEvent);
        this.input.addEventListener("blur", this.handleFocusEvent);
    }


    handleInputEvent= () => {
        if (!this.listContainer.classList.contains("d-none")) {
            this.render();
        }
    }
    handleFocusEvent= (event) => {
        if (event.target.value == "") {
            this.inputDisplay.classList.add("d-none");
        } else {
            this.inputDisplay.classList.toggle("d-none",document.activeElement === this.input);
        }
    }

    showPrice() {
        this.inputDisplay.classList.remove("d-none");
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
            this.inputDisplay =this.input.closest(".input-container").querySelector('.right');
        }


        this.toggleWindow();
    }

    async toggleWindow() {
        if(this.listContainer.classList.contains("d-none")) { 
            await this.load();
            this.input.style.zIndex=2;
            this.addInputEvent();
            if (this.filteredList.length>5) this.input.focus(); // On demanmd

        } else {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";
        };
    }

    selectEquipment(id) {
        let equipment=this.data.find(e => e.id==id);

        this.input.blur();
        this.input.value=equipment.name;
        this.inputId.value=equipment.id;
        this.inputDisplay.innerText=equipment.price
        this.toggleWindow();
        // this.getPrice(equipment); //Neu -> equpmentPrice.getPrice(equipment)
        this.showPrice();
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
            <div class="right"></div>
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

        // this.input.focus();
        input.value="";
        inputId.value="";
        this.inputDisplay.innerText="";
        this.toggleWindow();
    } 

    async XgetPrice(equipment) {
        this.inputDisplay.innerText = await equipmentPrice.getPrice(equipment);
    }


}
