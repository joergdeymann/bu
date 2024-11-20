export class ColorAdjust {
    constructor(data) {
        this.data = data; // Eingabedaten
        this.colorList = {}; // Farbliste für angepasste Farben
    }

    // Helligkeit einer Farbe anpassen
    adjustBrightness(hex, factor) {
        console.log(hex);
        let [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
        r = Math.min(255, Math.max(0, r + factor));
        g = Math.min(255, Math.max(0, g + factor));
        b = Math.min(255, Math.max(0, b + factor));
        return `#${r.toString(16).padStart(2, "0")}${g.toString(16).padStart(2, "0")}${b.toString(16).padStart(2, "0")}`;
    }


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
                brightnessFactor += 100; // Helligkeit erhöhen bei Überlappung
            }
            // if (current.id !== other.id && !this.doEventsOverlap(current, other)) {
            //      brightnessFactor = 101; // Helligkeit erhöhen bei Überlappung
            // }
        });

        return Math.max(0,brightnessFactor-100);
    }

    // Farben anpassen und speichern
    updateColors(adjustedData) {
        adjustedData.forEach((current) => {
            const brightnessFactor = this.calculateBrightnessFactor(
                current,
                adjustedData
            );

            this.colorList[current.id] = this.adjustBrightness(
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
