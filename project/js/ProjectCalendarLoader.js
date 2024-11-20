import { ProjectCalendar } from "./ProjectCalendar";

export class CalendarLoader extends Calendar {
     /**
      * Set the Date we are now
      * @param dateString - A date that can ghet converted to date 
      */
     constructor(dateString="") {
        super(dateString);
        // this.calendar = new ProjectCalendar()
        this.projectWorkerClass = new this.projectWorkerClass();


    };


    loadCalendar(dateString="") {
        super(dateString);
    }

    async loadWorker() {
        await projectWorker.get();
        this.prepareWorkerButtons();
        this.outputWorkerButtons();
    }
}

class ProjectWorkerLoader {

}