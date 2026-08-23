# API FizzBuzz

API REST developpee avec **Symfony 8**, **API Platform 4**, **Doctrine ORM** et **PostgreSQL**.

L'application genere une liste de valeurs FizzBuzz a partir de deux diviseurs, de deux chaines de remplacement et d'une limite. Elle enregistre egalement le nombre d'utilisations de chaque combinaison afin d'exposer la requete la plus frequente.

## Prerequis

- Docker Desktop avec Docker Compose v2
- Git
- Les ports `80`, `443` et `5432` disponibles

PHP et Composer sont fournis par les conteneurs Docker. Il n'est donc pas necessaire de les installer sur la machine hote.

## Installation

Cloner le projet puis se placer dans son dossier :

```powershell
git clone https://github.com/mouradbenfradj/TestTechniquePHP.git
cd TestTechniquePHP
```

Construire et demarrer les conteneurs :

```powershell
docker compose build
docker compose run --rm php composer install --prefer-dist --no-progress --no-interaction
docker compose up -d
```

La commande `composer install` doit etre executee avant les migrations. Elle installe notamment `symfony/runtime`, necessaire au demarrage de Symfony. L'entrypoint reinstalle automatiquement les dependances si un des fichiers `vendor/autoload.php` ou `vendor/autoload_runtime.php` est absent.

Si les conteneurs ont deja ete demarres avec un dossier `vendor` incomplet, reparer l'installation avec :

```powershell
docker compose run --rm php composer install --prefer-dist --no-progress --no-interaction
docker compose restart php
```

Creer la table utilisee pour les statistiques :

```powershell
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

Installer les assets necessaires a la documentation Swagger :

```powershell
docker compose exec php php bin/console assets:install public
```

L'application est ensuite disponible ici :

- API et documentation : <https://localhost/api/docs>
- Documentation OpenAPI JSON : <https://localhost/api/docs.jsonopenapi>

En environnement local, le certificat HTTPS genere par Caddy peut necessiter une exception dans le navigateur.

### Changer d'environnement avec Docker

Le fichier `compose.override.yaml` active automatiquement l'image de developpement lorsque Docker Compose est lance sans option `-f`. Pour passer de la production au developpement :

```powershell
docker compose -f compose.yaml -f compose.prod.yaml down
docker compose build
docker compose up -d
```

Pour passer du developpement a la production, arreter d'abord les services de developpement puis utiliser explicitement le fichier Compose de production :

```powershell
docker compose down
docker compose -f compose.yaml -f compose.prod.yaml build
docker compose -f compose.yaml -f compose.prod.yaml up -d
docker compose -f compose.yaml -f compose.prod.yaml exec php php bin/console doctrine:migrations:migrate --env=prod --no-interaction
```

Avant de demarrer la production, definir `APP_SECRET` et les autres variables de production dans l'environnement PowerShell ou dans un fichier `.env.local` non versionne. Les volumes Docker de production sont distincts des volumes de developpement. La commande `docker compose down -v` supprime les donnees de la base et ne doit etre utilisee que volontairement.

## Installation sans Docker

Cette installation necessite PHP, Composer et PostgreSQL installes directement sur la machine.

### Prerequis

- PHP `8.5` ou une version superieure
- Composer 2
- PostgreSQL 16 ou une version compatible
- Les extensions PHP `ctype`, `iconv`, `intl`, `pdo_pgsql` et `zip`
- Git

Verifier les versions installees :

```bash
php --version
composer --version
psql --version
```

### Installation du projet

Cloner le depot puis installer les dependances :

```bash
git clone https://github.com/mouradbenfradj/TestTechniquePHP.git
cd TestTechniquePHP
composer install
```

Generer une cle secrete Symfony si `APP_SECRET` est vide, puis configurer les variables locales dans `.env.local` :

```dotenv
APP_ENV=dev
APP_SECRET=changez-cette-valeur
DATABASE_URL="postgresql://app:mot_de_passe@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

La base PostgreSQL `app` et l'utilisateur indique dans `DATABASE_URL` doivent exister avant d'executer les migrations. Exemple :

```bash
sudo -u postgres psql
```

```sql
CREATE USER app WITH PASSWORD 'mot_de_passe';
CREATE DATABASE app OWNER app;
\q
```

