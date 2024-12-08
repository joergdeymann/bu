export class ColorAdjust {
    constructor(data) {
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

    // Überlappungen verarbeiten und Helligkeitsfaktor berechnen
    calculateBrightnessFactor(current, adjustedData) {
        let brightnessFactor = 0;
        console.log("calculateBrightness");

        adjustedData.forEach((other) => {
            if (current.id !== other.id && this.doEventsOverlap(current, other)) {
                // Helligkleit erhöhen wenn 
                // 1. farbe 1 ähnlich der Farbe 2 ist
                if (!this.isVisibleDifference(current.color,other.color)) {
                    console.log("Brightness geändert",brightnessFactor);
                    brightnessFactor +=30; // Helligkeit erhöhen bei Überlappung
                    if (brightnessFactor > 90) {
                        brightnessFactor=0;
                    }
                    other.color=this.adjustNewBrightness(other.color,brightnessFactor);
    
                }
                
            }
        });

        // return Math.max(0,brightnessFactor-50); // -50 zurückgeben nötig ? glaube hier ist keine Rückgabe mehr nötig
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

            this.calculateBrightnessFactor(
                current,
                adjustedData
            )
            // const brightnessFactor = this.calculateBrightnessFactor(
            //     current,
            //     adjustedData
            // );

            this.colorList[current.id]=current.color;
            // this.colorList[current.id] = this.adjustNewBrightness(
            //     current.color,
            //     brightnessFactor
            // );
        });
    }

    updateEntries() {
        this.data.filter(entry => {
            entry.modifiedColor=this.gradientColor(this.colorList[entry.id]);
            return true;
        })
        console.log ("updateEntries",this.data);
    }

    adjustNewBrightness(hex,brightness) {
        if (hex == null ) debugger;
        console.log("brightness_adjust",brightness);
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
    
    getBrightness(hex) {
        try {
            let [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
            const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;
            if (luminance < 85) return 0.5;
            if (luminance > 170) return 0.2;
            return 0.4;
        } catch(e) {
            return 0;
        }
    }

    gradientColor(hex) {
        try {
            let opacity=this.getBrightness(hex);
            let [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
            return `linear-gradient(to top, rgba(${r},${g},${b},${opacity}), rgba(${r},${g},${b},1))`;    
        } catch (e) {
            return hex;
        }
        // let opacity=0.7
        // if ((r|g|b)>128)  {
        //     opacity=0.3
        // } 
    }
    // Hauptmethode, die die Verarbeitung steuert
    getColorList() {
        console.log("getColorList");
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
