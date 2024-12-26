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
            dayrateStandart: document.getElementsByName("dayrateStandart")[0],
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
    /**
     * INTERFACE - Schnittstelle von der Datenbank zur Eingabe
     * IF_ProjectNew
     */
    XfillForm() {
        let data=null;
        // 1. Dataset exist already by Loading an existing project 
        let jobId=this.var.jobDefinitionId;

        if (projectWorker.customerPriceId.value != "") {
            this.currentId=projectWorker.customerPriceId.value;
            data=[
                getById[this.currentId]
            ];
        } else

        // Try to load a dataset from 2 Values customer and Job
        if (this.input.customerId.value && jobId) {
            data=this.getFormCustomerAndJob();
            if (data.length == 0) data = this.filterJob(jobId);
            if (data.length == 0) data = this.filterJob(this.input.customerId.value);
        } else 
        if (jobId.value) {
            data=this.filterJob(jobId);
        } else
        if (this.input.customerId.value) {
            data=this.filterJob(this.input.customerId.value);
        }

        if (data && data.length>0) {
            fillFormData(data[0]);
            this.currentId=data[0].id;
        }

    }

    fillFormData(data) {
        this.input.dayPrice.value=data.dayPrice;
        // this.input.dayPriceText.value=data.dayPriceText;
        // hide the text on focus out 
        // make the text width new data visible "Lichttechniker: "
        // ans instead only € show the price,too;
        // an own Listener is a good idea 

        // this.input.customerPriceId.value=data.id;
    }

    fillListData() {
        // For the Popdown List
    }


    // If we change Dayrate Find new Id in CustomerPrice
    // articleIdDayrate 
    // id : if exists
    // 0 : if we want to create
    fillCustomerPrice() {
        this.findNewCustomerPrice(projectWorker.customerPriceId.value);
    };

    // ### What is the PriceId wi have choosen
    XfillSelectedPrice() {
        this.findNewCustomerPrice(projectWorker.customerPriceId.value);
    };

    // Wie soll es anezeigt werden ?
    // 1. Wenn Kunde angegeben 
    //      dann soll der Spezifische Kundenpeis angezegt werden falls er gespreichert ist
    //      sonst zeige den Stadart Preis mit Kunde 0 an
    //      falls der Standartpreis nicht verfügbar erstelle eine neue Zugriffsdatei
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
        if (this.dataset?.articleIdDayrate == articleIdDayrate) return this.dataset.drPrice;
        console.log("Artikelpreis hat sich geändert");

        // Try to find Article in the grouped CustomerPrice 
        // 1. DayrateId must be equal
        // 2. customerId must be 0 or selected customerId

        let data=this.data.filter(e => e.articleIdDayrate == articleIdDayrate && (e.customerId == 0 || e.customerId == +this.input.customerId.value));
        
        if (data.length == 0) {
            console.log(`Artikel nicht in der Datenbank gefunden, DayrateId=${articleIdDayrate}`);
            console.log(`Suche den Artikel aus JobDefinition`);
            //ArticleId is not yet in the Database we have to isert it
            // Try to find articleId over ProjectJobDefinition-> Article
            
            // We will have a new Entry so we ave to fill Data width the standart of JobDefinition ID
            // If we dont find it, there has to Popup, "We didnt find the standart Value of JobDefinitioId"
            // 
            // Values

            // ich ziehe hier nur den Namen des JobDefinitionIds raus und die Id zum speichern

            let entry=job.data.find(e=> e.id = job.newEntry.jobId);
            if (entry.length==0) {
                // Artikel does not exist or is 0
                // the shud not really Happen
                // !!! TODO: show Input that connects the JobDefinitionId width the ArticleId
                // let the Input field clear
            }
            let invoice_text = calendar.entries[job.newEntry.jobId].invoiceText;


            let customerId=0;
            customerId=this.input.customerId.value; //customerId nur auf Wunsch setzen User Imput
            
            const index = this.data.findIndex(item => item.id === 0);
            if (index !== -1) {
                this.data.splice(index, 1);
            }
            
            let dayrateId = articleIdDayrate??entry[0].articleId;

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
                drPrice: drPrice, // Das muss auch aus Article Kommen // get Customerprice if customerId > 0 else standart

                articleIdOffday: 0, // Neu erzeugen oder Auswahl in den Artiekeln
                offName: "",        // Eingabe
                offPrice: 0,        // Eingabe
                
                articleIdOvertime:0, // Neu erzeugenn oder Auswahl in den Artikeln
                otName: "",        // Eingabe
                otPrice: 0,        // Eingabe
                
                jobDefinitionId: job.newEntry.jobId,
                cpName:job.newEntry.name,          // Name für diese zusammenhängende Daten  
                standart: 0,
                dayrateCustomer: 0
            });
            this.currentId=0;
            if (customerId > 0) this.data[0].standart=1;

        } else 
        if (data.length == 1) {
            // Hier haben wir den aktuellen Satz der PriceId in data[0]
            this.currentId=data[0].id;
        } else {
            d= data.find(e => e.standart==1);

            // Can we seperate it  if there are more than one article width different Values ?
            // Mabe , make it to standart 
            this.currentId=d?.id??data[0].id;
        }
        project.setDayrateStandart(this.input.dayrateStandart,this.dataset.standart);
        project.setDayrateCustomer(this.input.dayrateCustomer,this.dataset.dayrateCustomer);
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



}
