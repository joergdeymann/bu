/* eslint-env browser */
/* global setup */
import {ColorAdjust} from "./ColorAdjust.js"; // Wird nicht von innen genutzt


export class ProjectCalendarRender {
    calendar;
    // jobs;

    constructor(calendar) {
        // console.log("Hallo");
        // console.log(calendar.setup);
        this.calendar=calendar;
        // this.jobs=new ProjectJobs();
    }


    renderCalendar() {
        let travelChecked=opt.mobileCalendar.allowTravel?"checked":"";
        let periodChecked=opt.mobileCalendar.allowEvent?"checked":"";
        let plateNameChecked=opt.mobileCalendar.plateName?"checked":"";
        let plateCityChecked=opt.mobileCalendar.plateCity?"checked":"";
        let mainColorsChecked=opt.mobileCalendar.mainColors?"checked":"";

        
        let html=/*html*/ `
            <div class="headline">
                <h2 class="theme">${this.calendar.positionText[this.calendar.position]}</h2>
                <img class="gear">
                <div class="calendar-setup d-none">
                    <div class="screen"></div>
                    <header>Selektionen</header>
                    <nav>
                        <label><input name="period" type="checkbox" ${periodChecked}>Terminstart und ende</label>
                        <label><input name="travel" type="checkbox" ${travelChecked}>An- und Abfahrt</label>
                        <label><input name="plateName" type="checkbox" ${plateNameChecked}>Plakette als Name</label>
                        <label><input name="plateCity" type="checkbox" ${plateCityChecked}>Plakette als Ort</label>
                        <label><input name="mainColors" type="checkbox" ${mainColorsChecked}>nur Haupt Farben</label>
                    </nav>
                </div>
            </div>

            <div class="calendar">
                <nav>
                    <img src="../img/pfeil-links.png" onclick="calendar.setMonth(-1)">
                    <span onclick="calender(0)">${this.calendar.getDateMMMJJJJ()}</span>
                    <img src="../img/pfeil-rechts.png" onclick="calendar.setMonth(1)">
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
            

        `;
        // document.getElementById("calendar").innerHTML=html;
        return html;
        // return html;
    }

    addCalendarSetupListener() {
        /*
            Close Setup of Calendar
        */
        document.querySelector(".calendar-setup .screen").addEventListener("click",e => {  
            document.querySelector(".calendar-setup:not(.d-none)")?.classList.add("d-none");
        })
    
        /* 
            Prevent Clicks form Calendar Setup to other areas
        */
        document.querySelector(".calendar-setup")?.addEventListener("click",e => {
            e.stopPropagation(); 
        })
        
        /* 
            Enables Open Setup
        */
        document.querySelector(".calendar-container .headline img")?.addEventListener("click",e => {
            document.querySelector(".calendar-setup.d-none")?.classList.remove("d-none");   
            e.preventDefault(); 
            e.stopPropagation(); 
        })

        /*
            Change Parts in Setup
        */
        document.querySelectorAll('.calendar-setup input[type="checkbox"]').forEach(set => {
            set.addEventListener("change",event => {
                /* global setup */

                // this.calendar.opt.calendar[set.name]=event.target.checked;

                if (set.name=="travel") {
                    opt.mobileCalendar.allowTravel=event.target.checked;
                    if (!event.target.checked && (this.calendar.position == 2 || this.calendar.position ==3)) {
                        this.calendar.position=0;
                    }      
                }
                if (set.name=="period") {
                    opt.mobileCalendar.allowEvent=event.target.checked;
                    if (!event.target.checked && (this.calendar.position == 0 || this.calendar.position ==1)) {
                        this.calendar.position=2;
                    }      
                }
                set.closest(".headline").querySelector(".theme").innerHTML=this.calendar.positionText[this.calendar.position];

            
                if (set.name=="plateName") {
                    console.log(set.name);
                    let city=document.getElementsByName("plateCity")[0];
                    opt.mobileCalendar.plateName=event.target.checked;
                    if (opt.mobileCalendar.plateName) {
                        city.checked = false;
                        opt.mobileCalendar.plateCity=false;
                    }
                    calendar.display();
                    document.querySelector(".calendar-setup.d-none")?.classList.remove("d-none");
                }

                if (set.name=="plateCity") {
                    console.log(set.name);
                    let name=document.getElementsByName("plateName")[0];
                    opt.mobileCalendar.plateCity=event.target.checked;
                    if (opt.mobileCalendar.plateName) {
                        name.checked = false;
                        opt.mobileCalendar.plateName=false;
                    }
                    calendar.display();
                    document.querySelector(".calendar-setup.d-none")?.classList.remove("d-none");                
                }

                if (set.name == "mainColors") {
                    opt.mobileCalendar.mainColors=event.target.checked;
                    calendar.display();
                    document.querySelector(".calendar-setup.d-none")?.classList.remove("d-none");                
                }

            })
        }) 

    
    }