Creer les tables applicatives :

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

Installer les assets API Platform :

```bash
php bin/console assets:install public
```

### Changer d'environnement sans Docker

L'environnement Symfony est determine par `APP_ENV`. Pour passer du developpement a la production, arreter le serveur courant, modifier `.env.local` et lancer les commandes avec `APP_ENV=prod` :

```dotenv
APP_ENV=prod
APP_SECRET=une-valeur-secrete
DATABASE_URL="postgresql://app:mot_de_passe@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

Sous PowerShell :

```powershell
$env:APP_ENV = "prod"
php bin/console cache:clear --env=prod --no-debug
php bin/console doctrine:migrations:migrate --env=prod --no-interaction
php -S 127.0.0.1:8000 -t public public/index.php
```

Pour revenir de la production au developpement, remplacer `APP_ENV=prod` par `APP_ENV=dev` dans `.env.local`, puis relancer le serveur :

```powershell
$env:APP_ENV = "dev"
php bin/console cache:clear --env=dev
php -S 127.0.0.1:8000 -t public public/index.php
```

La base de donnees et les migrations restent communes aux deux environnements lorsque la meme `DATABASE_URL` est utilisee. En production, utiliser une base et des secrets distincts et ne jamais les versionner.

### Demarrer le serveur local

Avec la CLI Symfony :

```bash
symfony server:start
```

L'application est disponible sur l'URL indiquee par la commande, generalement <http://127.0.0.1:8000>.

Sans la CLI Symfony, utiliser le serveur integre a PHP :

```bash
php -S 127.0.0.1:8000 -t public public/index.php
```

Dans ce cas, les URLs sont :

- Interface web : <http://127.0.0.1:8000/>
- Documentation API : <http://127.0.0.1:8000/api/docs>

### Utiliser l'API sans Docker

Generer une sequence FizzBuzz :

```bash
curl "http://127.0.0.1:8000/api/fizzbuzz?int1=3&int2=5&limit=15&str1=fizz&str2=buzz" \
  -H "Accept: application/json"
```

Reponse attendue :

```json
["1", "2", "fizz", "4", "buzz", "fizz", "7", "8", "fizz", "buzz", "11", "fizz", "13", "14", "fizzbuzz"]
```

Consulter la requete la plus frequente :

```bash
curl "http://127.0.0.1:8000/api/statistics/most-frequent" \
  -H "Accept: application/json"
```

Executer les tests sans Docker :

```bash
php bin/phpunit
```

Verifier la configuration et les routes :

```bash
php bin/console lint:container
php bin/console debug:router
```

## Endpoint FizzBuzz

### Requete

`GET /api/fizzbuzz`

Parametres obligatoires :

| Parametre | Type | Description | Exemple |
| --- | --- | --- | --- |
| `int1` | entier positif | Premier diviseur | `3` |
| `int2` | entier positif | Second diviseur | `5` |
| `limit` | entier de `1` a `100000` | Dernier nombre a generer | `15` |
| `str1` | chaine non vide, 100 caracteres maximum | Remplacement des multiples de `int1` | `fizz` |
| `str2` | chaine non vide, 100 caracteres maximum | Remplacement des multiples de `int2` | `buzz` |

### Exemple avec curl

```bash
curl -k -X GET \
  "https://localhost/api/fizzbuzz?int1=3&int2=5&limit=15&str1=fizz&str2=buzz" \
  -H "Accept: application/json"
```

### Exemple PowerShell

```powershell
Invoke-RestMethod `
  -Uri "https://localhost/api/fizzbuzz?int1=3&int2=5&limit=15&str1=fizz&str2=buzz" `
  -Method Get
```

### Reponse

```json
["1", "2", "fizz", "4", "buzz", "fizz", "7", "8", "fizz", "buzz", "11", "fizz", "13", "14", "fizzbuzz"]
```

Pour un nombre divisible par les deux diviseurs, les chaines sont concatenees dans l'ordre `str1str2`.

Une requete sans parametres ou avec des parametres invalides retourne une reponse `400 Bad Request`.

## Endpoint de statistiques

### Requete

`GET /api/statistics/most-frequent`

Cet endpoint n'accepte aucun parametre et retourne la combinaison de parametres ayant le plus grand nombre de requetes.

```bash
curl -k -X GET \
  "https://localhost/api/statistics/most-frequent" \
  -H "Accept: application/json"
