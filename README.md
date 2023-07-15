# Pr�requis

Modifier le fichier .env
 - Changer le prefix des containers
 - Optionnel - Changer le nom de la base et le mdp root

# Les containers

- ## Lancer les containers.

```Shell
docker-compose up -d
```

Le pr�fix -d` emp�che d'avoir les logs dans le terminal apr�s le lan�ement.

S'ils sont lanc�s pour la premi�re fois, les containers vont metre du temps � s'installer

Projet : http://localhost | PHPMyAdmin : https://localhost:8080

- ## installer les dépendances composer

```Shell
docker exec -it symfony_php bash -c 'composer install'
```

- ## créé la BDD

```Shell
docker exec -it symfony_php bash -c 'php bin/console doctrine:database:create'
```

- ## Lancer les migrations

```Shell
docker exec -it symfony_php bash -c 'php bin/console doctrine:migrations:migrate -n'
```

- ## Lancer les migrations

```Shell
docker exec -it symfony_php bash -c 'php bin/console doctrine:fixtures:load -n'
```
