# Kitten Cannon score server

Dockerized high-score API (PHP 8.3 + MariaDB 11). Same endpoints/JSON as the original PHP, plus CORS so the open-quake drop-in app (or any other origin) can call it.

## Run

```bash
cp .env.example .env   # then edit the passwords
docker compose up -d --build
```

API is then at `http://<host>:8480` (change `API_PORT` in `.env`). Put it behind your reverse proxy for HTTPS.

## Verify

```bash
curl http://localhost:8480/health.php
curl http://localhost:8480/get_high_score.php
curl -X POST -d "userId=test&score=123" http://localhost:8480/save_score.php
curl "http://localhost:8480/get_personal_high_score.php?userId=test"
```

## Endpoints

| Endpoint | Method | Params | Returns |
|---|---|---|---|
| `save_score.php` | POST | `userId` (also accepts `userid`), `score`, `game` | `{success, message}` |
| `get_high_score.php` | GET | `score` (optional, for percentile), `game` | `{success, highScore, percentile, totalScores}` |
| `get_personal_high_score.php` | GET | `userId`, `game` | `{success, personalHighScore}` |
| `get_leaderboard.php` | GET | `game`, `limit` (default 10, max 100), `runs=1` (top individual runs, arcade-style repeats, instead of best-per-player) | `{success, game, leaderboard: [{rank, userid, score}]}` |
| `health.php` | GET | — | `ok` (used by container healthcheck) |

Multi-game: `game` is a slug (`a-z 0-9 _ -`, max 32 chars) and defaults to `kitten-cannon` everywhere, so the original game client works unchanged. `userId` is the player's initials: the server uppercases, strips non-letters, and crops to 3 (`t2j.s!` → `TJS`); ids with no letters are rejected. `tjs` and `TJS` are the same player.

## Unraid

Templates in [`unraid/`](unraid) — copy both `my-*.xml` to `/boot/config/plugins/dockerMan/templates-user/` and `init/01-schema.sql` to `/mnt/user/appdata/kitten-cannon-db/init/`. The API image is published by the GitHub Actions workflow to `ghcr.io/teejs/kitten-cannon-api:latest` (package must be public). Start order: db (port 3309), then api (port 8480, reaches db via host IP:3309 — bridge containers can't use container names).

## Notes

- Score is clamped to 0–100000; userid trimmed to 64 chars.
- DB schema auto-creates on first start from `init/01-schema.sql`; data persists in the `db_data` volume.
- Fixes a bug from the original: the game POSTs `userId` but the old `save_score.php` only read `userid` — this server accepts both.
- The debug file-logging from the original PHP was dropped (it wrote world-readable log files next to the scripts).
