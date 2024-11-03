export class ProjectCalendarRender {
    calendar;
    constructor(calendar) {
        console.log("Hallo");
        console.log(calendar.setup);
        this.calendar=calendar;

    }


    renderCalendar() {
        let travelChecked=this.calendar.setup.calendar.travel?"checked":"";
        let periodChecked=this.calendar.setup.calendar.period?"checked":"";
        
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
                <section>${this.calendar.renderCalendarDays()}</section>
            </div>
        `
        return html;
    }

    addCaledarSetupListener() {
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
                this.calendar.setup.calendar[set.name]=event.target.checked;
                if (set.name=="travel") {
                    if (!event.target.checked && (this.calendar.position == 2 || this.calendar.position ==3)) {
                        this.calendar.position=0;
                    }      
                }
                if (set.name=="period") {
                    if (!event.target.checked && (this.calendar.position == 0 || this.calendar.position ==1)) {
                        this.calendar.position=2;
                    }      
                }
                set.closest(".headline").querySelector(".theme").innerHTML=this.calendar.positionText[this.calendar.position];
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
        const weekday=this.calendar.getDayOfWeek();
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
        const remainingDays = (7 - date.getDay()) % 7; // Berechnet die verbleibenden Tage bis zum nächsten Montag
        return Array.from({ length: remainingDays }, () => `<div><div class="empty"></div></div>`).join('');
    }       

    getStyles(dt,ds,de) {

    }



    renderCalendarDays() {
        this.calendar.date.setDate(1); 
        let date=new Date(this.calendar.date);
        let month=date.getMonth();
        let html="";

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
                
                if (dt == ds) {
                    format+=" start";
                } 
                if (dt == de) {
                    format+=" end";
                } 
                if (dt >= ds && dt <= de) {
                    styles.push(`background-color:${entry.color}`);
                    display=true;
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

  
            html+=/*html*/ `
            <div>
                ${htmlLevel.join("")}
                <span onclick="calendar.setCalendarInformation(event,'${dt}')">${date.getDate()}</span>
            </div>
            `;
    

            date.setDate(date.getDate() + 1);
        } 
              
        html += this.renderEmptyCellsEnd(date);

        this.calendar.entries.pop();

        return html;
    }


}