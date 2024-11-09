export class Request {
    promise = null;
    // promiseList = [];
    filename = "";
    element = null;
    isLoading = false;
    data = [];

    constructor(query) {
        this.setFilename();

        if (query) {
            this.request(query);
        }
    }

    init() {
        setFilename();
        // this.promiseList = Null
    }

    setFilename(filename='./php/request.php') {
        this.filename = filename;
    }

    setElement(elementId) { // element oder ElementID
        let element = null;
        if (elementId === undefined) return;
        if (typeof elementId === "object") element = elementId;
        if (typeof elementId === "string") {
            element = document.getElementById(elementId);
        }
        this.element = element;
    }

    // startLoad startet das Laden der Daten über fetchData
    async request(query) {
        if (this.isLoading) return; // Wenn schon geladen wird, abbrechen

        this.isLoading = true; // Ladeprozess startet

        this.promise = this.fetchData({query:query}); // fetchData wird aufgerufen und ein Promise zurückgegeben

        this.promise.then(() => {
            this.isLoading = false; // Ladeprozess abgeschlossen
            // this.content = "Der Hinzugefügte Content"; // Content setzen, wenn die Daten geladen sind
        });

        // this.promiseList.push(this.promise);
    }

    // doSingle prüft, ob die Daten bereits geladen sind, und wartet notfalls
    async get() {
        if (!this.promise) {
            console.log("Request: Promise wurde noch nicht gestartet.");
            return;
        }

        console.log("Request: Warte auf den Ladeprozess...");
        await this.promise; // Warten, bis das Promise abgeschlossen ist

        // Ab hier später weg
        console.log("Request: Promise Result:", this.content); // Zeigt den geladenen Content an

        if (this.element && this.content) {
            this.element.innerHTML = this.content;
        } else {
            console.error("Request: Element zum Testen ist nicht gesetzt.");
        }
        return this.data;
    }

    // fetchData führt die Fetch-Anfrage durch
    async fetchData(keyvalues) {
        try {
            const response = await fetch(this.filename, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(keyvalues)
            });

            let data = await response.json(); // Antwort in JSON umwandeln
            this.data=data;
            console.log("Daten vom Server:", data);
            return data; // Rückgabe der Daten aus dem PHP-Skript

        } catch (error) {
            // Fehlerbehandlung
            console.log("Request:\n" + keyvalues.query);
            console.error("Fehler im PHP-Skript:", error);
            throw error;
        }
    }
}












/*
SYNTAX: 

let request = new Request("SELECT * from Datei");
oder 
let request = new Request();
request.request("SELECT * from Datei");

requets.get();
oder
await request.get();
console.log(request.data);
async function render(request) {
    await request.get();
    // request.data

}

function init() {
    const p = new Request();
    // p.setElement("content"); Duirekte zuweisung der Daten nicht möglich
    p.setFilename('./php/request.php');

    const query = "SELECT * FROM table" ; // Beispielhafter request
    p.request(query); // Startet den Ladeprozess

    // A Direkt
    render(p); 
}

*/
