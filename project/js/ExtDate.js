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
}
