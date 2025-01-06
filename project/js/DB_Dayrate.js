import { Query } from "./Query.js";

export class  DB_Dayrate extends Query {
    

    constructor() {
        super();
        this.elements();
        this.addEventListener();
    }

    elements() {
        this.input = {
            price:  document.getElementsByName("dayrate-price")[0],
            name:   document.getElementsByName("dayrate-name")[0],
            text:   document.getElementsByName("dayrate-re_text")[0],
            vat:    document.getElementsByName("dayrate-vat")[0]
        }

        this.main = {
            price:    document.getElementsByName("price-name")[0],
            overlay:  document.getElementsByName("price-name")[0]
        }

    }

    setPriceElements(id) {
        if (!id) id=this.lastOpenId;
        if (!id) return;

        this.price = {
            vat:document.getElementsByName(`${id}-vat`)[0],
            text:document.getElementsByName(`${id}-text`)[0],
            price:document.getElementsByName(`${id}-price`)[0],
            container: document.getElementById(id).parentElement
        }
    }

    async insertQuery() {
        await this.request(`
            INSERT INTO bu_article
            SET 
                bu_article.companyId =  ${+login.companyId},
                bu_article.usage =      0,
                bu_article.price =      ${+this.input.price.value},
                bu_article.vat =        ${+this.input.vat.value},
                bu_article.name =      '${this.input.name.value}',
                bu_article.re_text =   '${this.input.text.value}'
        `); 
    }


    async insert() {
        this.insertQuery();
        await this.get();
        this.id=this.data.lastId;
    }

    addEventListener() {
        document.querySelector("#dayrate-edit .blocker").addEventListener("mousedown",(event) => {
            this.closeWindow();
            this.hideOverlay();
            this.main.price.value="";
            this.main.price.focus();
            console.log("db_dayrate Blocker");
            event.preventDefault();
            // event.stopPropagation();
            // document.getElementsByName("price-name")[0].focus();
            // document.getElementsByName("price-name")[0].style="border: 2px solid red";

        });

        // document.querySelector("#dayrate-edit .blocker").addEventListener("mousedown",(event) => {
            // this.uiClose(event.target.id);

        // })


    }

    hideOverlay() {
        document.getElementsByName("price-name")[1].classList.add("d-none");
        document.getElementsByName("price-name")[0].classList.remove("d-none");        
    }

    closeWindow(element) {
        if (!element) element=document.getElementById("dayrate-edit");
        element.classList.add("d-none");
    }

    openWindow(element) {
        if (!element) element=document.getElementById("dayrate-edit");
        if (+this.main.price.value == 0) return;
        element.classList.remove("d-none");
        if (!this.input.vat.value)  this.input.vat.value="19.00";
    }
    
    fillPrice() {
        this.input.price.value=this.main.price.value;
    }

    async save() {
        this.closeWindow();
        await this.insert();

        // if_projectNew.dataset.articleIddayrate=this.id;
        // if_projectNew.dataset.drName=this.input.name.value;
        // if_projectNew.dataset.drPrice=this.input.price.value;
        // if_projectNew.dataset.standard=0;
        
        this.main.price.value=this.input.name.value;

        if (!projectPrice.ap) {
            projectPrice.ap={data:[]};
        }
        projectPrice.ap.data.push({
            articleId: this.id,
            name: this.input.name.value,
            price: this.input.price.value
        })
        projectPrice.setPriceDayrate(this.input.price.value,this.id);

        
        // do some actions to update all data
    }

    clear() {
        Object.values(this.input).forEach(input => {
            input.value="";
        })
        this.main.price.value="";
        if_projectNew.clearDisplay("price-name");
        if_projectNew.hideDayPriceGroup();
        if (!if_projectNew.dataset) return;
        if_projectNew.dataset.articleIdDayrate=0;
        if_projectNew.dataset.drName="";
        if_projectNew.dataset.drPrice=0;
        if_projectNew.dataset.standard=0;

    }


    cancel() {
        this.closeWindow();
        this.clear();
    }



    async insertPriceElements() {
        await this.request(`
            INSERT INTO bu_article
            SET 
                bu_article.companyId =  ${+login.companyId},
                bu_article.usage =      0,
                bu_article.price =      ${+this.price.price.value},
                bu_article.vat =        ${+this.price.vat.value},
                bu_article.name =      '${this.price.text.value}',
                bu_article.re_text =   '${this.price.text.value}'
        `); 
        await this.get();
        this.setId();
        this.price.container.classList.add("d-none");
    }

    updateOvertime() {
        if_projectNew.dataset.otPrice=this.price.price.value;
        if_projectNew.dataset.articleIdOvertime=this.id;
        if_projectNew.dataset.otName=this.price.text.value;
    }

    updateOffday() {
        if_projectNew.dataset.offPrice=this.price.price.value;
        if_projectNew.dataset.articleIdOffday=this.id;
        if_projectNew.dataset.offName=this.price.text.value;
    }

    async uiOvertimeSave() {
        this.setPriceElements();
        await this.insertPriceElements();
        this.updateOvertime();
        this.uiClose();
        projectPrice.showOverlay();
    }

    async uiOffdaySave() {
        this.setPriceElements();
        await this.insertPriceElements();
        this.updateOffday();
        this.uiClose();
        projectPrice.showOverlay();
    }

    uiIsOpen() {
        if (!this.price) return false;
        return !(this.price.container.classList.contains("d-none"));
    }
    
    uiClose() {
        this.price.container.classList.add("d-none");
    }

    uiCancel() {
        this.setPriceElements();
        this.uiClose();
        projectPrice.clearOverlay();
        projectPrice.hideOverlay();
        this.price.price.focus();
    }

    uiOpen(id) {
        console.log("db_dayrate.uiOpen")
        this.lastOpenId=id;
        this.setPriceElements();
        // let container=document.getElementById(id).parentElement; 
        this.price.container.classList.remove("d-none");
        if (!+this.price.vat.value) this.price.vat.value="19.00";
    }
}
