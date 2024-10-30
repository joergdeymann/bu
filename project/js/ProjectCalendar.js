import { Calendar } from './Calendar.js';
import { PHP } from './PHP.js';

export class ProjectCalendar extends Calendar {
    /*
    start, end, arrival, departure, detailinformation
    start, end, arrival, departure
    start, end, arrival, departure
    start, end, arrival, departure
    */
    #entries=[
        {
            start:'2024-10-03',
            end:'2024-10-05',
            arrival:'2024-10-03',
            departure:'2024-10-06',
            color: '#FFFF00'            
        },
        {
            start:'2024-10-10',
            end:'2024-10-20',
            arrival:'2024-10-09',
            departure:'2024-10-21',
            color: '#CCFF00'            
        }
    ];  // Entries of this Month

     /**
      * Set the Date we are now
      * @param dateString - A date that can ghet converted to date 
      */
    constructor(dateString="") {
        super(dateString);
    };


    async getRequest() {
        let php=new PHP('./php/calendar_read.php');
        let parameters = {
            searchDate: this.date.toISOString()
        }
        this.#entries=await php.get(parameters);   

    }
 
    separateDateString(date) {
        return date.toISOString().slice(0,10);
    }

    separateDate(date) {
        return new Date(date.toISOString().slice(0,10));
    }

    renderCalendarDays() {
        let date=this.separateDate(this.date);
        let weekday=this.getDayOfWeek();
        let format="";
        let month=date.getMonth();
        let html="";

        let i=1;
        while(i<weekday) {
            html+=`<div><div class="empty"></div></div>`;
            i++;
        } 

        while(month == date.getMonth()){
            for (let entry of this.#entries) {
                let ds=entry.start.slice(0,10);  
                let de=entry.end.slice(0,10);;
                let arrival   =entry.arrival.slice(0,10);
                let departure =entry.departure.slice(0,10);

                let style=""; // color
                let add="";   // Arrival or Depature
                let format="";
                
                if (date == ds) {
                    format+=" start";
                } 
                if (date == de) {
                    format+=" end";
                } 
                if (date >= ds && date <= de) {
                    style=`style="color:${entry.color}"`;
                }
                if (date == arrival ) {
                    add+=`<span class="small bl">An</span>`;
                }
                if (date == departure ) {
                    add+=`<span class="small br">Ab</span>`;
                }
                if ((date == departure || date == arrival) && format=="") {
                    format = "big";
                }
                //  noch die Ecken
                if (format+style != "") {
                    break; 
                }
            }
            html+=/*html*/ `
                <div>
                    <div class="${format}">${date.getDate()}</div>
                    ${add}
                </div>
            `;
            date.setDate(date.getDate() + 1);
        } 

        while(date.getDay() != 6) {
            html+=`<div><div class="empty"></div></div>`;
        } 
        return html;
    }

    renderCalendar() {
        let html=/*html*/ `
            <h2 id="theme">Wann ?</h2>
            <div class="calendar">
                <nav>
                    <img src="../img/pfeil-links.png" onclick="calendar(-1)">
                    <span id="month" onclick="calender(0)">${this.getDateMMMJJJJ()}</span>
                    <img src="../img/pfeil-rechts.png" onclick="calendar(1)">
                </nav>
                <header>
                    <div>Mo</div>
                    <div>Di</div>
                    <div>Mi</div>
                    <div>Do</div>
                    <div>Fr</div>
                    <div>Sa</div>
                    <div>So</div>
                </header>
                <section>${this.renderCalendarDays()}</section>
            </div>
        `
        return html;
    }
}



