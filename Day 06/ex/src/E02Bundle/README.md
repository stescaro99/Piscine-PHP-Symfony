# E02Bundle - Funzionalità e spiegazione

## Descrizione generale
Questo bundle estende il progetto Symfony con una gestione avanzata degli utenti e delle autorizzazioni lato amministratore. Permette la visualizzazione, cancellazione e amministrazione degli utenti tramite interfaccia web, con protezione CSRF e controllo dei ruoli.

## Funzionalità implementate

- **Homepage personalizzata** (`/e02`):
  - Visualizza messaggio di benvenuto per utente loggato o guest.
  - Mostra link di login, registrazione, logout e, se admin, gestione utenti.

- **Gestione utenti (solo admin)** (`/e02/users`):
  - Visualizzazione tabellare di tutti gli utenti registrati.
  - Possibilità di cancellare utenti (eccetto se stesso) tramite form protetto da token CSRF.
  - Feedback visivo per azioni di successo o errore.

- **Cancellazione utente** (`/e02/delete/{id}`):
  - Solo admin può cancellare altri utenti.
  - Protezione tramite CSRF token.
  - Redirect automatico alla lista utenti dopo l’azione.

- **Ruoli e sicurezza**:
  - Accesso alle pagine di amministrazione solo per utenti con ruolo `ROLE_ADMIN`.
  - Blocco cancellazione utente per se stesso.

## Routing
- Tutte le rotte sono definite tramite attributi PHP (`#[Route(...)]`).
- Le rotte principali sono:
  - `/e02` (homepage)
  - `/e02/users` (lista utenti, solo admin)
  - `/e02/delete/{id}` (cancellazione utente, solo admin, POST)

## Sicurezza
- Utilizzo di `denyAccessUnlessGranted('ROLE_ADMIN')` per proteggere le azioni amministrative.
- Protezione CSRF su tutte le azioni di cancellazione.
- Validazione lato controller per evitare la cancellazione del proprio utente.

## Template Twig
- Template personalizzati per homepage, lista utenti, e feedback.
- Form di cancellazione utente con token CSRF e conferma.

## Dipendenze
- Symfony 5/6
- Doctrine ORM

## Note di sviluppo
- Tutte le dipendenze sono autowire-ate tramite configurazione standard Symfony.
- I controller sono posizionati in `src/Controller`.
- Le entity e repository sono in `src/Entity` e `src/Repository`.

## Esempio di utilizzo
1. Accedi come admin.
2. Vai su `/e02/users` per vedere la lista utenti.
3. Cancella un utente tramite il pulsante dedicato (non puoi cancellare te stesso).
4. Ricevi feedback visivo sull’azione.

---
Per ulteriori dettagli, consulta il codice sorgente e i template Twig nella cartella `templates/admin` e `templates/homepages`.
