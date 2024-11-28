TODO
=======
Projek anlegen:
    beim Speichern schaun wenn keine ID, ob der Text mit einer ID übereinstimmt
        - Name des Kinden
        - jeder Zeile Equipment
        - Name der UNterkunft
        - und Später Ort der Veranstaltung

    Local Setup
        Kalendar:
            Eventname als Plakette anzeigen
            Ort als Plakette anzeigen

    Global Setup: Setup
        Kalender: 
            Terminstart und -ende zulassen
            An- und Abfahrt zulassen
            Eventname als Plakette anzeigen
            Ort als Plakette anzeigen
            Nur Hauptfarben nutzen
            
        Eingabefeld: 
            Popup schließen bei Auswahl "Zrücksetzen"
            Unterkunft Auswahl anzeigen
            Terminstart und -ende anzeigen
            An- und Abfahrt anzeigen



Einige Farben:
    #colors=
        {yellow:["#FFFF00","#DDDD33","#FFFF66","#DDDDAA","#FFEE33","#EEFF00"],
         green:[ '#00FF00','#00CC00','#009900','#66FF66','#BBFFBB','#00FFAA']
        };






=================================================================================
ToDo:
==================================================================================
1. Name der Veranstaltung und Ort mit der Artikelliste verunden

2. Tagessatz: 
    Die ID merken und wenn eine Id vorhanden ist den Wert der Oben ausgewählten Sektion
    z.b. Lichttechniker überschreiben, beide Wert behalten und dann den neuen Wert zum Speichern nehemn

3. Projekt erweitern
    Hier kann ein bestehendes Projet mit neuen Daten oder mit kopierten Daten erweiteert werden
    Das Projekt wird dann angepasset mit der Länge der Dauer

4. links neben Terminstart:
    Button mit Vollerkreis minus 1/16 Kuchen = Projektansicht
    Button mitz 1/8 Kuchen                   = TeilProjekt
    als Toggle
    Hinweis on the Fly: Projekt-Ansicht / Teil-Projekt-Ansicht 

5. Als Gast einloggen
    - Werte einer Gastfirma anlegen
    - Gast account anlegen / Gast / gast

6. Buttons Anlegen mus noch überall implementiert werden.


=================================================================================
Ideen:
==================================================================================
1. Was bringst du mit ?
    Rechts ein Button der ein neues Fenster mit allen nötigen Informationen wo das
    Equipment eingesetzt wird, oder besser auf den klick zuerst eine Übersicht anzeiegn

3. Wenn ich die Firma ändere 
    - muss sich der Tages Preis anpassen, falls gesetzt oder auch so
    - muss sich die Equipment Preise anpassen, falls gesetzt oder auch so 

4. Option: 
    Background Color / sheme
    Texte Für Englisch Deutsch usw.
    local JSON array mit allen Texten


=================================================================================
Fehler:
==================================================================================
1. Wenn Firma nicht angegeben wurde, dann der Tagessatz ausgewählt wurde, kommt ein Fehler
    Fixed: 26.11.2024

2. Popup Screen setzte mal z-index auf 10 wegen den Kalender

3. Bei einzeiligen einträgen im Kalender den Text statt zentriert oben ausrichten
4. Der Border wird nicht richtig gerechnet 
    ich denke hier muss man anzahl der (Tage-1) Pixel innerhal der Woche addieren 
    bei 20 Tage beispiel: 
    Zeile 1: Tag 01-02 = 2 Tage-1 = 1 -> Grösse 2*width of cell+2px * 1
    Zeile 2: Tag 03-09 = 7 Tage-1 = 7 -> Grösse 7*width of cell+2px * 6
    Zeile 3: Tag 10-16 = 7 Tage-1 = 7 -> Grösse 7*width of cell+2px * 6
    Zeile 3: Tag 17-20 = 4 Tage-1 = 3 -> Grösse 4*width of cell+2px * 3

5. Login mit Passwort wechsel hinweis das es geändert werden muss.
    habe eigentlich das Passwort geändert und dann jemand anders einloggen lassen, der musste dann wieder das Passwort ändern
    Mach einen Plan des zusammenhangs:
        Cookies
        Sessions
        Login passwort/user stimmt


    
