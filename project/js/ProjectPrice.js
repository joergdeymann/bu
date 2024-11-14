import { Query } from './Query.js';

export class ProjectPrice {
    
    ap;
    cp;

    constructor() {
        this.setElements();
        this.addEvents();
    }

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
    /**
     * 
     * --- !!! MUST CHANGES !!! --
     * 
     * Change the right Side customerXX ind your ids given in HTML
     *  
     */
    setElements() {
        this.list=document.getElementById("price-list");
        this.listContainer=this.list.parentElement;
        this.input=document.getElementsByName("price-name")[0];
    }

    // get filterList() {
    //     return this.data.filter(e=>e.firma.toLowerCase().includes(this.input.value.toLowerCase())); 
    // }

    loadCustomerPrice() {
        if (!customerList.id) return 0;
        this.cp=new Query(`
            SELECT tagessatz as price
            FROM bu_kunden 
            WHERE recnum=${customerList.id}
        ;`);
        
    }

    /*
        calendar.newEntry.jobId = bu_job.id
        projectjobs.data[] = 
    */
    loadArticlePrice() {
        if (!calendar.newEntry.jobId ) return 0;

        this.ap=new Query(`
            SELECT a.netto as price 
            FROM bu_artikel a 
            JOIN bu_job j
            ON j.articleId = a.recnum
            WHERE a.auftraggeber=${login.companyId} 
            AND j.id=${calendar.newEntry.jobId};`
        );
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
        this.customerPrice=await this.getCustomerPrice();
        this.articlePrice=await this.getArticlePrice();            

        // Only the first Time ? 0 is allowed:
        if (this.input.value == '') {
            this.input.value =this.getProjectPrice(this.customerPrice,this.articlePrice);
        }
        this.render();
        this.listContainer.classList.remove("d-none");
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
        let html="<h1>Tagessatz</h1>";
        html+=/*html*/`
        <div class="selector-headline" onclick="projectPrice.clearField()">Zurücksetzten</div>
        <div onclick="projectPrice.setPrice(${this.customerPrice})">
            <div>Kundenpreis:</div>
            <div>${this.customerPrice} €</div>
        </div>
        <div onclick="projectPrice.setPrice(${this.articlePrice})">
            <div>Artikelpreis:</div>
            <div>${this.articlePrice} €</div>
        </div>
        `;
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
    }

    async toggleWindow() {
        if(this.listContainer.classList.contains("d-none")) { 
            await this.load();
            this.input.style.zIndex=2;

        } else {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";
        };
        this.input.focus(); 
    }

    XselectCustomer(id) {
        let customer=this.data.find(e => e.recnum==id);
        this.input.value=customer.firma;
        this.toggleWindow();
    }

    clearField() {
        this.input.value="";
        this.toggleWindow();
    } 
    setPrice(price) {
        this.input.value=price;
        this.toggleWindow();
    }
}
