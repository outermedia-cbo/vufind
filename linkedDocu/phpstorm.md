# About

Dieses Dokument soll das Knowhow bzgl. der Handhabung von PhpStorm sammeln.

# Look and feel anpassen

## Zeilennummern anzeigen

Für alle Dateien:
File -> Settings -> Editor -> General -> Appearance -> Show line numbers

Nur für das aktuelle Dokument:
Rechtsklick auf grauen Balken links vom Sourcecode: "Show line numbers"

## Schriftgrösse anpassen
File -> Settings -> Editor -> General -> [X] Change font size (Zoom) with Ctrl+ Mouse Wheel
Danach mit Ctrl+Mouse wheel anpassen

Schriftgrösse resetten: Ctrl+Shift+A -> "Reset font size" eingeben

## vim Plugin

Nur geeignet wenn man vi/vim kennt!

    File -> Settings -> Plugins -> Suchen nach "IdeaVim"

# Im Code navigieren

## Zur Definition einer Funktion/Klasse springen

    Ctrl-B

## Call-Hierarchie anzeigen 

    Ctrl-Alt-H

oder 
    
    Navigate -> Call Hierarchy

# Debugging

Vorgehen gemäss http://www.swissbib.org/wiki/index.php?title=Members:VuFind_Installation

## Installation von Chrome und Extension (optional)

Chrome installieren:

https://www.google.com/intl/en-US/chrome/browser/desktop/index.html -> .deb Datei herunterladen

    sudo dpkg -i XY.deb
    sudo apt-get -f install

Chrome Extension installieren:

https://chrome.google.com/webstore/detail/jetbrains-ide-support/hmhgeddbohgjknpmjagkdomcpobmllji
