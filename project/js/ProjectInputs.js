export class ProjectInputs {
    constructor() {

    }

    isYes(element) {
        return element.classList.contains("bg-green-gradient");        
    }

    toggleYesNo(event,status) {
        let target;
        if (event instanceof Event) {
            target=event.target;
        } else if (event instanceof Element) {
            target=event;
        } else {
            return;
        }

        let elements=target.parentElement.querySelectorAll("button"); // 1= Yes 2=No
        elements[0].classList.toggle("bg-green-gradient",status);
        elements[1].classList.toggle("bg-red-gradient",!status);
    }

    setHotelNecessary(event,status) {
        this.toggleYesNo(event,status);

        document.querySelector(".input-container.hotel-given").classList.toggle("d-none",!status); 
        if (!status) {
            document.querySelector(".input-container.hotel-name").classList.toggle("d-none",!status); 
        } else {
            // let s=document.querySelector(".input-container.hotel-given").querySelector("button").classList.contains("bg-green-gradient"); 
            let s=document.querySelector(".input-container.hotel-given button").classList.contains("bg-green-gradient"); 
            document.querySelector(".input-container.hotel-name").classList.toggle("d-none",!s); 
        }
    }

 
    setHotelGiven(event,status) {
        this.toggleYesNo(event,status);
        document.querySelector(".input-container.hotel-name").classList.toggle("d-none",!status); 
    }

    setOvertime(event,status) {
        this.toggleYesNo(event,status);
        // let oPrice=document.getElementsByName("overtime-price")[0];
        // if (status) db_workPrice.load();
        // oPrice.parentElement.classList.toggle("d-none",!status);
    }

    setDayrateStandard(event,status=null) {
        if (status==null) {
            status=event;
            event=document.getElementsByName("dayrateStandard")[0];
        }
        this.toggleYesNo(event,status);
        if_projectNew.dataset.standard=status?1:0;

    }

    setDayrateCustomer(event,status=null) {
        if (status==null) {
            status=event;
            event=document.getElementsByName("dayrateCustomer")[0];
        }
        this.toggleYesNo(event,status);
        if_projectNew.dataset.dayrateCustomer=status?1:0;
    }


    setDayrateAll(status=false) {
        let element=document.getElementsByName("dayrateAll")[0];
        this.toggleYesNo(element,status);
    }

    isDayrateAll() {
        return this.isYes(document.getElementsByName("dayrateAll")[0]);
    }

    isStandard() {
        return this.isYes(document.getElementsByName("dayrateStandard")[0]);
    }

    isOnlyCustomer() {
        return this.isYes(document.getElementsByName("dayrateCustomer")[0]);
    }
    
    isDayrateVisible() {
        return !document.getElementsByName("dayrateAll")[0].closest(".input-container").classList.contains("d-none");
    }

}