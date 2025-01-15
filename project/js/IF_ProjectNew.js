import { CustomerList } from "./CustomerList.js";
import {DB_CustomerPrice} from "./DB_CustomerPrice.js";

export class IF_ProjectNew extends DB_CustomerPrice {
    constructor() {
        super();
        this.relations() 
        // this.elements();
        this.selectQuery();
    }

    elements() {

        this.input={
            dayrateStandard: document.getElementsByName("dayrateStandard")[0],
            dayrateCustomer: document.getElementsByName("dayrateCustomer")[0],

            // not in use after selected, change this in all Files
            // index.html -> ??
            // projectEdit -> ?? 
            // projectView -> ??
            customerId: document.getElementsByName("customerId")[0],
            dayPrice: document.getElementsByName("price-name")[0],
            // projectWorker.customerPriceId
        }
        // this.name=document.getElementsByName("customerName")[0];
        // this.id=document.getElementsByName("customerId")[0];
    }

    fillFromDatabase() {
        // if (this.input.jobDefinitionId.value == "")

    }


    relations() {
        this.var = {
            jobDefinitionId:job.newEntry.jobId           
        }
    }


    getFormCustomerAndJob() {
        return this.filterCustomerAndJob(this.input.customerId.value,this.var.jobDefinitionId);
    }

    fillFormData(data) {
        this.input.dayPrice.value=data.dayPrice;
    }


    fillCustomerPrice() {
        this.findNewCustomerPrice(projectWorker.customerPriceId.value);
    };

    isChangedJobDefinition() {
        if (!this.dataset.jobDefinitionId) return false;
        return (
            !this.dataset.jobDefinitionId
            || this.dataset.cpName !== document.querySelector("#jobs h2").innerHTML
        );
    }
    // Wie soll es anezeigt werden ?
    // 1. Wenn Kunde angegeben 
    //      dann soll der Spezifische Kundenpeis angezegt werden falls er gespreichert ist
    //      sonst zeige den Stadart Preis mit Kunde 0 an
    //      falls der Standardpreis nicht verfügbar erstelle eine neue Zugriffsdatei
    // 2. beim Speichern 
    //      muss ich mir merken welcher Wert gespeichert wird
    //      a) der allgemeine
    //      b) der Kundenspezifische
    //      beim Speichern speicehre ich ja die ArtikelId in der bu_timeWorker.articleIdDayrate
    //      deswegen ist auch beim Lden diese Id Massgeblich für den Preis und den Rechnungstext
    //      Natürlch werden Direkt eingegebene Werte überschrieben landen in der timeWorker tabelle
    //  3. Eingaben von Überstundensatz und Offday
    //      Diese könnnen über den Artikel addiert werden, aber auch verändert werden
    //      Wenn der Artikel über die Liste addiert wurde wird die Id hinzugefügt
    //      Wenn der Preis manuell geändert wurde bleibt die Id vorhanden aber 
    //      der neue Preis wird auch gespeichert
    //      Will man die Daten ändern: muss amn das irgendwo angeben
    findNewCustomerPrice(articleIdDayrate) {
        // Try to find Article in the grouped CustomerPrice 
        // 1. DayrateId must be equal
        // 2. customerId must be 0 or selected customerId
        let data=this.data.filter(e => e.articleIdDayrate == articleIdDayrate && (e.customerId == 0 || e.customerId == +this.input.customerId.value));
        
        if (data.length == 0 || data[0].id == 0) {
            // ArticleId is not yet in the Database we have to isert it
            // Try to find articleId over ProjectJobDefinition-> Article
            
            // We will have a new Entry so we ave to fill Data width the standard of JobDefinition ID
            // If we dont find it, there has to Popup, "We didnt find the standard Value of JobDefinitioId"
            
            let entry=job.data.find(e=> e.id == job.newEntry.jobId);
            let jobId=entry?entry.jobId:0;
            let jobName=entry?entry.name:"";
            let jobStandard=entry?entry.standard:0;

            // Try to find the JobDefinitionId from other Customers
            if (!jobId) {
                let j=this.findByDayrateId(articleIdDayrate);
                jobId=j?.jobDefinitionId??0;
                jobName=j?.cpName??"";
                jobStandard=j?.standard??0;
            }


            let customerId=0;
            customerId=this.input.customerId.value; //customerId nur auf Wunsch setzen User Imput
            
            const index = this.data.findIndex(item => item.id === 0);
            let jobChanged=true;
            if (index !== -1) {
                jobChanged = this.data.splice(index, 1)[0].jobDefinitionId != jobId;
            }
            
            let dayrateId = articleIdDayrate??entry?.articleId??0;

            let drName=projectPrice.getName(dayrateId);
            let drPrice=projectPrice.getPrice(dayrateId);
            
            // cp = CustomerPrice
            // ot = OverTime
            // off  = Offday
            
            this.data.push({
                id: 0, // Wird neu erzeugt

                customerId: customerId,
                companyId: login.companyId,

                articleIdDayrate: dayrateId, // entry falls ArtikelID = 0
                drName:  drName, // job.newEntry.name,     // Das muss aus Article kommen
                drPrice: drPrice, // Das muss auch aus Article Kommen // get Customerprice if customerId > 0 else standard

                articleIdOffday: 0, // Neu erzeugen oder Auswahl in den Artiekeln
                offName: "",        // Eingabe
                offPrice: 0,        // Eingabe
                
                articleIdOvertime:0, // Neu erzeugenn oder Auswahl in den Artikeln
                otName: "",        // Eingabe
                otPrice: 0,        // Eingabe
                
                jobDefinitionId: jobId, // job.newEntry.jobId,
                cpName:jobName,          // Name für diese zusammenhängende Daten  
                standard: jobStandard,
                dayrateCustomer: 0
            });
            this.currentId=0;
            if (!dayrateId || !this.dataset.id) this.clearDayPriceGroup(); 
            if (jobId && jobChanged) job.chooseAndDisplayJob(jobId);
            this.showDayPriceGroup();

        } else 
        if (data.length == 1) {
            this.currentId=data[0].id;
            this.dataset.dayrateCustomer=this.dataset.customerId>0;
            this.currentId?this.hideDayPriceGroup():this.showDayPriceGroup();
            this.fillDayPriceGroup();
            if (this.isChangedJobDefinition() ) job.chooseAndDisplayJob(this.dataset.jobDefinitionId);
        } else {
            d= data.find(e => e.standard==1);
            // Can we seperate it  if there are more than one article width different Values ?
            this.currentId=d?.id??data[0].id;
            this.dataset.dayrateCustomer=this.dataset.customerId>0;
            this.currentId?this.hideDayPriceGroup():this.showDayPriceGroup();
            this.fillDayPriceGroup();
            if (this.isChangedJobDefinition() ) job.chooseAndDisplayJob(this.dataset.jobDefinitionId);
        }
        project.setDayrateStandard(this.dataset.standard);
        project.setDayrateCustomer(this.dataset.dayrateCustomer);
        project.setDayrateAll(false);
        
        return this.dataset.drPrice;

    }

