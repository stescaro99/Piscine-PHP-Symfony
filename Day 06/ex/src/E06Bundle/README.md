# ex06 - Funzionalità implementate

Questo modulo Symfony implementa un sistema di gestione dei post con le seguenti funzionalità principali:

## Funzionalità principali

- **Visualizzazione post**: ogni post mostra titolo, autore (con reputazione), data di creazione, contenuto, numero di like/dislike e pulsanti per votare.
- **Modifica post**: l'autore può modificare il proprio post tramite un form dedicato.
- **Cancellazione post**: l'autore può cancellare il proprio post.
- **Votazione**: gli utenti autenticati possono mettere like o dislike ai post.
- **Tracciamento modifiche**: se un post viene modificato, in fondo alla pagina dei dettagli viene mostrato chi ha effettuato l'ultima modifica e quando.

## Dettagli tecnici

- Entità `Post` aggiornata con i campi `updated` (data/ora ultima modifica) e `lastEditedBy` (utente che ha effettuato l'ultima modifica).
- Modifica e salvataggio dei post aggiornano automaticamente questi campi.
- Le informazioni sull'ultima modifica vengono mostrate solo se il post è stato effettivamente modificato.
- Tutte le operazioni sono protette: solo l'autore può modificare o cancellare i propri post.

## File principali

- `src/Entity/Post.php`: definizione dell'entità Post e dei nuovi campi.
- `src/Controller/E06Controller.php`: logica di visualizzazione, modifica, cancellazione e votazione dei post.
- `templates/posts/post_details06.html.twig`: template per la visualizzazione dettagliata del post, inclusa la sezione "last edited".
- `templates/posts/post_edit.html.twig`: template per la modifica del post.

## Migrazioni

Dopo aver aggiunto i nuovi campi all'entità Post, sono state generate e applicate le relative migration Doctrine per aggiornare lo schema del database.

---

Per domande o miglioramenti, contattare lo sviluppatore.
