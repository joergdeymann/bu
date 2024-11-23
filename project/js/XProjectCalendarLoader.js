import { ProjectCalendar } from "./ProjectCalendar";

export class CalendarLoader extends ProjectCalendar {
     /**
      * Set the Date we are now
      * @param dateString - A date that can ghet converted to date 
      */
     constructor(dateString="") {
        super(dateString);
        // this.calendar = new ProjectCalendar()
        this.job = new ProjectJob();
        this.job.renderHeadLines();
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


