export class SelectionList {
    
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


    get filterList() {
        return this.filteredList=this.data.filter(e=>e[this.DBfield1].toLowerCase().includes(this.input.value.toLowerCase())); 
    }

    render() {
        let html=`<h1>${this.headline}</h1>`;
        html+=/*html*/`
        <div class="list-button-group">
            <div class="selector-headline" onclick="${this.classname}.clearField()">Zurücksetzen</div>
            <div class="selector-headline" onclick="${this.classname}.addCustomer()">${this.new}</div>
        </div>`;
        for(let row of this.filterList) {
            let field2 = this.DBfield2?", "+row[this.DBfield2]:"";
            html+=/*html*/`<div onclick="${this.classname}.select(${row[this.DBid]})">${row[this.DBfield1]}${field2}</div>`;
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
            this.input.style.zIndex=3;
            if (this.filteredList.length>5) this.input.focus(); 
        } else {
            this.listContainer.classList.add("d-none");
            this.input.style.zIndex="";
        };
    }

    select(id) {
        let data=this.data.find(e => e[this.DBid]==id);
        this.input.blur();
        this.input.value=data[this.DBfield1];
        this.inputId.value=data[this.DBid];
        if (this.input2) {
            this.input2.value=this.DBfield2?data[this.DBfield2]??"":"";
        }
        this.toggleWindow();
    }

    clearField() {
        this.input.focus();
        this.input.value="";
        if(this.input2) this.input2.value="";        
        this.inputId.value="";
        this.toggleWindow();
    } 
}
