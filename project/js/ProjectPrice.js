import { Query } from './Query.js';

export class ProjectPrice {
    
    ap;
    cp;
    ep;
    header="Tagessatz";
    changedPrice=false;


    constructor() {
        this.setElements();
        this.addEvents();

        this.setElements("overtime");
        this.addEvents();

        this.setElements("offday");
        this.addEvents();        
    }


    // Das muss weg , der Preis bezieht sioch auf nichts 
    loadCustomerPrice() {
        if (!customerList.id) return 0;
        this.cp=new Query(`
            SELECT price as price
            FROM bu_customer 
            WHERE 
                id=${customerList.id}
            AND
                companyId = ${login.companyId} 
        ;`);
        
    }

    // Bezogen auf Job Definition ID das wäre eigentlich nur der Wert der in JobDefinitionID eingetragen ist
    loadArticlePrice() {
        if (!calendar.newEntry.jobId ) return 0;

        this.ap=new Query(`
            SELECT 
                a.name,
                a.price as price,
                a.id    as articleId 
            FROM 
                bu_article a 
            JOIN 
                bu_job_definition jd
            ON 
                jd.articleId = a.id
            WHERE 
                a.companyId=${login.companyId} 
            AND 
                jd.id=${calendar.newEntry.jobId};`
        );
    }


    // BU_EQUIPMENT_PRICE = Liste aller equipments die einem Kunden zugeordnet sind
    // articleId / customerId / price

    loadEquipmentPrice() {
        if (!customerList.id) return 0;
        this.ep=new Query(`
            SELECT
                a.id as articleId, 
                a.name, 
                a.price
            FROM 
                bu_article a
            WHERE 
                a.companyId = ${login.companyId} 
            AND 
                a.usage = 0               
            ORDER BY 
                name;
        `);
    }
    // loadEquipmentPrice() {
    //     if (!customerList.id) return 0;
    //     this.ep=new Query(`
    //         SELECT
    //             a.name, 
    //             eq.price as price,
    //             a.id as articleId 
    //         FROM 
    //             bu_equipment_price eq 
    //         LEFT JOIN 
    //             bu_article a
    //         ON 
    //             a.id = eq.articleId
    //         WHERE 
    //             eq.companyId = ${login.companyId} 
    //         AND 
    //             eq.customerId = ${customerList.id} 
    //         ORDER BY 
    //             name;
    //     `);
    // }






    async getCustomerPrice()  {

        if (this.cp ==  null) return 0;
        await this.cp.get();
        return this.cp.data[0]?.price || 0;
    }

    async getArticlePrice() {
        if (this.ap ==  null) return 0;
        await this.ap.get();
        return this.ap.data[0]?.price || 0;
        // await this.ap;
        // return this.ap.data[0].price && 0;
    }

    async getEquipmentPrice() {
        if (this.ep ==  null) return 0;
        await this.ep.get();
        return this.ep.data[0]?.price || 0;
        // await this.ap;
        // return this.ap.data[0].price && 0;
    }
    /**
     * 
     * --- !!! MUST CHANGES !!! --
     * 
     * Change the right Side customerXX ind your ids given in HTML
     * Area = class="input-container"
     *  
     */
    setElementsPopup() {


    }


    setElements(area=null) {
        let id="price-list";
        let name="price-name";
    // if (!area && document.activeElement) {
        //     area=document.activeElement.closest(".input-container");
        // }

        if (!area) area="price-list";
        if (typeof area === "string") {
            switch (area) {
                case "price-list":
                    id="price-list";
                    name="price-name";
                    break;
                case "overtime":
                case "overtime-list":
                    id="overtime-list";
                    name="overtime-price";
                    break;
                case "offday":
                case "offday-list":
                    id="offday-list";
                    name="offday-price";
                    break;
                default:
                    console.error("Falsche id angegeben: ${area}");
                    console.trace();

            } 

            this.inputContainer=document.getElementsByName(name)[0].closest(".input-container");
            this.listContainer=document.getElementById(id).parentElement;

            this.input=this.inputContainer.querySelector("input:not(.left)");
    
        }


        if (area instanceof Element) {
            if (!area.classList.contains("input-containter")) area=area.closest(".input-container");            
            this.input=area.querySelector("input:not(.left)");
            this.inputContainer=area;
            
            if (this.input.name !== "price-list") {
                id=this.input.name.split("-")[0]+"-list";
            }

            this.listContainer=document.getElementById(id).parentElement;
        }

        this.list=document.getElementById(id);
        this.left=this.inputContainer.querySelector(".left");
        this.right=this.inputContainer.querySelector(".right");
        this.inputDisplay=this.right;
        this.header=this.input.placeholder; // {offday:"Offday Preis",overtime:"Überstundensatz"}[area] || "Tagessatz";
        this.description=this.inputContainer.querySelector("header");
        
    }


