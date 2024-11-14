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

    // startet das Laden der Daten über fetchData
    async request(query) {
        if (query) {
            query=query.replace(/\s+/g, " ").trim();
            console.log("request:",query); //DEBUG 
        } else {
            console.log("request: no Parameter"); //DEBUG 
        }
        if (this.isLoading) return; 
        this.isLoading = true; 
        const json = query ? { query: query } : {};
        this.promise = this.fetchData(json); 
        this.promise.then(() => {
            this.isLoading = false; 
        });
    }

    async get() {
        if (!this.promise) return;
        await this.promise;
        console.log(this.data);
        return this.data;
    }

    // fetchData führt die Fetch-Anfrage durch
    async fetchData(keyvalues = {}) {
        try {
            const response = await fetch(this.filename, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(keyvalues)
            });
            this.data = await response.json();

            /*
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                this.data = await response.json();
            } else {
                const errorText = await response.text(); // HTML oder Text lesen
                throw new Error("Unerwartete Antwort: " + errorText);
            }
            */
            return this.data; // Rückgabe der Daten aus dem PHP-Skript

        } catch (error) {
            console.error("Fehler im PHP-Skript:", error);
            console.error("Request:\n" + keyvalues.query);
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
