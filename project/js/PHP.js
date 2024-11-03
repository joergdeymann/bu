export class PHP {
    #filename; // Root is the Directory of the HTML file

    constructor(filename) {
        this.#filename=filename
    }

    async get(keyvalues) {
        return fetch(this.#filename, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify(keyvalues)
        })
        .then(response => response.json())
        .then(data => {
            return data; // Rückgabe vom PHP-Skript anzeigen
        })
        .catch(error => {
            // ###Fehlerbehandlung für JS SCRIPT !!
            console.error('Fehler:', error);
            throw error;
        });
    }
    
}