    /**
     * 
     * --- !!! MUST CHANGES !!! --
     * 
     * 1. firma -> get it from SESSION variabble
     * 2. Change the whole SELECT Request 
     *  
     */
    getProjectPrice(customerPrice,articlePrice) {
        // if (customerPrice>0) return customerPrice; // Perhaps own Table with Article-Customer-Price
        if (articlePrice>0) return articlePrice;
        if (customerPrice>0) return customerPrice; // Meanwhile this order
        return '';
    }


    async load() {
        this.loadArticlePrice();
        this.loadCustomerPrice();
        this.loadEquipmentPrice();
        this.customerPrice=await this.getCustomerPrice();
        this.articlePrice=await this.getArticlePrice();            
        this.equipmentPrice=await this.getEquipmentPrice();            

        // Only the first Time ? 0 is allowed:
        if (this.input.value == '') {
            this.input.value =this.getProjectPrice(this.customerPrice,this.articlePrice);
        }
        this.render();
        // ##### this.listContainer.classList.remove("d-none");
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
        let articleId=projectPrice.ap?.data[0]?.articleId??0;
        let articlePriceText=articleId?`${(+this.articlePrice).toFixed(2)} €`:"Job nicht asgewählt";
        let customerPriceText=customerList.inputId.value?`${(+this.customerPrice).toFixed(2)} €`:"Kunde nicht ausgewählt";

        let html=/*html*/`
        <h1>${this.header}</h1>
        <div class="list-button-group">
            <div class="selector-headline" onclick="projectPrice.uiClearField()">Zurücksetzten</div>
            <div class="selector-headline" onclick="projectPrice.showPriceGroup()">ändern</div>
        </div>          
        <div onclick="projectPrice.setPrice(${this.articlePrice},${articleId})">
            <div>${job.newEntry.name||"Artikelpreis"}:</div>
            <div>${articlePriceText}</div>
        </div>
        <!-- div onclick="projectPrice.setPrice(${this.customerPrice},0)">
            <div>Kundenbasis:</div>
            <div>${customerPriceText}</div>
        </div -->
        `;
        if (this.ep?.data) {
            for (let ep of this.ep.data) {
                html +=/*html*/`
                <div onclick="projectPrice.setPrice(${ep.price},${ep.articleId})">
                    <div>${ep.name||"Equipmentpreis"}:</div>
                    <div>${(+ep.price).toFixed(2)} €</div>
                </div>
                `;
                
            }    
        }
        this.list.innerHTML=html;
    }

    addEvents() {
        this.listContainer.querySelector(".blocker").addEventListener("mousedown",event => {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";
            event.preventDefault();
            event.stopPropagation();
        });

        this.list.addEventListener("mousedown",event=> {
            event.preventDefault();
            event.stopPropagation();
        });

        this.input.addEventListener("focus", this.handleFocusEventPre);
        this.input.addEventListener("blur", this.handleBlurEvent);
        this.input.parentElement.querySelector(".left")?.addEventListener("focus", this.handleFocusEventPre);


        this.input.addEventListener("input",event=> {
            if (!this.listContainer.classList.contains("d-none")) {
                this.render();
            }           
        });


        this.input.addEventListener('keydown', function(event) {
            const char = event.key;

            const controlKeys = ['Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Tab', 'Enter', 'Escape', 'Clear'];
            if (controlKeys.includes(char)) {
                return;
            }
            if (!/[0-9.]/.test(char) || (char === '.' && event.target.value.includes('.'))) {
                event.preventDefault();
            }
        });


        // document.getElementsByName("customerName")[0].addEventListener('input',this.handleCustomerEvent)
    }


