export class ProjectOvertime {
    constructor() {
        this.setElements();
        this.addEvents();
    }
    setElements() {
        this.input=document.getElementsByName("overtime-price")[0];
        this.inputDisplay =this.input.closest(".input-container").querySelector('.right');
        this.overtime=document.getElementsByName("overtime");
    }


    addEvents() {
        this.input.addEventListener("focus", this.handleFocusEvent);
        this.input.addEventListener("blur", this.handleFocusEvent);


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
        let target;
        if (event instanceof Event) target=event.target;
        if (event instanceof Element) target=event;
        if (target.value == "") {
            this.inputDisplay.classList.add("d-none");
        } else {
            this.inputDisplay.classList.toggle("d-none",document.activeElement === this.input);
            if (document.activeElement !== this.input) {
                this.input.value = parseFloat("0"+this.input.value).toFixed(2) || "";   
            }
        }
    }

    setPrice() {
        // this.toggleYesNo(event,status);
        let oPrice=document.getElementsByName("overtime-price")[0];
        let status=oPrice.value != 0 
        //if (status) db_workPrice.load();
        oPrice.parentElement.classList.toggle("d-none",!status);
        this.handleFocusEvent(oPrice);
    }

    get isOvertime() {
        return this.overtime[0].classList.contains("bg-green-gradient");
    }

    // toggleDisplay(target,display) {
    //     if (target.value == "") {
    //         this.inputDisplay.classList.add("d-none");
    //     } else {
    //         this.inputDisplay.classList.toggle("d-none",document.activeElement === this.input);
    //         if (document.activeElement !== this.input) {
    //             this.input.value = parseFloat("0"+this.input.value).toFixed(2) || "";   
    //         }
    //     }
    // }

    // clearField() {
    //     this.input.value="";
    //     this.articleId=null;
    //     this.toggleWindow();
    // } 

    // setPrice(price,articleId) {
    //     this.articleId=articleId;
    //     this.input.value=price;
    //     this.input.blur();
    //     this.toggleWindow();
    // }
}
