export class ColorAdjust {
    constructor(data) {
        this.data = data; // Eingabedaten
        this.colorList = {}; // Farbliste für angepasste Farben
    }

    // Helligkeit einer Farbe anpassen
    // XadjustBrightness(hex, factor) {
    //     console.log(hex);
    //     if (hex == null ) debugger;
    //     let [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));

    //     r=(r+factor) % 255;
    //     g=(g+factor) % 255;
    //     b=(b+factor) % 255;
        
    //     // r = Math.min(255, Math.max(0, r + factor));
    //     // g = Math.min(255, Math.max(0, g + factor));
    //     // b = Math.min(255, Math.max(0, b + factor));
    //     return `#${r.toString(16).padStart(2, "0")}${g.toString(16).padStart(2, "0")}${b.toString(16).padStart(2, "0")}`;
    // }


    // Datumsanpassung: start -1 Tag, end +1 Tag
    adjustDates(entry) {
        const start = new Date(entry.start);
        const end = new Date(entry.end);
        start.setDate(start.getDate() - 1);
        end.setDate(end.getDate() + 1);

        return {
            ...entry,
            start,
            end,
        };
    }

    // Überprüfen, ob zwei Events überlappen
    doEventsOverlap(event1, event2) {
        return event1.start <= event2.end && event1.end >= event2.start;
    }

    // Überlappungen verarbeiten und Helligkeitsfaktor berechnen
    calculateBrightnessFactor(current, adjustedData) {
        let brightnessFactor = 0;

        adjustedData.forEach((other) => {
            if (current.id !== other.id && this.doEventsOverlap(current, other)) {
                brightnessFactor +=40; // Helligkeit erhöhen bei Überlappung
                if (brightnessFactor > 90) {
                    brightnessFactor=0;
                }
                
            }
            // if (current.id !== other.id && !this.doEventsOverlap(current, other)) {
            //      brightnessFactor = 101; // Helligkeit erhöhen bei Überlappung
            // }
        });

        return Math.max(0,brightnessFactor-50);
    }

    isValidHexColor(color) {
        const hexColorPattern = /^#[A-Fa-f0-9]{6}$/;
        return hexColorPattern.test(color);

    }

    // Farben anpassen und speichern
    updateColors(adjustedData) {
        adjustedData.forEach((current) => {
            if (!this.isValidHexColor(current.color)) {
                this.colorList[current.id]=current.color;
                return;                
            }

            const brightnessFactor = this.calculateBrightnessFactor(
                current,
                adjustedData
            );

            this.colorList[current.id] = this.adjustNewBrightness(
                current.color,
                brightnessFactor
            );
        });
    }

    updateEntries() {
        console.log ("updateEntries",this.colorList);
        this.data.filter(entry => {
            entry.color=this.gradientColor(this.colorList[entry.id]);
            return true;
        })
    }

    adjustNewBrightness(hex,brightness) {
        console.log(hex);
        if (hex == null ) debugger;

        let [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
        const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;

        let scale=1+(brightness/100); 
        if (brightness > 0) {
            if (luminance <100 && brightness <50) { // and brightness > 0 
                scale*=1.3; 
            } else scale *=0.9;
    
        }
        


        r = Math.min(255, Math.max(0, Math.round(r * scale)));
        g = Math.min(255, Math.max(0, Math.round(g * scale)));
        b = Math.min(255, Math.max(0, Math.round(b * scale)));
        return `#${r.toString(16).padStart(2, "0")}${g.toString(16).padStart(2, "0")}${b.toString(16).padStart(2, "0")}`;
    }
    
    getBrightness(hex) {
        let [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
        const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;
        if (luminance < 85) return 0.7;
        if (luminance > 170) return 0.3;
        return 0.5;
        
        // Normierung auf den Bereich 0 bis 1
        //return luminance / 255;

    }
    gradientColor(hex) {
        let opacity=this.getBrightness(hex);
        let [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
        // let opacity=0.7
        // if ((r|g|b)>128)  {
        //     opacity=0.3
        // } 
        return `linear-gradient(to top, rgba(${r},${g},${b},${opacity}), rgba(${r},${g},${b},1))`;
    }
    // Hauptmethode, die die Verarbeitung steuert
    getColorList() {
        console.log("getColorList");
        const adjustedData = this.data.map((entry) => this.adjustDates(entry));
        this.updateColors(adjustedData);
        this.updateEntries();
        return this.colorList;
    }
}

function test() {
    // Beispiel-Daten
    const data = [
        { id: 1, color: "#FF5733", name: "Event A", start: "2024-11-01", end: "2024-11-05" },
        { id: 2, color: "#33FF57", name: "Event B", start: "2024-11-04", end: "2024-11-08" },
        { id: 3, color: "#3357FF", name: "Event C", start: "2024-11-07", end: "2024-11-12" },
    ];

    // Klasse instanziieren und ausführen
    const processor = new ColorAdjust(data);
    const colorList = processor.getColorList();

    console.log("Color List mit angepassten Farben:", colorList);

}
