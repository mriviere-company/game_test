# Grid hunt — PHP technical test

*[Version française plus bas](#français)*

A technical test from 2021, in **plain PHP 8 without a framework**: a turn-based game
played through HTTP routes. You move on a 21 × 21 grid to find a hidden target, which only
shows up once you are close, then you must hit it three times.

## How it is built

- One file, [`game.php`](game.php), with a small router (`/restart`, `/move`, `/shoot`).
- The game state lives on the server and is stored **encrypted (AES-256-CBC)**, so a player
  cannot read the target's position from the save file.
- The target is revealed only within a fixed radius around the player, checked on every move.

## Run it

```bash
php -S localhost:8000 game.php
```

| Route | Purpose |
|---|---|
| `/` | Show the map (M = you, T = target when close) |
| `/restart` | New game |
| `/move?action=up` | Move: `up`, `down`, `left`, `right` |
| `/shoot?x=X&y=Y` | Shoot at a cell |

## Known limits (as delivered)

- The encryption key and IV are constants in the code and the IV never changes: fine to
  stop casual cheating in a test, not real security (a key from the environment and a
  random IV per save would be).
- One save file for everyone: a single player at a time.
- The demo that was hosted at the time is no longer online.

---

<a id="français"></a>

# Chasse sur grille — test technique PHP

Test technique de 2021, en **PHP 8 natif, sans framework** : un jeu au tour par tour joué
via des routes HTTP. On se déplace sur une grille de 21 × 21 pour trouver une cible cachée,
qui n'apparaît qu'une fois à proximité, puis il faut la toucher trois fois.

## Conception

- Un seul fichier, [`game.php`](game.php), avec un petit routeur (`/restart`, `/move`,
  `/shoot`).
- L'état de la partie vit sur le serveur et il est enregistré **chiffré (AES-256-CBC)** :
  un joueur ne peut pas lire la position de la cible dans le fichier de sauvegarde.
- La cible n'est révélée que dans un rayon fixe autour du joueur, vérifié à chaque
  déplacement.

## Limites connues (à la livraison)

- La clé et le vecteur d'initialisation sont des constantes dans le code, et le vecteur ne
  change jamais : suffisant pour décourager la triche dans un test, pas une vraie sécurité
  (il faudrait une clé lue dans l'environnement et un vecteur aléatoire à chaque sauvegarde).
- Un seul fichier de sauvegarde pour tous : un joueur à la fois.
- La démo hébergée à l'époque n'est plus en ligne.
