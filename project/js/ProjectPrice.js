import { Query } from './Query.js';

export class ProjectPrice {
    
    ap;
    cp;
    ep;

    constructor() {
        this.setElements();
        this.addEvents();
    }



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

    loadArticlePrice() {
        if (!calendar.newEntry.id ) return 0;

        this.ap=new Query(`
            SELECT a.price as price 
            FROM bu_article a 
            JOIN bu_job_definition jd
            ON jd.articleId = a.id
            WHERE a.companyId=${login.companyId} 
            AND jd.id=${calendar.newEntry.id};`
        );
    }

    loadEquipmentPrice() {
        if (!customerList.id) return 0;
        this.ep=new Query(`
            SELECT 
                a.name, 
                eq.price as price 
            FROM 
                bu_equipment_price eq 
            LEFT JOIN 
                bu_article a
            ON 
                a.id = eq.articleId
            WHERE 
                eq.companyId = ${login.companyId} 
            AND 
                eq.customerId = ${customerList.id} 
            ORDER BY 
                name;
        `);
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
     *  
     */
    setElements() {
        this.list=document.getElementById("price-list");
        this.listContainer=this.list.parentElement;
        this.input=document.getElementsByName("price-name")[0];
        this.inputDisplay =this.input.closest(".input-container").querySelector('.right');

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
        <div onclick="projectPrice.setPrice(${this.articlePrice})">
            <div>${job.newEntry.name||"Artikelpreis"}:</div>
            <div>${this.articlePrice} €</div>
        </div>
        <div onclick="projectPrice.setPrice(${this.customerPrice})">
            <div>Kundenbasis:</div>
            <div>${this.customerPrice} €</div>
        </div>
        `;
        if (this.ep?.data) {
            for (let ep of this.ep.data) {
                html +=/*html*/`
                <div onclick="projectPrice.setPrice(${ep.price})">
                    <div>${ep.name||"Equipmentpreis"}:</div>
                    <div>${ep.price} €</div>
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

        this.input.addEventListener("focus", this.handleFocusEvent);
        this.input.addEventListener("blur", this.handleFocusEvent);

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

    handleFocusEvent= (event) => {
        if (event.target.value == "") {
            this.inputDisplay.classList.add("d-none");
        } else {
            this.inputDisplay.classList.toggle("d-none",document.activeElement === this.input);
            if (document.activeElement !== this.input) {
                this.input.value = parseFloat("0"+this.input.value).toFixed(2) || "";   
            }
        }
    }

    async toggleWindow() {
        if(this.listContainer.classList.contains("d-none")) { 
            this.load();
            this.input.style.zIndex=2;
            this.input.focus(); 

        } else {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";
        };
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
        this.input.blur();
        this.toggleWindow();
    }
}