    // handleCustomerEvent = (event) => {
    //     document.getElementById("customer-name").innerHTML=event.target.value;
    // }

    handleFocusEventPre= (event) => {
        let area= {"overtime-price":"overtime","offday-price":"offday"}[event.target.name] || null;
        this.setElements(area);
        this.hideOverlay();

        this.handleFocusEvent(event);
        this.input.focus();
    }

    handleFocusEvent= (event) => {
        let activeElement=document.activeElement;
        let left=this.input.parentElement.querySelector(".left"); 
        if (document.activeElement === left) activeElement = this.input;

        if (activeElement.value == "" || !+event.target.value) {
            this.inputDisplay.classList.add("d-none");
        } else {
            this.inputDisplay.classList.toggle("d-none",activeElement === this.input);

            if (activeElement !== this.input) {
                this.input.value = parseFloat("0"+this.input.value).toFixed(2) || "";
                this.showOverlay();
            }
        }
    }

    handleBlurEvent= (event) => {
        this.handleFocusEvent(event);
        if (if_projectNew.dataset && +if_projectNew.dataset.drPrice != +this.input.value) this.changedPrice = true;

        if (if_projectNew.dataset?.id) if_projectNew.saveValues(this.input);
        // if_projectNew.saveValues(this.input);

        if (event.target == document.getElementsByName("price-name")[0]) {

            let value=(if_projectNew.dataset?.id??false) ||  this.changedPrice;

            if (!if_projectNew.dataset?.articleIdDayrate && (+this.input.value != 0) ) {
                db_dayrate.fillPrice();
                db_dayrate.openWindow();
            }

            document.getElementById("dayrate-section").classList.toggle("d-none",!value);
        }
        // document.getElementById("dayrate-section").classList.remove("d-none");
    }

    closeWindow() {
        this.listContainer.classList.add("d-none");
        this.input.style.zIndex="";
    }

    async openWindow() {
        await this.load();
        this.input.style.zIndex=3;
        this.input.focus(); 
        // this.showPriceGroup();
        this.listContainer.classList.remove("d-none");
    }

    // this is the new one 
    // the set Elemenst of the other must be set before calling
    // or change the Name: toggleWindowfromElementName 
    async toggleWin() {
        if(this.listContainer.classList.contains("d-none")) { 
            await this.openWindow();
        } else {
            this.closeWindow();
        };
    }

    async toggleWindowfromElementName(area=null) {
        this.setElements(area);
        await this.toggleWin();
    }

    // Find out where it is calledFrom with parameters
    // This is DEPRICATED and should be elemated as soon as possible
    // Lets do it in one Day and search for all
    async toggleWindow(area=null) {
        this.setElements(area);
        await this.toggleWin();
    }

    getElementSetter(element) {
        if (!element) return null; 
        return {"overtime-price":"overtime","offday-price":"offday","price-name":null}[element.name]??null;
    }

    setActiveElements() {
        let name=this.getElementSetter(document.activeElement.parentElement.querySelector("input"));
        this.setElements(name);
    }

    getSelectedInputField() {
        return document.activeElement.closest(".input-container").querySelector("input");
    }

    setSelectedElements() {
        this.setElements(this.getSelectedInputField());
    }

    get isDayrate() {
        return this.input.name == "price-name";
    }

    get isOvertime() {
        return this.input.name == "overtime-price";
    }

    get isOffday() {
        return this.input.name == "offday-price";
    }
    

    uiClearField() {
        if (this.isDayrate) this.setElements();
        this.clearSelectedField();
    }


