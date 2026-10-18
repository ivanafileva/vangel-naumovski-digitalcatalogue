# 48 Art Works — Vangel Naumovski (WordPress)

An interactive online catalogue of the paintings of Vangel Naumovski, built as a
custom WordPress theme and deployed on [RepoRun](https://reporun.finki.net.mk).

## What's in this repository

| Path | Purpose |
|---|---|
| `docker-compose.yml` | WordPress + MariaDB |
| `stack.yml` | Tells RepoRun to expose the `wordpress` service on port 80, publicly |
| `wordpress/Dockerfile` | Official WordPress image + WP-CLI + the theme |
| `wordpress/reporun-init.sh` | On start-up: installs WordPress (first boot only) and activates the theme |
| `wordpress/theme/vangel-naumovski/` | The theme (grid, list, gallery, about, artwork, team, news, services, contact and cookie pages) |

Database files and uploads are stored by RepoRun under `.stack-data/` and are never committed.

## Environment values (set them in RepoRun, on the stack page)

| Key | Required | Notes |
|---|---|---|
| `DB_PASSWORD` | yes | database password for WordPress |
| `DB_ROOT_PASSWORD` | yes | MariaDB root password |
| `WP_ADMIN_PASSWORD` | yes | password for the WordPress admin (`/wp-admin`) |
| `WP_ADMIN_USER` | no | defaults to `admin` |
| `WP_ADMIN_EMAIL` | no | defaults to `admin@example.com` |
| `WP_SITE_TITLE` | no | defaults to `48 Art Works` |

## Contact form

Messages sent from the Contact page are saved in WordPress and can be read in
`/wp-admin` under **Messages**. Nothing is emailed, so no mail server is needed.

## Deploy

1. Push this repository to GitHub (public, or private with RepoRun's deploy key added).
2. In RepoRun, open your team's stack → **Repository** → paste the repository URL and branch (`main`), repository path `.`.
3. Add the three required environment values above.
4. Press **Validate**, then **Deploy**. The first deploy builds the image and takes a few minutes.
5. Open the endpoint URL shown on the stack page.
