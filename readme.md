## Manager API


### Installation

#### 1. Cloning

```
git clone ...
```

#### 2. Using Docker (with Laradock)

If you’re using NGINX or Apache, make sure the server_name (in case of NGINX) or ServerName (in case of Apache) in your the server config file, is set to the following `api.manager.dev`.
(Also don’t forget to set your root or DocumentRoot to the public directory inside manager manager-api/public)

#### 3. Add the domain to the Hosts file

Open the hosts file on your local machine `/etc/hosts`.
```
127.0.0.1   api.manager.dev
```
or use by machine
```
machine_ip  api.manager.dev
```

#### 4. Database, OAuth 2.0, API Documentation Setup

##### 4.1. Enter `workspace` container

```
docker exec -it workspace_name bash
```

##### 4.2. Composer install

```
composer install
cp .env.example .env
php artisan key:generate
```

##### 4.3. Database

```
php artisan migrate:status
php artisan migrate
```

##### 4.4 OAuth 2.0

Create encryption keys to generate secure access tokens and create “personal access” and “password grant” clients which will be used to generate access tokens:
```
php artisan passport:install
```

##### 4.5 API Documentation

- Install ApiDocJs using NPM or your favorite dependencies manager: Install it Globally with -g or locally in the project without -g
```
npm install apidoc -g
```
Or install it by just running `npm install` on the root of the project, after checking the package.json file on the root.

- Create folder api-rendered-markdowns
```
cd public
mkdir api-rendered-markdowns
```

- Run `php artisan apiato:docs` to generate apidoc
Behind the scene apiato:docs is executing a command like this
```
apidoc -c app/Containers/Documentation/ApiDocJs/public -f public.php -i app -o public/api/documentation
```

- Import URL postman example from: `https://www.getpostman.com/collections/112d7c48835b44e06429`
