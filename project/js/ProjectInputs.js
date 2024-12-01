export class ProjectInputs {
    constructor() {

    }

    toggleYesNo(event,status) {
        let elements=event.target.parentElement.querySelectorAll("button"); // 1= Yes 2=No
        elements[0].classList.toggle("bg-green-gradient",status);
        elements[1].classList.toggle("bg-red-gradient",!status);
    }

    setHotelNecessary(event,status) {
        this.toggleYesNo(event,status);

        document.querySelector(".input-container.hotel-given").classList.toggle("d-none",!status); 
        if (!status) {
            document.querySelector(".input-container.hotel-name").classList.toggle("d-none",!status); 
        } else {
            let s=document.querySelector(".input-container.hotel-given").querySelector("button").classList.contains("bg-green-gradient"); 
            document.querySelector(".input-container.hotel-name").classList.toggle("d-none",!s); 
        }
    }

 
    setHotelGiven(event,status) {
        this.toggleYesNo(event,status);
        document.querySelector(".input-container.hotel-name").classList.toggle("d-none",!status); 
    }

    setOvertime(event,status) {
        this.toggleYesNo(event,status);

    }

}