export class ExtDate {
    date=null;
    #lang="DE";
    
    constructor(dateString="") {
        this.setDate(dateString);
    }
    
    getDate() {
        return this.date;
    }

    getDateString(lang=null) {
        let returndate=this.date.toLocaleString('de-DE', { timeZone: 'Europe/Berlin' });
        if (lang == null) return date=this.date.toString();
        return returndate;
    }

    getDateMMMJJJJ() {
        // month["DE"]["Januar"]
        let month={
            "DE":[
                "Januar",
                "Februar",
                "März",
                "April",
                "Mai",
                "Juni",
                "Juli",
                "August",
                "September",
                "Oktober",
                "November",
                "Dezember"
            ]
        };
        return `${month[this.#lang][this.date.getMonth()]} ${this.date.getFullYear(4)}`;                                                            
    }

    addMonth(c=1) {
        this.date.setMonth(this.date.getMonth() + c);
    }
    
    subMonth(c=1) {
        this.date.setMonth(this.date.getMonth() - c);
    }

    setDate(dateString) {
        this.date=dateString==""?new Date():new Date(dateString);
    }

    getDayOfWeek() {
        if(this.#lang == "DE") {
            return this.date.getDay()==0?6:this.date.getDay()-1;
        }
        return this.date.getDay();
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

    getDateLastMonday(givenDate) {
        // Monat wird in JS mit 0-indexiert (0 = Januar, 11 = Dezember)
        let lastDay = new Date(givenDate.getFullYear(), givenDate.getMonth()+1, 0); // letzter Tag des Monats
        let dayOfWeek = lastDay.getDay(); // Wochentag des letzten Tages (0 = Sonntag, 1 = Montag, ..., 6 = Samstag)
    
        let offset = (dayOfWeek >= 1) ? dayOfWeek - 1 : 6;
    
        lastDay.setDate(lastDay.getDate() - offset);
        return lastDay;
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

}
