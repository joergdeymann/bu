import { Request } from './Request.js';

export class UnterkunftList {
    
    
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
    setElements() {
        this.list=document.getElementById("unterkunft-list");
        this.listContainer=this.list.parentElement;
        this.input=document.getElementsByName("unterkunftName")[0];
        this.inputId=document.getElementsByName("unterkunftId")[0];
    }

    filterList() {
        let [name, ort] = this.input.value.toLowerCase().split(",").map(item => item.trim());
        let all = this.input.value.toLowerCase();
        return this.data.filter(e => 
            (e.name && e.name.toLowerCase().includes(name)) || 
            (e.ort && e.ort.toLowerCase().includes(ort)) ||
            (e.name && e.name.toLowerCase().includes(all)) || 
            (e.ort && e.ort.toLowerCase().includes(all))
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

    async load() { 
        let firma=login.companyId; 

        let p=new Request(`SELECT recnum,name,ort FROM bu_adresse WHERE zuordnung = 10 and firmanr=${firma};`);         
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
        let html="<h1>Wo willst du schlafen?</h1>";
        html+=/*html*/`<div class="selector-headline" onclick="unterkunftList.clearField()">Zurücksetzten</div>`

        for(let row of this.filterList()) {
            // Version 1 = nicht auswählbar: let click=+row.inuse?'class="red"':`onclick="unterkunftList.selectunterkunft(${row.recnum})"`;
            
            let click=`onclick="unterkunftList.selectUnterkunft(${row.recnum})"`;
            html+=/*html*/`<div ${click}>${row.name},${row.ort}</div>`;
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

        } else {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";
        };
        this.input.focus(); 
    }

    selectUnterkunft(id) {
        let data=this.data.find(e => e.recnum==id);
        this.input.value=`${data.name},${data.ort}`;
        this.inputId.value=data.recnum;
        this.toggleWindow();
    }
    clearField() {
        this.input.value="";
        this.inputId.value="";
        this.toggleWindow();
    } 

}
