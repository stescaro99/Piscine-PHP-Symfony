# Esercizio Symfony: Login, Registrazione e Homepage

## Passaggi e comandi utilizzati

### 1. Creazione progetto Symfony
```bash
composer create-project symfony/website-skeleton nome_progetto
cd nome_progetto
```

### 2. Creazione cartella bundle
```bash
mkdir src/E01Bundle
```

### 3. Installazione pacchetti necessari
```bash
composer require symfony/security-bundle symfony/orm-pack symfony/maker-bundle
```

### 4. Creazione entity User
```bash
php bin/console make:user
```
Aggiunti i campi: email, username, password, roles.

### 5. Migrazione del database
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### 6. Generazione form di login
```bash
php bin/console make:auth
```
Scegliendo "Login form authenticator".

### 7. Generazione form di registrazione
```bash
php bin/console make:registration-form
```
Aggiunto il campo `username` al form.

### 8. Validazione unicità username/email
Aggiunto in `User.php`:
```php
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'This username is already in use.')]
```

### 9. Homepage personalizzata
- Messaggio di benvenuto con nome utente se loggato
- Link login/registrazione se non loggato

### 10. Avvio server di sviluppo
```bash
symfony serve
```
oppure
```bash
php -S 127.0.0.1:8000 -t public
```

## Note
- Tutte le funzionalità richieste sono state implementate con le versioni aggiornate di Symfony e PHP.
- La validazione di unicità è gestita da Symfony.
- I form sono generati tramite MakerBundle.

---