    clearSelectedField() {
        this.input.value="";
        this.left.value="";
        this.right.innerHTML="";
        this.description.innerHTML="";

        this.toggleWin();
        if (this.isOvertime) if_projectNew.clearOvertime();
        if (this.isOffday)   if_projectNew.clearOffday();

        if (this.isDayrate) {
            this.articleId=null;
            this.clearDayrate();
            if_projectNew.prepareNew(); // NUr das Wichtigste behalten
            
        }
    } 

    clearField() {
        this.setActiveElements();
        this.clearSelectedField();
    } 
    
    setDayrateText() {
        let text=if_projectNew.dataset?.drName  // cpName not always loded, it is the same normaly
        ||document.getElementsByName("price-name")[0].parentElement.querySelector("header").innerHTML
        ||"Tagessatz";

       
        document.getElementById("dayrate-text").innerHTML=text;
        document.querySelector("#dayrate-section H3").innerHTML=text;
    }

    setPriceDayrate(price,articleId) {
        this.setElements();
        this.articleId=articleId;
        this.input.value=price;
        this.input.blur();
        this.closeWindow();
        if_projectNew.findNewCustomerPrice(articleId);
        if_projectNew.saveValues(this.input,articleId); // ArtikelId and name must be saved here
        this.showOverlay(this.input);
    }

    setPrice(price,articleId) {
        this.setActiveElements();
        this.articleId=articleId;
        this.input.value=price;
        this.input.blur();
        this.toggleWindow(this.getElementSetter(this.input));

        if (this.input.name == "price-name") {
            if_projectNew.findNewCustomerPrice(articleId);
        }
        if_projectNew.saveValues(this.input,articleId); // ArtikelId and name must be saved here
        // this.setHeadline(this.input);
        this.showOverlay(this.input);
        

    }

    getName(articleId) {

        return this.ep?.data?.find(e => e.articleId == articleId)?.name
        ??this.ap?.data?.find(e => e.articleId == articleId)?.name
        ??"";

        
        ; // Hat aber nur die Artikel des Kunden
    }

    getPrice(articleId) {
        return this.ep?.data?.find(e => e.articleId == articleId)?.price
        ??this.ap?.data?.find(e => e.articleId == articleId)?.price
        ??"";

        // if (articleId == null) return this.getProjectPrice(this.customerPrice,this.articlePrice);
        // return this.ep.data.find(e => e.articleId == articleId).price; // Hat aber nur die Artikel des Kunden
    }

    showOverlay(input=this.input) {
        let p=input.parentElement;
        let left  = p.querySelector(".left");
        let right = p.querySelector(".right");
        if (!left) return;

        input.classList.add("d-none");
        left.classList.remove("d-none");
        right.classList.remove("d-none");
        right.innerHTML=input.value?input.value+" €":"";
        left.value=+input.value?input.placeholder:"";
        this.setHeadline(input);
    }

    hideOverlay(input=this.input) {
        let p=input.parentElement;
        let left  = p.querySelector(".left");
        let right = p.querySelector(".right");
        if (!left) return;
        input.classList.remove("d-none");
        left.classList.add("d-none");
        right.classList.add("d-none");
    }

    
    clearOverlay(input=this.input) {
        let p=input.parentElement;
        let left = p.querySelector(".left")
        let right= p.querySelector(".right");
        let header=p.querySelector("header");

        if (left) left.value = "";
        if (right) right.innerHTML = "";
        if (header) header.innerHTML = "";
        if (input) input.value="";

    }

    hidePriceGroup() {
        if_projectNew.hideDayPriceGroup();
    }

    showPriceGroup() {
        if_projectNew.showDayPriceGroup();
        this.toggleWindow(); // #### 
    }

    setHeadline(input) {
        let name={
            "overtime-price":"otName",
            "offday-price":"offName",
            "price-name":"drName"
        }[input.name];
        let header=input.parentElement.querySelector("header");
        if (name && header) {
            header.innerHTML=if_projectNew.dataset?.[name]??"";
        }
        this.setDayrateText();

    }

    clearDayrate() {
        let p=document.getElementsByName("price-name")[0];
        this.clearOverlay(p);
        this.hidePriceGroup();
    }

}
