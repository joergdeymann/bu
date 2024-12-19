// ISO STRING = Date("2024-12-15T14:30:00")

export class DateTime {
    datetime=new Date(); // correct Datetime = new Date()
    datetimeString; // correct Datetime as utc String;
    YYYY;
    MM;
    DD;
    hh;
    mm;
    ss;
    dayOfWeekMonday;
    dayOfWeekSunday;



    setLang(locale=null) {
        if (locale) this.locale=this.getLocale(locale);
        else this.locale=null;
    }
    
    analyse(datetime) {
        if (datetime instanceof Date) {
            this.datetime=datetime;
            this.datetime.toISOString();
            this.YYYY=datetime.getFullYear(); // Jahr
            this.MM=datetime.getMonth();    // Monat (0-basiert: 0 = Januar)
            this.DD=datetime.getDate();     // Tag des Monats
            this.hh=datetime.getHours();    // Stunden
            this.mm=datetime.getMinutes();  // Minuten
            this.ss=datetime.getSeconds();  // Sekunden
        }
        if (typeof datetime === "string") {
            let d=new Date(datetime);
            if (d != new Date(" ")) this.analyse(d);
        }
    }

    getLocale(lang) {
        if (!lang) lang=this.locale;
        if (!lang) return null;
        switch (lang.toLowerCase()) {
            case "de": return "de-DE";
            case "en": return "en-US";
        }
        return lang;

    }

    getDate(datetime,lang=null) {
        this.analyse(datetime);
        lang=this.getLocale(lang);
        // console.log(datetime.toISOString().split("T")[0]);
        if (lang) return this.datetime.toLocaleDateString(lang);
        else return this.datetime.toISOString().split("T")[0];
    }

    getTime(datetime,lang=null) {
        this.analyse(datetime);
        lang=this.getLocale(lang);
        if (lang) return this.datetime.toLocaleTimeString(lang);
        else return this.datetime.toISOString().split("T")[1];
    }
}

// Modifications:
const date = new Date();

console.log(date.toLocaleDateString("en-US", {
    weekday: "long", // Wochentag
    year: "numeric", // Jahr
    month: "long",   // Monat ausgeschrieben
    day: "numeric",  // Tag
}));
// Ausgabe (en-US): Sunday, December 15, 2024
// de-DE würde die Anzeige in deutsch anzeigen
// so kann man keine eigenen Seperatoren verwenden

