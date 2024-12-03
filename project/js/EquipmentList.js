import { DB_EquipmentPrice } from './DB_EquipmentPrice.js';
import { DB_Article } from './DB_Article.js';
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
        this.inputPrice=document.getElementsByName("equipmentPrice[]")[0];
        this.inputId=document.getElementsByName("equipmentId[]")[0];
        this.inputDisplay =this.input.closest(".input-container").querySelector('.right');
    }

    filterList() {
        return this.filteredList=this.data.filter(e=>e.name && e.name.toLowerCase().includes(this.input.value.toLowerCase()));  
    }

    getById(id) {
        return this.data.find(e=>e.id == id);  
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
            html+=/*html*/`<div ${click}>${row.name}, ${row.price} €${info}</div>`;
        }
        this.list.innerHTML=html;
    }

    showWindow() {
        this.listContainer.classList.remove("d-none");
    }

    isInputValid() {
        console.log("IsValid",!!this.input.value === !!this.inputId,this.inputId);
        return !!this.input.value === !!this.inputId;
    }

    abortWindow() {
        if (!this.inputId.value) this.input.value="";
        else {
            let element=this.getById(+this.inputId.value);
        
            if (this.input.value != element.name) {
                this.input.value="";
            }
        }
        this.closeWindow();
    }
              
    closeWindow() {
        this.listContainer.classList.add("d-none");
        this.input.style.zIndex="";

        if  (this.list.classList.contains("nolist")) {
            this.input.focus();
            if (!this.isInputValid()) this.input.value=""; // nur wenn ungültiger wert 
            this.list.classList.remove("nolist");
        } 
    }


    showPrice() {
        this.inputDisplay.classList.remove("d-none");
    }


    addEvents() {
        this.listContainer.querySelector(".blocker").addEventListener("mousedown",event => {
            this.closeWindow();

            event.preventDefault();
            event.stopPropagation();
        })
        this.list.addEventListener("mousedown",this.handlePropagation)
        this.addInputEvent();

    }

    addInputEvent() {
        this.input.addEventListener("change",event=> {
            this.inputId.value="";
            this.inputDisplay.innerText="";            
            this.inputPrice.value="";            
        })

        this.input.classList.add("listener");
        this.input.addEventListener("input",this.handleInputEvent);
        this.input.addEventListener("focus", this.handleFocusEvent);
        this.input.addEventListener("blur", this.handleBlurEvent);
    }

    handlePropagation = (event) => {
        if  (!this.list.classList.contains("nolist")) {
            event.preventDefault();
            event.stopPropagation();    
        }
    } 

    handleInputEvent= () => {
        if (!this.listContainer.classList.contains("d-none")) {
            this.render();
        }
    }
    
    handleEuroDisplay(event) {
        if (event.target.value == "") {
            this.inputDisplay.classList.add("d-none");
        } else {
            this.inputDisplay.classList.toggle("d-none",document.activeElement === this.input);
        }
    }

    hidePrice() {
        this.inputDisplay.classList.add("d-none");
    } 

    handleFocusEvent= (event) => {
        this.moveElements(event.target);
        this.hidePrice();
    }

    handleBlurEvent = (event) => {
        this.handleEuroDisplay(event);
        if (event.target.value != "" && !this.inputId.value) this.newArticleInterface();
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

    moveElements(element=null) {
        // console.log("moveElements",element);
        // if (element==null) {
        //     console.trace();
        //     debugger;
        // }


        if(this.listContainer.classList.contains("d-none")) { 
            if (element == null) {
                if (document.activeElement.closest(".input-container")) {
                    element=document.activeElement;
                } else {
                    element=document.querySelector('input[name="equipmentName[]"]');
                }    
            }

            element.closest(".input-container").insertAdjacentElement("afterend", document.getElementById("popup"));
            
            let parent=element.parentElement; // elternteil des Inputs also der container

            this.input=parent.querySelector('input[name="equipmentName[]"]');
            this.inputId =parent.querySelector('input[name="equipmentId[]"]');
            this.inputPrice =parent.querySelector('input[name="equipmentPrice[]"]');
            this.inputDisplay =parent.querySelector('.right');
        }
    }

    setWindow(event) {
        this.moveElements(event.target);
        event.preventDefault();    
        this.toggleWindow();
    }

    async toggleWindow() {
        if(this.listContainer.classList.contains("d-none")) { 
            await this.load();
            this.input.style.zIndex=3;
            if (this.filteredList.length>5) this.input.focus(); // On demanmd
        } else {
            this.closeWindow();
        };
    }

    selectEquipment(id) {
        let equipment=this.data.find(e => e.id==id);

        this.input.blur();
        this.input.value=equipment.name;
        this.inputId.value=equipment.id;
        this.inputPrice.value=equipment.price;
        this.inputDisplay.innerText=equipment.price+" €";
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
            <input type="hidden" name="timeEquipmentId[]"  value="${0}">
            <input type="hidden" name="equipmentId[]">
            <input type="hidden" name="equipmentPrice[]">
            <input type="text" name="equipmentName[]"  placeholder="Was bringst du mit">
            <button class="small" type="button" onmousedown="equipmentList.setWindow(event)">&#128315;</button>
            <div class="right"></div>
        `; 
        
        event.target.closest(".input-container").insertAdjacentElement("beforebegin", newContainer);
        this.moveElements(newContainer.firstElementChild); // ich brauche hier erstmal nur das input field
        this.addInputEvent() ;
        

    }

    removeInputField(event) {
        let list=document.querySelectorAll(".input-container.equipment");
        let len=list.length;
        if (len>0) {
            list[len-1].remove();
        }
    }

    clearField() {
        this.input.value="";
        this.inputId.value="";
        this.inputPrice.value="";
        this.inputDisplay.innerText="";
        this.toggleWindow();
    } 

    newArticleInterface() {
        let html=/*html*/`
        <h1>Neues Equipment?</h1>
        <input type="hidden" name="newArticleId"  value="${this.inputId.value}">
        
        <div class="input-container" >
            <input type="text" name="newArticleName" placeholder="Bezeichnung" value="${this.input.value}">
        </div>

        <div class="input-container" >
            <input type="number" min="0" name="newArticlePrice" placeholder="Tagessatz in €">
        </div>

        <div class="input-container" >
            <input type="text" name="newArticleText" placeholder="Rechnungstext" value="${this.inputText?.value??""}">
        </div>
        `;

        if (customerList.input.value) {
            html+=/*html*/`
            <input type="hidden" value="" name="newEquipmentPriceId">
            <div class="input-container" >
                <div>Nur für Kunde<br>${customerList.input.value}?</div>
                <div class="group">
                    <button class="flex w50p" type="button" id="newArticleCustomer" onmousedown="project.toggleYesNo(event,true)">Ja</button>
                    <button class="flex w50p bg-red-gradient" type="button" onmousedown="project.toggleYesNo(event,false)">Nein</button>    
                </div>
            </div>
            `;
        }

        html+=/*html*/`
        <div class="movement input-container">
            <button class="bg-green-gradient" type="button" onmousedown="equipmentList.saveArticle()">Anlegen</button>
            <button class="bg-red-gradient" type="button" onmousedown="equipmentList.abortWindow()">Abbruch!</button>
        </div>
        `;

        this.list.innerHTML=html;
        this.list.classList.add("nolist");
        this.showWindow();
        // list.removeEventListener("mousedown",this.handleInputEvent);
    }

    async saveArticle() {
        let article=new DB_Article();
        article.id  =document.getElementsByName("newArticleId")[0];
        article.text=document.getElementsByName("newArticleText")[0];
        article.name=document.getElementsByName("newArticleName")[0];
        article.price=document.getElementsByName("newArticlePrice")[0];

        if (!article.id.value) {
            await article.insert();    
        }
        this.inputId.value=article.id.value;
        this.input.value=article.name.value;
        this.inputPrice.value=article.price.value;
        this.inputDisplay.innerText=this.inputPrice.value+" €";
        
        if (this.articleForCustomer && db_customer.id.value) {
            let ep=new DB_EquipmentPrice();
            ep.id  =document.getElementsByName("newEquipmentPriceId")[0];
            ep.articleId=article.id;
            ep.customerId=db_customer.id;
            ep.price=article.price;
            await ep.insert();
        }
        // this.showPrice();
        this.closeWindow();
        await projectSave.msg("Neues Equipment erstellt");


    }

    changeArticle() {
        // new Button 
    }

    get articleForCustomer() {
        return document.getElementById("newArticleCustomer")?.classList.contains("bg-green-gradient")??false;
    }

}