```

### Reponse

```json
{
  "int1": 3,
  "int2": 5,
  "limit": 15,
  "str1": "fizz",
  "str2": "buzz",
  "hits": 1
}
```

S'il n'existe encore aucune requete FizzBuzz, l'endpoint retourne `null`.

## Fonctionnement technique

1. API Platform expose les ressources et genere automatiquement la documentation OpenAPI/Swagger.
2. `FizzBuzzProvider` lit et valide les parametres HTTP.
3. `FizzBuzzGenerator` contient la logique metier pure de generation de la liste.
4. `RequestStatisticRepository` enregistre chaque combinaison dans PostgreSQL avec un compteur.
5. L'instruction PostgreSQL `INSERT ... ON CONFLICT` garantit un increment atomique lorsque plusieurs requetes arrivent simultanement.
6. `MostFrequentRequestProvider` lit la combinaison ayant le plus grand nombre de hits.

La separation DTO / Provider / Service / Repository facilite les tests et limite les responsabilites de chaque classe.

## Tests et qualite

Executer toute la suite de tests :

```powershell
docker compose exec php php bin/phpunit
```

Verifier la configuration Symfony :

```powershell
docker compose exec php php bin/console lint:container
docker compose exec php php bin/console debug:router
```

Verifier l'etat des migrations :

```powershell
docker compose exec php php bin/console doctrine:migrations:status
```

## Deploiement automatique

Le workflow [ci.yaml](.github/workflows/ci.yaml) lance automatiquement le deploiement apres un push sur `main`, uniquement si toutes les etapes suivantes reussissent :

1. Construction de l'image Docker de test
2. Demarrage de Symfony et PostgreSQL
3. Creation de la base de test et execution des migrations
4. Suite PHPUnit
5. Validation du schema Doctrine
6. Test HTTP de l'API FizzBuzz
7. Analyse avec Super-Linter

Le job de deploiement installe ensuite les dependances Composer de production, prepare un package contenant le code, `public/`, `vendor/`, les migrations et les templates, puis le transfere en FTPS.

### Secrets GitHub requis

Dans **Settings > Secrets and variables > Actions**, ajouter les secrets suivants, de preference dans un environnement GitHub nomme `production` :

| Secret | Description |
| --- | --- |
| `FTP_SERVER` | Nom d'hote du serveur FTP/FTPS |
| `FTP_USERNAME` | Identifiant FTP |
| `FTP_PASSWORD` | Mot de passe FTP |
| `FTP_SERVER_DIR` | Dossier distant de l'application, avec un `/` final |

Le workflow utilise FTPS sur le port `21` et ne transfere jamais les fichiers `.env`, les tests ou les fichiers Docker. Les variables de production (`APP_SECRET`, `DATABASE_URL`, `APP_ENV=prod`) doivent etre configurees dans l'environnement PHP de l'hebergeur.

Le dossier web de l'hebergeur doit pointer vers le sous-dossier `public/`. Apres le premier deploiement, appliquer la migration sur l'environnement de production avec la commande Symfony disponible chez l'hebergeur :

```bash
php bin/console doctrine:migrations:migrate --env=prod --no-interaction
```

Le transfert FTP ne peut pas executer cette commande a distance. Si l'hebergeur ne fournit ni terminal SSH ni outil de migration, la migration doit etre appliquee depuis son interface PostgreSQL avant d'utiliser l'endpoint de statistiques.

## Commandes Docker utiles

Afficher les logs de l'application :

```powershell
docker compose logs -f php
```

Arreter les services :

```powershell
docker compose down
```

Arreter les services et supprimer les volumes, y compris les donnees PostgreSQL :

```powershell
docker compose down -v
```

La derniere commande est destructive pour les donnees locales de la base.

## Structure principale

```text
src/
├── Dto/                    # Objets exposes par l'API
├── Entity/                 # Entites Doctrine
├── Repository/             # Acces aux statistiques
├── Service/                # Logique metier FizzBuzz
└── State/                  # Providers API Platform
migrations/                 # Migrations PostgreSQL
config/packages/            # Configuration Symfony et API Platform
tests/                      # Tests PHPUnit
```

## Licence

Ce projet est distribue sous licence MIT.