    saveValues(input,articleId=null) {
        if (!this.dataset) return; // If first time Focus out of Field: dayrate it comes here too early 
        let data=this.dataset;
        let keys={
            "price-name": {
                articleId: "articleIdDayrate",
                name:      "drName",
                price:     "drPrice"
            },
            "overtime-price": {
                articleId: "articleIdOvertime",
                name:      "otName",
                price:     "otPrice",
            },
            "offday-price": {
                articleId: "articleIdOffday",
                name:      "offName",
                price:     "offPrice"
            }
        }

        let key;

        if (articleId) { // if called from focus out we only need to set the Price
            key=keys[input.name].articleId;
            data[key]=articleId;

            key=keys[input.name].name;
            data[key]=projectPrice.getName(articleId);

        }

        key=keys[input.name].price;
        data[key]=input.value;
        input.parentElement.querySelector(".right").innerHTML = (+input.value).toFixed(2) + " €";
    }

    clearGroup(name) {
        for(let element of document.getElementsByName(name)) {
            element.value="";
        }
    }

    clearDisplay(name) {
        let p=document.getElementsByName(name)[0].parentElement;
        p.querySelector(`.right`).innerHTML="";
        p.querySelector(`.left`).value="";
        p.querySelector(`header`).innerHTML="";
    }

    clearDayPriceGroup() {
        this.clearGroup("overtime-price");
        this.clearGroup("offday-price");
        this.clearDisplay("overtime-price");
        this.clearDisplay("offday-price");
        project.setDayrateStandard(false);
        project.setDayrateCustomer(false);
    }

    getFillPrice(datasetValue) {
        return datasetValue?(+datasetValue).toFixed(2) + " €":"";      
    }

    fillDayPrice(elementName,datasetValue) {
        let element=document.getElementsByName(elementName);
        element[0].value=datasetValue;
        projectPrice.showOverlay(element[0]);
    }

