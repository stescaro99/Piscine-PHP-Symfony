# Esercizio: Blog Post Symfony - E03

## Funzionalità implementate

- Creata l'entità `Post` con i campi: `title`, `content`, `created`, `author` (relazione ManyToOne con User).
- Ogni post è collegato all'utente che lo ha creato.
- La homepage dei post (`/e03/posts`) mostra la lista di tutti i post ordinati dal più recente al più vecchio, con titolo, autore e data di creazione.
- Ogni titolo di post è un link alla pagina di dettaglio, visibile solo agli utenti loggati.
- Solo gli utenti loggati possono vedere e utilizzare il form per creare un nuovo post.
- Il form di creazione post utilizza un Symfony FormType e gestisce titolo e contenuto.
- Pagina di dettaglio del post con tutte le informazioni.

## Come è stato completato il progetto

1. **Creazione entità Post**
   - Usato il comando Symfony: `php bin/console make:entity Post`
   - Aggiunti i campi richiesti e la relazione con User.
   - Eseguita la migration: `php bin/console make:migration` e `php bin/console doctrine:migrations:migrate`

2. **Repository e Form**
   - Creato il repository per Post (generato automaticamente).
   - Creato un FormType per Post per gestire la creazione tramite form Symfony.

3. **Controller**
   - Modificato `E03Controller` per:
     - Mostrare la lista dei post ordinati dal più nuovo.
     - Gestire la creazione di nuovi post solo per utenti loggati.
     - Mostrare la pagina di dettaglio del post solo a utenti loggati.

4. **Template Twig**
   - `posts.html.twig`: mostra la lista dei post, il form di creazione (solo se loggato) e i link ai dettagli.
   - `post_details.html.twig`: mostra tutti i dettagli del post selezionato.

5. **Sicurezza**
   - L'accesso alla creazione e ai dettagli dei post è riservato agli utenti autenticati.

## Note
- Per resettare il database, è disponibile la rotta `/e01/reset`.
- Tutte le funzionalità richieste sono state implementate secondo la traccia.

---

Per domande o miglioramenti, consulta il codice nei controller e nei template Twig.
