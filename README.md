# COSA FA IL MODULO
-------------------
Questo modulo permette l'autenticazione spid su Drupal 8 tramite la libreria
SimpleSPIDphp-PASW (raccomandata) o altra libreria assimilabile. 
La procedura di installazione prevede i seguenti passi:
1) Installazione della libreria
2) Richiesta ad AGID e conseguente test della libreria
3) Installazione del modulo externalauth-pasw
4) Installazione del modulo e sua configurazione

NOTA IMPORTANTE: se scompare il bottone SPID dalla pagina di login,
ricordarsi di attivarlo dalla configurazione, altrimenti è possibile
posizionare a mano il blocco nella posizione voluta.

NOTA: il modulo è in versione beta pienamente funzionante, tuttavia
alcune stringhe della configurazione non sono ancora state tradotte
in italiano.

# Installazione della libreria
------------------------------
La prima (e indispensabile) cosa da fare è la sistemazione del file .htaccess nella
root di Drupal 8. Aprire il file con un editor e dopo le righe
```
  # Allow access to test-specific PHP files:
  RewriteCond %{REQUEST_URI} !/core/modules/system/tests/https?.php
```
inserire le righe:
```
  # PASW SPID
  RewriteCond %{REQUEST_URI} !^/spid
```
Quindi scaricare (anche da www.github.com/pagolo) il pacchetto spidinst.zip
ed estrarre il file install.php. COPIARE IL FILE install.php NELLA ROOT DANDOGLI
IL NOME spidinst.php E LANCIARLO COSI:
https://nomesito.gov.it/spidinst.php
completare la procedura aiutandosi eventualmente con il tutorial scaricabile  da
www.scuolacooperativa.net/drupal7

Nel caso non si usi l'installer dedicato occorrerà a questo punto intervenire
per INSERIRE IN FONDO AL FILE sites/default/settings.php QUESTE RIGHE
```
/**
 * PASW SPID
 */
$settings['simplesamlphp_dir'] = '<path_assoluto_libreria>';
```
DOVE <path_assoluto_libreria> STA PER PERCORSO ASSOLUTO DELLA
CARTELLA della libreria, PER ES. /home/drupal8/simplesamlphp . Nel
caso di dubbio consultare il provider.

# Richiesta ad AGID e conseguente test della libreria
-----------------------------------------------------
Consultare il tutorial PASW e il sito Agid dedicato a SPID

# Installazione del modulo externalauth-pasw
--------------------------------------------
Il modulo richiede la presenza di un altro modulo: externalauth.
Consigliamo di usare la versione pasw del modulo presente sul
nostro canale github.

# Installazione del modulo e sua configurazione
-----------------------------------------------
Scaricare il modulo e decomprimerlo, all'interno si troverà la cartella
drupal8-spid-master da rinominare in spid_pasw.
Installare i modulo caricando la cartella (rinominata) spid_pasw
nella cartella /modules di Drupal 8 e intervenendo come al solito nella
amministrazione di Drupal (Estendi->elenco spuntare Spid_PASW e premere
sul bottone installa). Una volta installato il modulo, entrare nella sua
configurazione, attivarlo (prima spunta e salva). Verificare a questo
punto che, entrando come utente anonimo e premendo su accedi compaia
anche il bottone di SPID. Se non compare provare a pulire tutte le cache
e ritentare oppure attivare il bottone dalla configurazione.
Quando tutto è funzionante occorrerà anche ricordarsi di avviare la
procedura amministrativa Agid per il riconoscimento finale del servizio.

# Crediti
----------
Autore del modulo: Paolo Bozzo (pagolo DOT bozzo AT gmail DOT com)
- Rete "Porte aperte sul Web" (Nadia Caprotti, Antonio Todaro, Helios
  Ciancio, Marco Milesi)
- Modulo simpleSAMLphp Authentication e suoi maintainer
- AGID (Umberto Rosini)
- Comune di Firenze (modulo SPID-Drupal)
- Referenti tecnici degli IDP Spid
