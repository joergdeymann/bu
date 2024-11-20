import { Calendar } from './Calendar.js';
import { ProjectCalendarRender } from './ProjectCalendarRender.js';
import { ProjectJobs } from './ProjectJobs.js';
import { ProjectWorker } from './ProjectWorker.js';

export class ProjectCalendar extends Calendar {
    position=0;
    positionText=["Termin Start","Termin Ende","Anfahrt","Abfahrt"];

    // setup = {
    //     calendar: {
    //         period: true,
    //         travel: true
    //     }
    // }



    newEntry= {
        start:'',
        end:'',
        arrival:'',
        departure:'',
        color: '#FFA500',
        id:0,  
        jobName:"",
        ultimateColor:'#FFA500'   
    }

    render;
    jobs;
    undoList=[];



     /**
      * Set the Date we are now
      * @param dateString - A date that can ghet converted to date 
      */
    constructor(dateString="") {
        super(dateString);
        this.timeline=new ProjectWorker();
        this.jobs=new ProjectJobs();    
        this.render=new ProjectCalendarRender(this);


    };

    setMonth(month) {
        this.date.setMonth(this.date.getMonth()+month);
        // this.position;
        this.renderCalendarAll();
    }

    ifDisableHeadline(forcechange) {
        return (!opt.mobileCalendar.allowEvent && !opt.mobileCalendar.allowTravel);       
    }

    changeCalendarInformation(day) {
        //if (this.newEntry.start == '' && this.newEntry.end != '') this.newEntry.start = this.newEntry.end;
        // if (this.newEntry.start != '' && this.newEntry.end == '') this.newEntry.end = this.newEntry.start;
        if (day == "") {
            if (this.position == 0 && this.newEntry.end != '') this.newEntry.start = this.newEntry.end;
            if (this.position == 1 && this.newEntry.start != '') this.newEntry.end = this.newEntry.start;
            day=this.newEntry.start;

        }

        if (this.position == 0)  {
            
            if (day>this.newEntry.end) {
                this.newEntry.end=day;
            }

            this.newEntry.start=day;
            // if (this.newEntry.end == '') this.newEntry.end=day;
            // if (this.newEntry.end == '') this.newEntry.end=day;
        }
        if (this.position == 1)  {
            if (day<this.newEntry.start) {
                this.newEntry.start=day;
            }
            this.newEntry.end=day;
        }
        if (this.position == 2)  this.newEntry.arrival=day;
        if (this.position == 3)  {
            this.newEntry.departure=day;

            if (this.newEntry.arrival != '' ) {
                if (this.newEntry.arrival > this.newEntry.departure ) [this.newEntry.arrival,this.newEntry.departure ] = [this.newEntry.departure,this.newEntry.arrival];
            }
        }
        
    }

   
    calendarPosition() {     
        if (this.position == 1) {
            if (this.newEntry.start > this.newEntry.end ) [this.newEntry.start,this.newEntry.end ] = [this.newEntry.end,this.newEntry.start];
        }
        if  (!opt.mobileCalendar.allowEvent) {
            if (this.position == 0 || this.position==1) this.position=2;
        } else 
        if  (!opt.mobileCalendar.allowTravel) {
            if (this.position == 2 || this.position==3) this.position=0;
        }
    }

    setCalendarInformation(day) {
        if (!opt.mobileCalendar.allowEvent && !opt.mobileCalendar.allowTravel) {
            return;
       }
       this.changeCalendarInformation(day);
       this.position = ++this.position%4;   
       this.calendarPosition(); 
       this.renderCalendarAll();
   }


    // async getRequest() {
    //     let php=new PHP('./php/calendar_read.php');
    //     let parameters = {
    //         searchDate: this.date.toISOString()
    //     }
    //     this.entries=await php.get(parameters);   
    // }
 
    separateDateString(date) {
        if (date=="") return "";
        const d=new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0'); // Monate sind 0-basiert
        const day = String(d.getDate()).padStart(2, '0');
        
        return `${year}-${month}-${day}`;
    }

    separateDate(date) {
        return new Date(separateDateString(date));
    }

    getLevelCheck(level,entry) {
        // Check if there is an enty
        let from=new Date(entry.start);
        let to=new Date(entry.end);

        let calendarStart=new Date(this.date.setDate(1));
        let calendarEnd=new Date(this.date.getFullYear(),this.date.getMonth()+1,0);
        // if (from < calendarStart || to > calendarEnd)  return true; // Event liegt außerhalb des Kalenders
        if (this.outVisibleCalendar(entry)) return true;
        if (from > calendarEnd && to > calendarEnd) {
            console.log("Beidedrüber;");
            return true;
        }
        if (from < calendarStart && to < calendarStart) {
            console.log("Beidedrüber;");
            return true;
        }
        
        let day=from.getDate();
        for (let date=from;date<=to;date.setDate(date.getDate()+1)) {
            if (date.getDate() < day) break;
            day=date.getDate();
            if (level[day] != null) return false;
        }
        if (level[(new Date(entry.arrival)).getDate()] != null) return false;
        if (level[(new Date(entry.departure)).getDate()] != null) return false;
        return true;
    }

