
# Automatisert ID-Kontroll med Ansiktsgjenkjenning

Et system for aldersverifisering og identifisering ved hjelp av ansiktsgjenkjenning og QR-teknologi. Brukes i butikker, nattklubber og av nødetater for rask og sikker ID-kontroll.

## Om prosjektet

Formålet er å erstatte manuell ID-sjekk med et sikkert, effektivt system basert på maskinlæring, computer vision og ansiktsgjenkjenning. Systemet er bygget for Raspberry Pi og kan brukes til:

- Alderskontroll ved kjøp av alkohol og tobakk
- Adgangskontroll til nattklubber
- ID-verifisering for politi og ambulanse i nødsituasjoner
- Backup med QR-kode ved feil i ansiktsgjenkjenning

## Maskinvare og Programvare

### Maskinvare
- Raspberry Pi 4
- Picamera2
- MicroSD-kort
- LED-lys
- LCD-skjerm
- GPIO-moduler

### Programvare
- Raspberry Pi OS
- Python 3
- OpenCV, dlib (ansiktsgjenkjenning)
- MySQL / MariaDB
- PHPMyAdmin
- Pillow, Numpy, Pyzbar, QR-bibliotek

## Installasjonsveiledning (Raspberry Pi)

### 1. Oppdater systemet
```bash
sudo apt update
sudo apt upgrade
```

### 2. Aktiver Python-miljø (hvis aktuelt)
```bash
source id_control/bin/activate
```

### 3. Installer MariaDB og sett opp database
```bash
sudo apt install mariadb-server
sudo mysql_secure_installation
sudo mysql -u root -p
```

#### I MySQL-terminalen:
```sql
CREATE USER 'id_app_user'@'localhost' IDENTIFIED BY 'dittSikrePassord';
GRANT ALL PRIVILEGES ON id_control.* TO 'id_app_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Installer PHP og PHPMyAdmin
```bash
sudo apt install php phpmyadmin
sudo ln -s /usr/share/phpmyadmin /var/www/html
sudo nano /etc/apache2/apache2.conf
```
Legg til linjen:
```
Include /etc/phpmyadmin/apache.conf
```
Start Apache på nytt:
```bash
sudo service apache2 restart
```

### 5. Installer Python-pakker
```bash
pip install pillow numpy
pip install mysql-connector-python
pip install qrcode[pil]
pip3 install pyzbar geopy
```

### 6. Ekstra biblioteker
```bash
sudo apt install python3-pil.imagetk libzbar0 wf-recorder
```
## Logging og Opptak

- Skjermbilde:
```bash
sleep 5 && grim ~/screenshot.png
```
- Videoopptak:
```bash
wf-recorder -f ~/recording.mp4
```

### 7. Sett norsk språk (valgfritt)
```bash
sudo locale-gen nb_NO.UTF-8
sudo nano /etc/locale.gen  # Fjern kommentartegn på nb_NO.UTF-8
sudo update-locale LANG=nb_NO.UTF-8
sudo nano /etc/default/locale
# Legg inn:
LANG=nb_NO.UTF-8
LC_ALL=nb_NO.UTF-8
sudo reboot
```

## Databaseoppsett

Tabeller i databasen `id_control`:

- users: personnummer, navn, fødselsdato, bilde, QR-kode, politistatus
- logs: tidspunkt, sted, status, koblet til personnummer
- admin: brukernavn og passord for adminbrukere

## Adminportal

- Admin-panelet gir tilgang til:
  - Legge til/slette brukere
  - Redigere profiler
  - Se logger

### Adminpålogging
Disse er kun for adminpanelet (webgrensesnittet):
- Brukernavn: `group4`
- Passord: `moh76med`

## Bruk og Funksjonalitet

### Brukerregistrering
- Brukeren registrerer navn, fødselsdato og bilde
- Lagres i MySQL-databasen

### Modelltrening
- Ansiktsbilder trenes med maskinlæring (OpenCV + dlib)

### Privat ansiktsskanner
- Brukes ved adgangskontroll (f.eks. butikk/nattklubb)
- Systemet avgjør aldersstatus og viser fargetilbakemelding:
  - 🔵 Ingen ansikt funnet
  - 🟢 Full tilgang (20+)
  - 🟠 Begrenset tilgang (18–19)
  - 🔴 Avvist (under 18 eller ikke registrert)

### QR-kode-backup
- Genereres ved registrering
- Brukes ved feil i ansiktsgjenkjenning
- Dekrypteres og sjekkes mot databasen

### Nødetatens ansiktsskanner
- Viser full identitet til registrerte personer
- Brukes av politi/ambulanse i nødsituasjoner



## Etiske og Juridiske Hensyn

- Følger GDPR og norsk personvernlovgivning
- Brukerdata lagres sikkert og brukes kun med samtykke
- Informasjon om ansiktsgjenkjenning må kommuniseres tydelig
- Systemet må være nøyaktig for å unngå feilidentifisering

## Kontakt

kontakt prosjektgruppen direkte.
