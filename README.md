# Installazione dipendenze PHP

Questo documento descrive i passaggi necessari per installare e configurare le dipendenze PHP del progetto Laravel.

---

## Prerequisiti

* PHP (versione compatibile con Laravel)
* Composer
* Node.js e npm
* MySQL (o altro database supportato)

---

## 1. Installazione delle dipendenze PHP

Esegui il comando per scaricare tutte le dipendenze dichiarate nel file `composer.json`:

```bash
composer install
```

---

## 2. Creazione del file di configurazione ambiente

Copia il file di esempio e rinominalo in `.env`:

```bash
cp .env.example .env   # su Unix/Linux/macOS
# oppure
copy .env.example .env # su Windows
```

---

## 3. Configurazione delle variabili d'ambiente

Apri il file `.env` e modifica i seguenti valori:

```env
APP_NAME="NomeApp"
APP_URL="http://localhost"

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_database
DB_USERNAME=tuo_utente
DB_PASSWORD=tua_password
```

---

## 4. Generazione della chiave dell'applicazione

Questo comando genera una chiave univoca e la salva nel file `.env`:

```bash
php artisan key:generate
```

---

## 5. Migrazioni e seeding del database

Applica le migrazioni per creare le tabelle e popola il database con i dati di default:

```bash
php artisan migrate
php artisan db:seed
```

---

## 6. Installazione e compilazione asset frontend

Installa le dipendenze Node.js e genera i file CSS/JS:

```bash
npm install && npm run dev
```

---

## 7. Risoluzione dei problemi comuni

Se riscontri errori durante l'installazione, prova a pulire le cache di Laravel:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

Il progetto è ora pronto per l'esecuzione. Avvia il server di sviluppo/dev con:

```bash
php artisan serve
```