    setLevel(level,entry) {
        let from=new Date(entry.start);
        let to=new Date(entry.end);
        let day=from.getDate();
        for (let date=from;date<=to;date.setDate(date.getDate()+1)) {
            if (date.getDate() < day) break; 
            day=date.getDate();
            level[day] = 1;
        }
        level[(new Date(entry.arrival)).getDate()] = 1;
        level[(new Date(entry.departure)).getDate()] = 1;
    }

    outVisibleCalendar(entry) {
        let calendarStart=this.date.setDate(1);
        let calendarEnd=new Date(this.date.getFullYear(),this.date.getMonth()+1,0);
        return (entry.from < calendarStart && entry.to < calendarStart) || 
            (entry.to > calendarEnd && entry.from > calendarEnd); // Event liegt außerhalb des Kalenders
    }

    getLevel() {
        let level=new Array(32);
        let levels=[];
        let index=0;
        levels.push(level);
        for (let entry of this.entries) { 
            // if (this.outVisibleCalendar(entry)) continue;

            index=0;
            while  (!this.getLevelCheck(levels[index],entry)) {
                if (levels[++index] == null) {
                    levels.push(new Array(32));
                }
            }
            this.setLevel(levels[index],entry);
            entry.level=index;
        }
        return levels.length;
    }

    async display() {
        this.entries=await this.timeline.get();
        this.renderCalendar();
        this.render.addCalendarSetupListener();
    }

    async renderCalendarAll() {
        this.timeline.load(this.date);
        this.entries=await this.timeline.get();
        this.display();

    }

    // renderCalendarDays() {
    //     return this.render.renderCalendarDays();
    // }

    renderCalendar(undo=true) {
        if (undo) this.undoList.push({...this.newEntry,position:this.position});
        document.getElementById("calendar").innerHTML     = this.render.renderCalendar();
        document.getElementsByName("from")[0].value       =this.newEntry.start.substring(0,10);
        document.getElementsByName("to")[0].value         =this.newEntry.end.substring(0,10);
        document.getElementsByName("arival")[0].value     =this.newEntry.arrival.substring(0,10);
        document.getElementsByName("departure")[0].value  =this.newEntry.departure.substring(0,10);
        this.addEvents();
    }

    addEvents() {
        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.addEventListener("focus", () => {
                if (typeof input.showPicker === "function") {
                    try {
                        input.showPicker(); // Dies wird funktionieren, wenn "focus" direkt durch Benutzerinteraktion ausgelöst wird
                    } catch (exception) {

                    }
                }
            });
        });
    }
     
    async renderJobHeadline() {
        document.getElementById("jobs").innerHTML=`<h2></h2>` + await this.jobs.renderJobHeadlines();
    }

    async getJobs(id) {
        document.getElementById("jobs").innerHTML=`<h2>${this.newEntry.jobName}</h2>`+await this.jobs.renderJobs(id);
    }
    
    async chooseJob(id) {
        let job=this.jobs.getJob(id);
        this.newEntry.color=job.color;
        this.newEntry.id=id;
        this.newEntry.jobName=job.name;
        document.getElementById("jobs").innerHTML=`<h2>${this.newEntry.jobName}</h2>` + await this.jobs.renderJobHeadlines();
        this.renderCalendarAll();
    }

    reset() {
        this.newEntry = {
            ...this.newEntry,  // Die restlichen Attribute behalten
            start: '',
            end: '',
            arrival: '',
            departure: ''
        };        
        this.position=0;
        this.renderCalendarAll();    
    }

    next() {
        this.position = ++this.position%4;
        this.calendarPosition();
        this.renderCalendarAll();
    }

    undo() {
        if (this.undoList.length<=1) return;
        this.undoList.pop();
        this.newEntry={...this.undoList[this.undoList.length-1]}; 
        
        // this.position = (this.position + 3) % 4;
        this.position = this.newEntry.position;

        this.calendarPosition();
        this.renderCalendar(false);
        this.render.addCalendarSetupListener();
        document.getElementById("jobs").querySelector("h2").innerText=this.newEntry.jobName;
    }

    updateFromInputs(event,position) {
        this.position=position;
        let value=event.target.value==""?"":event.target.value + " 00:00:00";
        
        if (value !== "") this.date=new Date(value);
        this.changeCalendarInformation(`${value}`);
        this.calendarPosition();
        this.renderCalendar(false);
        this.render.addCalendarSetupListener();
    }

}




