import { Query } from './Query.js';

export class CustomerList {
    
    filteredList=[];

    constructor() {
        this.setElements();
        this.addEvents();
    }

    /**
     * 
     * --- !!! MUST CHANGES !!! --
     * 
     * Change the right Side customerXX ind your ids given in HTML
     *  
     */
    get id() {
        return this.inputId.value==""?0:+this.inputId.value;
    }

    setElements() {
        this.list=document.getElementById("customer-list");
        this.listContainer=this.list.parentElement;
        this.input=document.getElementsByName("customerName")[0];
        this.inputId=document.getElementsByName("customerId")[0];
    }

    get filterList() {
        return this.filteredList=this.data.filter(e=>e.firma.toLowerCase().includes(this.input.value.toLowerCase())); 
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

        let p=new Query(`SELECT recnum,firma FROM bu_kunden where auftraggeber=${login.companyId} ORDER BY firma;`);
        this.data=await p.get();
        
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
        let html="<h1>Kundenliste</h1>";
        html+=/*html*/`
        <div class="list-button-group">
            <div class="selector-headline" onclick="customerList.clearField()">Zurücksetzten</div>
            <div class="selector-headline" onclick="customerList.addCustomer()">Neuer Kunde</div>
        </div>`;
        for(let row of this.filterList) {
            html+=/*html*/`<div onclick="customerList.selectCustomer(${row.recnum})">${row.firma}</div>`;
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

        this.input.addEventListener("change",event=> {
            this.inputId.value="";
        })

        this.input.addEventListener("input",event=> {
            if (!this.listContainer.classList.contains("d-none")) {
                this.render();
            }
        })
    }

    toggleWindow() {
        if(this.listContainer.classList.contains("d-none")) { 
            this.load();
            this.input.style.zIndex=2;
            if (this.filteredList.length>5) this.input.focus(); 
        } else {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";
        };
    }

    selectCustomer(id) {
        let customer=this.data.find(e => e.recnum==id);
        this.input.blur();
        this.input.value=customer.firma;
        this.inputId.value=customer.recnum;
        
        
        this.toggleWindow();
    }

    clearField() {
        this.input.focus();
        this.input.value="";
        this.inputId.value="";
        this.toggleWindow();
    } 
}
