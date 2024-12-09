export class Color {
    colorPalette = {
        "rot": [
            "#FF0000",  // Intensiv Rot
            "#CC0000",  // Dunkleres Rot
            "#FF4D4D",  // Helleres Rot
            "#990000",  // Sehr dunkles Rot
            "#FF6666"   // Sehr helles Rot
        ],
        "grün": [
            "#00FF00",  // Intensiv Grün
            "#00CC00",  // Dunkleres Grün
            "#66FF66",  // Helleres Grün
            "#009900",  // Sehr dunkles Grün
            "#99FF99"   // Sehr helles Grün
        ],
        "blau": [
            "#4499FF",  // #Helleres Blau
            "#0000FF",  // #Intensiv Blau
            "#87CEFA",  // #Sehr helles Blau
            "#8262E8",  // #Dunkleres Blau
            "#28EEEE",  // #Sehr dunkles Blau
        ],
        "gelb": [
            "#FFFF00",  // Intensiv Gelb
            "#DDCC00",  // Dunkleres Gelb
            "#FFCF66",  // Helleres Gelb 
            "#DFFF99",   // Sehr helles Gelb"#CFFF99"
            "#EEEE33"  // Sehr dunkles Gelb #999900
        ],
        "grau": [
            "#808080",  // Grauton 50% Helligkeit
            "#A9A9A9",  // Dunkleres Grau
            "#D3D3D3",  // Helleres Grau
            "#696969",  // Sehr dunkles Grau
            "#C0C0C0"   // Sehr helles Grau
        ]
    };
    
    
    constructor() {
        // this.colorPalette["blau"]=["#000000","#FFFFFF","#FF0000","#00FF00","#0000FF"];
    }

    /**
     * Konvertiert RGB zu HSV.
     * @param {number} r - Rot (0–255).
     * @param {number} g - Grün (0–255).
     * @param {number} b - Blau (0–255).
     * @returns {{h: number, s: number, v: number}} - HSV-Werte.
     */
    rgbToHsv(r, g, b) {
        r /= 255;
        g /= 255;
        b /= 255;

        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        const delta = max - min;

        let h = 0;
        if (delta !== 0) {
            if (max === r) {
                h = ((g - b) / delta) % 6;
            } else if (max === g) {
                h = (b - r) / delta + 2;
            } else {
                h = (r - g) / delta + 4;
            }
        }

        h = Math.round(h * 60);
        if (h < 0) h += 360;

        const s = max === 0 ? 0 : delta / max;
        const v = max;

        return { h, s, v };
    }

    /**
     * Konvertiert HSV zu RGB.
     * @param {number} h - Farbton (0–360).
     * @param {number} s - Sättigung (0–1).
     * @param {number} v - Helligkeit (0–1).
     * @returns {{r: number, g: number, b: number}} - RGB-Werte.
     */
    hsvToRgb(h, s, v) {
        const c = v * s;
        const x = c * (1 - Math.abs((h / 60) % 2 - 1));
        const m = v - c;

        let r = 0, g = 0, b = 0;
        if (h >= 0 && h < 60) {
            r = c; g = x; b = 0;
        } else if (h >= 60 && h < 120) {
            r = x; g = c; b = 0;
        } else if (h >= 120 && h < 180) {
            r = 0; g = c; b = x;
        } else if (h >= 180 && h < 240) {
            r = 0; g = x; b = c;
        } else if (h >= 240 && h < 300) {
            r = x; g = 0; b = c;
        } else if (h >= 300 && h < 360) {
            r = c; g = 0; b = x;
        }

        r = Math.round((r + m) * 255);
        g = Math.round((g + m) * 255);
        b = Math.round((b + m) * 255);

        return { r, g, b };
    }

    /**
     * Hilfsfunktion, um RGB zu Hex zu konvertieren.
     * @param {number} r - Rotwert (0–255).
     * @param {number} g - Grünwert (0–255).
     * @param {number} b - Blauwert (0–255).
     * @returns {string} - Die Hex-Repräsentation der Farbe.
     */
    rgbToHex(r, g, b) {
        if (g === null && b === null  && r !== null && typeof r === 'object')  {
            b=r.b;
            g=r.g;
            r=r.r;
        }

        if (r < 0 || r > 255 || g < 0 || g > 255 || b < 0 || b > 255) {
            throw new Error("RGB-Werte müssen zwischen 0 und 255 liegen.");
        }

        return `#${((1 << 24) | (r << 16) | (g << 8) | b).toString(16).slice(1).toUpperCase()}`;
    }

 
    hexToRgb(hex) {
        const match = /^#([0-9a-fA-F]{6})$/.exec(hex);
        if (!match) return null;
        const r = parseInt(match[1].substring(0, 2), 16);
        const g = parseInt(match[1].substring(2, 4), 16);
        const b = parseInt(match[1].substring(4, 6), 16);
        return { r, g, b };
    };


 

    /**
     * Passt die Helligkeit einer RGB-Farbe an.
     * @param {number} r - Rot (0–255).
     * @param {number} g - Grün (0–255).
     * @param {number} b - Blau (0–255).
     * @param {number} scale - Helligkeitsfaktor (>0).
     * @returns {{r: number, g: number, b: number}} - Angepasste RGB-Farbe.
     */
    adjustBrightnessAbsolute(r, g, b, scale) {
        const { h, s, v } = this.rgbToHsv(r, g, b);
        const newV = Math.min(1, Math.max(0, v * scale));
        return this.hsvToRgb(h, s, newV);
    }

    hasChangedColors(c1,c2) {
        return c1.r != c2.r || c1.g != c2.g|| c1.b != c2.b;
    }

    adjustBrightness(r, g, b, scale) {
        let c1=this.adjustBrightnessAbsolute(r, g, b, scale);
        
        if (this.hasChangedColors(c1,{r,g,b})) {
            return c1;
        }
        scale=(scale-1)*3+1;

        const { h, s, v } = this.rgbToHsv(r, g, b);
    
        let newV = v * scale;
        let newS = s;
    
        if (newV > 1) {
            newS *= 1 / newV; // Sättigung reduzieren, wenn v > 1
            newV = 1;         // Helligkeit bei 1 begrenzen
        }
    
        return this.hsvToRgb(h, newS, Math.min(1, newV));
    }
    
    isValidHexColor(color) {
        const hexColorPattern = /^#[A-Fa-f0-9]{6}$/;
        return hexColorPattern.test(color);
    }

    rgbSplit(rgb) {
        const [r, g, b] = rgb.match(/\w\w/g).map((x) => parseInt(x, 16));
        return {r,g,b}
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

    getGradientColor(hex) {
        try {
            let opacity=this.getBrightness(hex);
            let [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
            let c1=this.rgbaToRgb(r,g,b,opacity);
            return `linear-gradient(to top, ${c1}, rgba(${r},${g},${b},1))`;    
        } catch (e) {
            return hex;
        }
    }
    
    isVeryBright(r, g, b) {
        const brightness = (r + g + b) / (3 * 255); // Durchschnittliche Helligkeit (0–1)
        return brightness > 0.8; // Sehr hell, wenn > 0.8
    }
    
    isPastel(r, g, b) {
        const { h, s, v } = rgbToHsv(r, g, b);
        return v > 0.8 && s < 0.5; // Pastellartig: hohe Helligkeit, niedrige Sättigung
    }
    
  
    /**
     * Bestimmt die dominante Farbe (Rot, Grün, Blau, Gelb oder Grau) basierend auf RGB-Werten.
     * @param {number} r - Rotwert (0–255).
     * @param {number} g - Grünwert (0–255).
     * @param {number} b - Blauwert (0–255).
     * @returns {string} - Die dominante Farbe: "rot", "grün", "blau", "gelb" oder "grau".
     */
    getDominantColor(r, g, b) {
        // Validierung der Eingaben
        if (r < 0 || r > 255 || g < 0 || g > 255 || b < 0 || b > 255) {
            throw new Error("RGB-Werte müssen zwischen 0 und 255 liegen.");
        }
    
        // Grau erkennen: RGB-Werte sind nahezu gleich
        const isGray = Math.abs(r - g) < 10 && Math.abs(g - b) < 10 && Math.abs(r - b) < 10;
        if (isGray) {
            return "grau";
        }
    
        // Gelb erkennen: Rot und Grün sind hoch, Blau ist niedrig
        if (r > 200 && g > 200 && b < 100) {
            return "gelb";
        }
    
        // Dominante Farbe ermitteln
        if (r >= g && r >= b) {
            return "rot";
        } else if (g >= r && g >= b) {
            return "grün";
        } else {
            return "blau";
        }
    }
      
  /**
   * Berechnet den Abstand zwischen zwei Farben in RGB.
   * @param {string} color1 - Die erste Farbe (Hex-Code).
   * @param {string} color2 - Die zweite Farbe (Hex-Code).
   * @returns {number} - Der Abstand zwischen den beiden Farben.
   */
  colorDistance(color1, color2) {
      const rgb1 = this.hexToRgb(color1);
      const rgb2 = this.hexToRgb(color2);
  
      if (!rgb1 || !rgb2) return null;
  
      // Berechnung der Farbentfernung (euklidischer Abstand)
      const dr = rgb1.r - rgb2.r;
      const dg = rgb1.g - rgb2.g;
      const db = rgb1.b - rgb2.b;
      return Math.sqrt(dr * dr + dg * dg + db * db);
  }
  
  /**
   * Findet die ähnlichste Farbe aus der Farbpalette basierend auf einem gegebenen RGB-Wert.
   * @param {number} r - Rotwert (0–255).
   * @param {number} g - Grünwert (0–255).
   * @param {number} b - Blauwert (0–255).
   * @returns {string} - Die ähnlichste Farbe im Hex-Format.
   */
  findClosestColor(r, g, b) {
      const dominantColor = this.getDominantColor(r, g, b);
      const colors = this.colorPalette[dominantColor];
  
      let closestColor = colors[0];
      let minDistance = this.colorDistance(this.rgbToHex(r, g, b), closestColor);
  
      for (let i = 1; i < colors.length; i++) {
          const distance = this.colorDistance(this.rgbToHex(r, g, b), colors[i]);
          if (distance < minDistance) {
              minDistance = distance;
              closestColor = colors[i];
          }
      }
  
      return closestColor;
  }
  
    /**
     * Konvertiert eine RGBA-Farbe in einen RGB-Wert, wenn der Hintergrund weiß ist.
     * 
     * @param {number} r - Rot-Wert (0-255).
     * @param {number} g - Grün-Wert (0-255).
     * @param {number} b - Blau-Wert (0-255).
     * @param {number} a - Alphakanal (0-1).
     * @returns {string} RGB-Wert im Format "rgb(r, g, b)".
     */

    rgbaToRgb(r, g, b, a) {
        // Berechnung der RGB-Werte basierend auf der Formel
        // die 255 könnte man auch nehmen als hintergrund rgb hier 255,255,255 weiß
        
        const outputR = Math.round(a * r + (1 - a) * 255);  
        const outputG = Math.round(a * g + (1 - a) * 255);
        const outputB = Math.round(a * b + (1 - a) * 255);
        
        // Rückgabe im RGB-Format
        return `rgb(${outputR}, ${outputG}, ${outputB})`;
    }

  

}


// Beispiel für #0000FF (reines Blau)
// const originalColor = { r: 0, g: 0, b: 255 };
// const scale = 0.5; // 50% dunkler
// const newColor = adjustBrightness(originalColor.r, originalColor.g, originalColor.b, scale);
// console.log(newColor); // Gibt die neue RGB-Farbe aus
// Beispiel: Farbe prüfen
// console.log(isVeryBright(200, 200, 255)); // true (sehr hell)
// console.log(isPastel(200, 200, 255));     // true (pastellartig)
// Beispielaufrufe
// console.log(getDominantColor(255, 0, 0)); // "rot"
// console.log(findClosestColor(255, 100, 50)); // Gibt die ähnlichste Farbe aus der Palette zurück
// Beispiel-Aufruf
// const rgbaColor = { r: 100, g: 150, b: 200, a: 0.5 };
// const convertedRgb = rgbaToRgbOnWhite(rgbaColor.r, rgbaColor.g, rgbaColor.b, rgbaColor.a);

// console.log(convertedRgb); // Ausgabe: rgb(178, 203, 228)
  
    

// Farbmöglkichkeieten
// 0.2	Sehr dunkles Blau (#000033)	Fast schwarz
// 0.5	Dunkles Blau (#000080)	Gut sichtbar
// 1.0	Unverändert (#0000FF)	Originalfarbe
// 1.5	Helleres Blau (#4C4CFF)	Klarer Farbton
// 2.0	Sehr helles Blau (#9999FF)	Fast pastellartig
// 3.0	Sehr hell (#CCCCFF, fast Weiß)	Farbton verblasst

// Minimaler Unterschied zur ekennung:
// 0.2	#000033	Sehr dunkel, fast Schwarz.
// 0.4	#000066	Dunkles Blau, gut sichtbar.
// 0.6	#000099	Mittel-dunkles Blau.
// 0.8	#0000CC	Helleres Dunkelblau.
// 1.0	#0000FF	Originalfarbe (Standardblau).
// 1.2	#3333FF	Etwas aufgehellt, klarer Farbton.
// 1.5	#6666FF	Deutlich heller, Pastell-Ton beginnt.
// 2.0	#9999FF	Sehr hell, fast pastellartig.

// Meine Verwendung:
// 0.4 bis 2.0
// Schritte 0.4