    /**
     * PRIVATE
     *  
     * Genertaes som empty Cells at the Beginning
     * 
     * @returns - html width Empty Cells
     */
    renderEmptyCellsStart() {
        const  weekday=this.calendar.getDayOfWeek();
        return Array.from({ length: weekday }, () => `<div><div class="empty"></div></div>`).join('');
    }
    

    /**
     * PRIVATE
     *  
     * Genertaes som empty Cells at the End
     * 
     * @returns - html width Empty Cells
     */
    renderEmptyCellsEnd(date) {
        //const remainingDays = (8 - date.getDay()) % 7; // Berechnet die verbleibenden Tage bis zum nächsten Montag
        let remainingDays = (8 - date.getDay()) % 7; // Berechnet die verbleibenden Tage bis zum nächsten Montag
        if (remainingDays == 0) return "";
        return Array.from({ length: --remainingDays }, () => `<div><div class="empty"></div></div>`).join('') + `<div class="floor-right"><div class="empty"></div></div>`;
        
    }       

    getStyles(dt,ds,de) {

    }

    daysUntil(date, endDate) {
        const today = new Date(date); // Gegebenes Datum
        const targetEndDate = new Date(endDate); // Enddatum
        if (today.getDay() == 0) return 0;

        // Nächster Sonntag berechnen
        const nextSunday = new Date(today);
        nextSunday.setDate(today.getDate() + (7 - today.getDay())); // Tag bis Sonntag hinzufügen
    
        // Differenzen berechnen
        const daysToSunday = Math.ceil((nextSunday - today) / (1000 * 60 * 60 * 24));
        const daysToEndDate = Math.ceil((targetEndDate - today) / (1000 * 60 * 60 * 24));
    
        // Rückgabe: Das frühere Ziel
        return Math.min(daysToSunday, daysToEndDate);
    }

    isSunday(date) {
        const today = new Date(date); // Gegebenes Datum
        return today.getDay() == 0;
    }
    isMonday(date) {
        const today = new Date(date); // Gegebenes Datum
        return today.getDay() == 1;
    }

    getSundayOfWeekAsDate(date) {
        const today = new Date(date); // Gegebenes Datum
        let dayOfWeek=today.getDay();
        if (dayOfWeek == 0) dayOfWeek=7;
        // let sundayDate=(new Date(today.getDate(today)-dayOfWeek+7)).toISOString().split("T")[0];
        today.setDate(today.getDate() - dayOfWeek +7);
        let sundayDate=today.toISOString().split("T")[0];
        return sundayDate;
    }

    compareDays(dateEnd,dateNow,days) {
        if (!dateEnd || !dateNow) return true;
        let de=new Date(dateEnd);
        let dt=new Date(dateNow);
        dt.setDate(dt.getDate()+days);
        return dt.toISOString().split("T")[0] >= de.toISOString().split("T")[0];
    }

    renderPlate(plateSize,cl,entry) {
        let display;

        display=entry.projectName;
        display=entry.city;
        if (!display) {
            cl += " new";
        }
        return `<div style="width:calc(${plateSize} + 2px); " class="calendar-text ${cl}"><div >${display ?? "Neu"}</div></div>`;
    }

    addPlate(dt,ds,de,entry,level,levels) {
        // dt = date
        // de end of event
        let add="";
        let cl="center"
        if (levels==2) {
            if (level==0 || levels==0) cl="top";
            else if (level==levels-1) cl="bottom";    
        }

        const plateSize = `${this.daysUntil(dt, de)*101+101}%`;

        if (dt == ds) {
            cl += " leftradius";
        }
        if (this.compareDays(de,dt,6) && this.getSundayOfWeekAsDate(dt)>=de) {
            cl += " rightradius";
        }

        if ((this.isMonday(dt) && (ds<=dt) && (dt <=de)) || dt == ds) {
            add=this.renderPlate(plateSize,cl,entry);
        };
        return add;
    }
    