    get isNewEntry() {
        return !(this.dataset?.id??false);
    }

    fillHeadlineH1() {
        let element=document.getElementById("dayrate-section").querySelector("h1");
        let savePrices=document.getElementById("dayrate-text").closest(".input-container");
        if (this.isNewEntry) {
            element.innerHTML="Neue Tagessatz Gruppe";
            savePrices.classList.add("d-none");
        } else {
            element.innerHTML="Ändere Tagessatz Gruppe";
            savePrices.classList.remove("d-none");
        }
    }

    fillDayPriceGroup() {
        this.fillDayPrice("offday-price",this.dataset.offPrice);
        this.fillDayPrice("overtime-price",this.dataset.otPrice);
        project.setDayrateStandard(this.dataset.standard);
        project.setDayrateCustomer(this.dataset.dayrateCustomer);
        this.fillHeadlineH1();
    }

    showDayPriceGroup() {
        document.getElementById("dayrate-section").classList.remove("d-none");
    }

    hideDayPriceGroup() {
        document.getElementById("dayrate-section").classList.add("d-none");
    }


    /**
     * 1. Artikel JobDefinition finden
     * 1.1 0 dann keine vorhanden und rückgabe leer
     * 1.2 1 dann nur einen vorhanden und Rückgabe
     * 1.3 Mehrerer -> Weiter testen
     * 2. Artkel für Kunde
     * 2.1 Kein gefunden dann
     * 2.1.1 Standart wert vorhanden bei den Artikeln dann rückgabe Standard 
     * 2.1.1 Rückgabe des ersten gefundenen Wertes
     * 2.2 Einen gefunden, Rückgabe des einen
     * 2.3 Mehrere gefunden -> Weiter testen
     * 3. Nach standard suchen und entweder den Standard wert oder den 1. Raussuchen
     * 
     * @returns best articleIdDayrate
     *  
     */
    findDayrateId() {
        let customerId=document.getElementsByName("customerId")[0].value;

        let data;
        if (job.isChoosen()) {
            data=this.data.filter(e => e.jobDefinitionId == job.newEntry.jobId);
        } else {
            data=this.data;
        }
        if (data.length==0) return null;
        if (data.length==1) return data[0].articleIdDayrate;

        let data2=data.filter(e => e.customerId == customerId);
        if (data2.length==1) return data2[0].articleIdDayrate;
        if (data2.length==0) {
            data2=data.find(e => e.standard>0);
            return data2?data2.articleIdDayrate:data[0].articleIdDayrate;
        }
        let data3=data2.find(e => e.standard>0);
        return  data3?data3.articleIdDayrate:data2[0].articleIdDayrate;
    }

    async showDayrate() {
        await projectPrice.load();
        const dayrateId=this.findDayrateId();
        if (dayrateId) {
            job.displayJobByArticleId(dayrateId);
            this.findNewCustomerPrice(dayrateId);
        }

        this.fillDayPrice("price-name",this.dataset.drPrice);
    }

    prepareNew() {
        let price=document.getElementsByName("price-name")[0].value;
        this.remove(0);

        this.currentId=0;
        this.data.push({
            id: 0, // Wird neu erzeugt
    
            customerId: customerList.id,
            companyId: login.companyId,
    
            articleIdDayrate: 0, // entry falls ArtikelID = 0
            drName:  "", // job.newEntry.name,     // Das muss aus Article kommen
            drPrice: price, // Das muss auch aus Article Kommen // get Customerprice if customerId > 0 else standard
    
            articleIdOffday: 0, // Neu erzeugen oder Auswahl in den Artiekeln
            offName: "",        // Eingabe
            offPrice: 0,        // Eingabe
            
            articleIdOvertime:0, // Neu erzeugenn oder Auswahl in den Artikeln
            otName: "",        // Eingabe
            otPrice: 0,        // Eingabe
            
            jobDefinitionId: job.newEntry.jobId, // job.newEntry.jobId,
            cpName:job.newEntry.jobId,          // Name für diese zusammenhängende Daten  
            standard: 0,
            dayrateCustomer: 0
        });
    
    }

    clearOvertime() {
        this.dataset.articleIdOvertime=0;
        this.dataset.otName = "";
        this.dataset.otPrice = 0;        
    }

    clearOffday() {
        this.dataset.articleIdOffday=0;
        this.dataset.offName = "";
        this.dataset.offPrice = 0;        
    }
}
