/* eslint-env browser */
/* global setup */
import {ColorAdjust} from "./ColorAdjust.js"; // Wird nicht von innen genutzt
import {ExtDate} from "./ExtDate.js"; // Wird nicht von innen genutzt
import { Setup } from './Setup.js';



export class ProjectCalendarRender extends ExtDate {
    calendar;
    // jobs;

    constructor(calendar) {
        super();
        // console.log("Hallo");
        // console.log(calendar.setup);
        this.calendar=calendar;

        // this.jobs=new ProjectJobs();
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
                    // calendar.renderCalendarAll();
                    document.querySelector(".calendar-setup.d-none")?.classList.remove("d-none");                
                }

                if (set.name == "mainColors") {
                    opt.mobileCalendar.mainColors=event.target.checked;
                    this.mainColorsSet=false;

                    // this.renderCalendarDays(); 
                    calendar.display();
                    // this.calendar.renderCalendarAll();
                    document.querySelector(".calendar-setup.d-none")?.classList.remove("d-none");                
                }

            })
        }) 

    
    }

    addDateEvents() {
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

    

    renderPlate(plateSize,cl,entry) {
        let display="";

        if (opt.mobileCalendar.plateName) display=entry.projectName;
        if (opt.mobileCalendar.plateCity) display=entry.city;

        if (!(entry.projectName + entry.city)) { // if (!display) {
            cl += " new";
            display="Neu";
        }
        return `<div style="width:calc(${plateSize} + 2px); " class="calendar-text ${cl}"><div >${display}</div></div>`;
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
    

    async XsetMainColors() {
        // if (this.mainColorsSet) return;
        this.mainColorsSet=true;
        await job.await(); // Warte auf die Daten von `job`

    
        for(let entry of calendar.entries) {
            entry.color= this.determineColor(entry) ?? entry.color;
        }


        // calendar.entries = calendar.entries.map(entry => ({
        //     ...entry,
        //     color: this.determineColor(entry) ?? entry.color,
        // }));
    }


    /**
     * Bestimmt die korrekte Farbe basierend auf dem Eintrag und Optionen.
     * @param {Object} entry - Der Kalendereintrag
     * @returns {string | null} - Die neue Farbe oder null
     */
    XdetermineColor(entry) {
        if (entry.start === '' || entry.end === '') {
            return null; // Kein Start oder Enddatum, keine Farbänderung
        }

        const jobEntry = job.getById(entry.id); // das ist arbeiter ID ??####?? das geht hier so nicht !!! die id aus entry ist eine andere 
        if (!jobEntry) {
            return null; // Kein zugehöriger Job gefunden
        }

        // Farbentscheidung basierend auf Optionen
        return opt.mobileCalendar.mainColors
        ? jobEntry.ultimateColor
        : jobEntry.color;
    }


    // async setMainColors() {
    //     await job.await();
    //     for(let entry of calendar.entries) {
    //         if (entry.start!='' && entry.end !='') {
    //             let uc=entry.color;
    //             if (entry.id != null && entry.id != 0) {
    //                 if (opt.mobileCalendar.mainColors) {
    //                     uc=job.getById(entry.id)?.ultimateColor;
    //                 } else {
    //                     uc=job.getById(entry.id)?.color;
    //                 }
    //             }
    //             entry.color=uc ?? entry.color; // opt.mobileCalendar.mainColors?uc:entry.color;
    //         }
    //     }
    // }

    updateCalendar(undo=true) {
        console.log("updateCalendar ");
        console.trace();
        if (undo) calendar.undoList.push({...calendar.newEntry,position:calendar.position});
        document.getElementById("calendar").innerHTML     = this.renderCalendar();

        document.getElementsByName("from")[0].value       =calendar.newEntry.start.substring(0,10);
        document.getElementsByName("to")[0].value         =calendar.newEntry.end.substring(0,10);
        document.getElementsByName("arival")[0].value     =calendar.newEntry.arrival.substring(0,10);
        document.getElementsByName("departure")[0].value  =calendar.newEntry.departure.substring(0,10);
        // this.addDateEvents();
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
                    <header>Einstellungen</header>
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



    renderCalendarDays() {
        this.calendar.date.setDate(1); 
        let date=new Date(this.calendar.date);
        let month=date.getMonth();
        let html="";

        // if (opt.mobileCalendar.mainColors == true) this.setMainColors();
        // this.setMainColors();
        // calendar.entries = await calendar.timeline.get(); //## ?? 

        let colorAdjust=new ColorAdjust(calendar.entries);
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
                    // let color=entry.displayColor;
                    // let color= opt.mobileCalendar.mainColors?entry.rootColor:entry.color ?? entry.color;
                    let color = entry.modifiedColor ?? entry.color;
                    // helligkeiten testen und 50 lumen drauf packen
                     
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


}
