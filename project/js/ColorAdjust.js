import {Color} from './Color.class.js';
export class ColorAdjust {
    constructor(data) {
        this.color=new Color();
        this.data = data; // Eingabedaten
        this.colorList = {}; // Farbliste für angepasste Farben
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


    
    isSimilarColor(obj,count) {
        let co=Object.values(this.color.rgbSplit(obj));
        let dominantColor=this.color.getDominantColor(...co);
        let nearestColor=this.color.findClosestColor(...co);
        return this.color.colorPalette[dominantColor][count]==nearestColor;

    }

    // Überlappungen verarbeiten und Helligkeitsfaktor berechnen
    calculateBrightnessFactor(current, adjustedData) {

        let brightnessFactor = 1.0;
        let count=0;

        adjustedData.forEach((other) => {
            if (current.id !== other.id && this.doEventsOverlap(current, other)) {
                if (!this.isVisibleDifference(current.color,other.color)) {

                    let co=Object.values(this.color.rgbSplit(other.color));
                    let dominantColor=this.color.getDominantColor(...co);

                    if (this.isSimilarColor(other.color,count)) {
                        ++count;
                        count%=5;
                    }
                    if (this.isSimilarColor(current.color,count)) {
                        ++count;
                        count%=5;
                    }

                    other.color=this.color.colorPalette[dominantColor][count];
                    count++;
                    count%=5;
                }
                
            }
        });
    }

    // Farben anpassen und speichern
    updateColors(adjustedData) {
        adjustedData.forEach((current) => {
            if (!this.color.isValidHexColor(current.color)) {
                this.colorList[current.id]=current.color;
                return;                
            }

            this.calculateBrightnessFactor(
                current,
                adjustedData
            )

            this.colorList[current.id]=current.color;
        });
    }

    updateEntries() {
        this.data.filter(entry => {
            entry.modifiedColor=this.color.getGradientColor(this.colorList[entry.id]);
            return true;
        })
    }

    adjustNewBrightness(hex,brightness) {
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
        console.log(brightness,hex,`#${r.toString(16).padStart(2, "0")}${g.toString(16).padStart(2, "0")}${b.toString(16).padStart(2, "0")}`);
        return `#${r.toString(16).padStart(2, "0")}${g.toString(16).padStart(2, "0")}${b.toString(16).padStart(2, "0")}`;
    }
    
    // Hauptmethode, die die Verarbeitung steuert
    getColorList() {
        const adjustedData = this.data.map((entry) => this.adjustDates(entry));
        this.useMainColors(adjustedData);
        this.updateColors(adjustedData);
        this.updateEntries();
        return this.colorList;
    }

    useMainColors(entries) {
        if (opt.mobileCalendar.mainColors) {
            for(let entry of entries) entry.color=entry.rootColor;
        }
        return;
    }

    isVisibleDifference(rgb1, rgb2) {
        if (!rgb1 || !rgb2) return true;
        let [r1, g1, b1] = rgb1.match(/\w\w/g).map((x) => parseInt(x, 16));
        let [r2, g2, b2] = rgb2.match(/\w\w/g).map((x) => parseInt(x, 16));

        const rDiff = r1 -r2;
        const gDiff = g1 - g2;
        const bDiff = b1 - b2;

        return Math.sqrt(rDiff ** 2 + gDiff ** 2 + bDiff ** 2)>5;
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
