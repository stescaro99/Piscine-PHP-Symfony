# Anonymous Session & Timer - Esercizio E04

## Descrizione
Questo esercizio implementa una homepage per utenti anonimi in Symfony. Quando un utente anonimo accede al sito, gli viene assegnato un nome casuale dalla lista di animali, preceduto da "Anonymous" (es: Anonymous dog). Il nome viene mostrato in homepage, insieme a un messaggio che indica i secondi trascorsi dall'ultima richiesta.

La sessione anonima dura solo un minuto: se passano più di 60 secondi dall'ultima richiesta, il nome viene riassegnato casualmente.

## Come è stato risolto
- Nel controller `E04Controller`:
  - Se l'utente non è autenticato, si controlla la sessione:
    - Se non esiste un nome anonimo o sono passati più di 60 secondi dall'ultima richiesta, viene assegnato un nuovo nome casuale dalla lista di animali.
    - Il tempo dell'ultima richiesta viene aggiornato ad ogni accesso.
    - Si calcola la differenza in secondi dall'ultima richiesta e la si passa alla view.
- Nella view Twig `e04.html.twig`:
  - Viene mostrato il nome anonimo e il tempo trascorso dall'ultima richiesta, se l'utente non è autenticato.

## File principali
- `src/Controller/E04Controller.php`
- `templates/homepages/e04.html.twig`

## Note
- La lista di animali è personalizzabile nel controller.
- Il nome anonimo e il timer sono gestiti tramite la sessione Symfony.

---
Esercizio svolto secondo le specifiche del subject.