    setMainColors() {
        for(let entry of calendar.entries) {
            if (entry.start!='' && entry.end !='') {
                let uc=entry.color;
                if (entry.id != null && entry.id != 0) {
                    if (opt.mobileCalendar.mainColors) {
                        uc=calendar.jobs.get(entry.id)?.ultimateColor;
                    } else {
                        uc=calendar.jobs.get(entry.id)?.color;
                    }
                }
                entry.color=uc; // opt.mobileCalendar.mainColors?uc:entry.color;
            }
        }
    }

    renderCalendarDays() {
        this.calendar.date.setDate(1); 
        let date=new Date(this.calendar.date);
        let month=date.getMonth();
        let html="";

        // if (opt.mobileCalendar.mainColors == true) this.setMainColors();
        this.setMainColors();

        let colorAdjust=new ColorAdjust(this.calendar.entries);
        colorAdjust.getColorList();

        this.calendar.entries.push(this.calendar.newEntry);
        
        let levels=this.calendar.getLevel();
        // let levels=3;
        let empty=`<div class="empty"></div>`;
    

        html += this.renderEmptyCellsStart();

        while(month == date.getMonth()){
            let dt=this.calendar.separateDateString(date);

            let style=""; // color
            let add="";   // Arrival or Depature
            let format="";
            let displayCount=0;
            let hideday="";

            let htmlLevel=Array(levels).fill(empty);

            for (let entry of this.calendar.entries) {
                let ds=this.calendar.separateDateString(entry.start);  
                let de=this.calendar.separateDateString(entry.end);
                let arrival   =this.calendar.separateDateString(entry.arrival);
                let departure =this.calendar.separateDateString(entry.departure);

                let display=false;
                let styles=[];
                add="";   // Arrival or Depature
                format="";
                let level=entry.level; // 1; //entry.leve+1;
                if (levels > 1) styles.push(`height: ${Math.floor(100/levels)}%`);
                styles.push(`top: ${Math.floor(100/levels*level)}% `);
                // styles.push(`top:calc(50% - 1px)`);
                

                add+=this.addPlate(dt,ds,de,entry,level,levels);

                if (dt == ds) {
                    format+=" start";
                } 
                if (dt == de) {
                    format+=" end";
                } 
                if (dt >= ds && dt <= de) {
                    let color=entry.color;
                    styles.push(`background:${color}`);
                    display=true;
                    
                    if (levels == 3 && level==1) {
                        hideday="trans25";
                    }

                }
                if (dt == arrival ) {
                    add+=`<span class="small bl">An</span>`;
                }
                if (dt == departure ) {
                    add+=`<span class="small br">Ab</span>`;
                }
                if ((dt == departure || dt == arrival) && format=="") {
                    format = "big";
                    display=true;
                }

                if (display) {

                    ++displayCount;
                    style="";
                    if (styles.length>0) style=`style="${styles.join(";")}"`;

                    htmlLevel[level]=`
                        <div class="${format.trim()}" ${style} >
                        ${add}
                        </div>
                    `;
                }
            }

            

            let floor="";
            // let d=new Date(date.getFullYear(),date.getMonth(),0).getDate(); // last day in Month
            
            if (date.getDate() == this.getDateLastMonday(date).getDate()) {
                floor=`class="floor-left"`;
            }
            if (date.getDate() == this.getDateLastMonday(date).getDate()+6) {
                floor=`class="floor-right"`;
            }
            
            // let dateString=date.getFullYear+"-"+date.getMonth()+"-"+date.getDate() ;
            // $dt = $date
            dt=this.utcDate(date);
  
            html+=/*html*/ `
            <div ${floor}>
                ${htmlLevel.join("")}
                <span class="${hideday}" onclick="calendar.setCalendarInformation('${dt}')">${date.getDate()}</span>
            </div>
            `;
    

            date.setDate(date.getDate() + 1);
        } 
              
        html += this.renderEmptyCellsEnd(date);

        this.calendar.entries.pop();

        return html;
    }

    getDateLastMonday(givenDate) {
        // Monat wird in JS mit 0-indexiert (0 = Januar, 11 = Dezember)
        let lastDay = new Date(givenDate.getFullYear(), givenDate.getMonth()+1, 0); // letzter Tag des Monats
        let dayOfWeek = lastDay.getDay(); // Wochentag des letzten Tages (0 = Sonntag, 1 = Montag, ..., 6 = Samstag)
    
        let offset = (dayOfWeek >= 1) ? dayOfWeek - 1 : 6;
    
        lastDay.setDate(lastDay.getDate() - offset);
        return lastDay;
    }
    
    utcDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Monate sind 0-basiert
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

}
