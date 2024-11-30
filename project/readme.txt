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
    Done: 28.11.2024

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

7. Ansicht in der Projectansicht
    Schwarze Punkte für jedes Subproject
    - erst die Projekte dann anzeigen 
    - darüber dann die Subprojekte zeigen

8. Artikelpreis mit anzeigen in "Was bringst du mit"



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
    Fixed: 28.11.2024 z-index 2 und 3
    
3. Bei einzeiligen einträgen im Kalender den Text statt zentriert oben ausrichten
    Fixed: 28.11.2024

4. Der Border wird nicht richtig gerechnet 
    Fixed: 28.11.2024

5. Login mit Passwort wechsel hinweis das es geändert werden muss.
    habe eigentlich das Passwort geändert und dann jemand anders einloggen lassen, der musste dann wieder das Passwort ändern
    Mach einen Plan des zusammenhangs:
        Cookies
        Sessions
        Login passwort/user stimmt


    
=================================================================================
Hinweise:
==================================================================================
1. Was bringst du mit
    a)  beim Laden sehe ich auch Kundenspezifische Preise für die Geräte
        ich kann die Preise, aber nicht ändern
    b)  ich kann bisher noch keine Geräte anlegen
        Wenn ein Neues Gerät erkannt ist als recnum nicht da ist und Text nicht gefunden
        dann soll eine Eingabeaufforderung kommen
    c)  Wenn man den Focus verlässt und keine id Articel id vorhanden ist dann soll 
        versucht werden den Artikel zu Laden
        zb: man gibt als  Text Lichtpult Omchen ein und wenn ghefunden dann geladen werden#
        ansonsten anlegen und Preis erfragen
    d) Den Preis:
        Es gilt, wenn es einen Preis des Artikels für den Kunden gibt, dann
        wird dieser genommen, ansonsten der Artikel Preis


2. Welche Einstellung ?
    a) Autovervollständigen an: 
        sucht automatisch in der Datei bzw in dem Vorgeladenen array nach dem jeweisl ersten Wert
        und malt es grau dahinter, bei tab wird es dann angezeigt
        das Grau ist ein Absolutes Feld das in der Höhe unter dem Text geblendet wird, wie man realisiert, 
        ist wahrscheinlich komplizirter
    b) Autovervollständigen als Liste:
        das heust die Liste wird immer angezeigt sobal man im Focus ist

    c) Autovervollständigen als manuell:
        Der nutzer öffnet die Liste und kann dann wie bei b) nutzen
        Diese Einstellung ist standart Voreinstellung
    

