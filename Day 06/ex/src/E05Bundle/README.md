# Esercizio E05 - Like, Dislike e Reputazione Utente

## Funzionalità implementate

- Gli utenti loggati possono votare ogni post con un like o un dislike.
- Ogni utente può votare un post solo una volta, ma può cambiare il proprio voto.
- I link per votare sono presenti sia nella lista dei post che nella pagina di dettaglio.
- La lista dei post e la pagina di dettaglio mostrano il numero di like e dislike ricevuti da ciascun post.
- Accanto al nome utente (autore del post) viene mostrata la reputazione, calcolata come somma dei like meno i dislike ricevuti su tutti i suoi post.

## Come è stato risolto

1. **Entità Vote**: creata una nuova entità `Vote` con relazione ManyToOne verso `User` e `Post`, e un campo `type` ("like" o "dislike").
2. **Entità Post**: rimossa la proprietà `likes` e aggiunta la relazione OneToMany con `Vote`. Aggiunti i metodi `countLikes()` e `countDislikes()`.
3. **Entità User**: aggiunta la relazione OneToMany con `Post` e `Vote`, e il metodo `getReputation()` per calcolare la reputazione.
4. **Migrazioni**: creata una migration per aggiungere la tabella `vote` e rimuovere la colonna `likes` da `posts`.
5. **Controller**: aggiornato `E05Controller` per gestire i voti tramite la tabella `Vote`, impedendo voti multipli e permettendo il cambio di voto.
6. **Template Twig**: aggiornati i template per mostrare like/dislike, reputazione e link per votare.

## Note
- Per applicare le modifiche al database, eseguire:
  ```
  php bin/console doctrine:migrations:migrate
  ```
- La reputazione viene aggiornata in tempo reale e mostrata accanto a ogni username.
- I voti sono permessi solo agli utenti autenticati.
