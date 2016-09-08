# Fragestellung

Gemäss Prototyp ( http://buldwg.axshare.com/prototype/login/buldwg#p=trefferseite_tab_literatur_und_medien , PW: linked) sollen zu einem im Suchschlitz eingegebenen Stichwort die zugehörigen Treffer in drei Kategorien bzw. Tabs angezeigt werden:

- Werke
- Autoren
- Themen

Die Frage ist ob/wie VuFind das unterstützt. Es sind folgende Varianten denkbar:

## Alle Kategorien ausgeben

Eine Resultatseite enthält die Treffer aller Kategorien, die Inhalte nicht-angewählter Tabs werden jedoch mittels jQuery ausgeblendet.

Vorteile:

- Schneller Tabwechsel, da Inhalte schon geladen sind

Nachteile:

- Funktioniert das Blättern/Paginierung/Zählung der Treffer?

## Eine Kategorie ausgeben

Eine Resultatseite enthält die Treffer von nur einer Kategorie. Die Inhalte der andern Tabs laden eine neue, mittels PHP generierte Seite.

Vorteile:

- Keine Probleme mit Blättern/Paginierung/Zählung der Treffer

Nachteile:

- Kann die Anzahl Treffer in den Tabs für andere Kategorien angezeigt werden?
- Für Tab-Wechsel muss die Seite neu geladen werden

## Tab-Inhalte mittels Ajax laden

Die Tab-Inhalte werden nicht durch PHP-Code generiert, sondern dynamisch mittels Ajax geladen.

Vorteile:

- Es müssen nur relevante Inhalte neu geladen werden.

Nachteile:

- Funktionalität von VuFind wird umgangen.