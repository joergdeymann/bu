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
        if (this.isLoading) return; 
        this.isLoading = true; 
        this.promise = this.fetchData({query:query}); 
        this.promise.then(() => {
            this.isLoading = false; 
        });
    }

    async get() {
        if (!this.promise) return;
        await this.promise;
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

            this.data = await response.json(); 
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
