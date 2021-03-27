## Preparation

```
docker-compose up --build -d
```

## Installation

Instalation of dependencies
```
docker run --rm -v $(pwd):/app -w /app  dependencyproject_php80 composer install
```

Creation of tables
```
docker exec -w /app dependencyproject_php80 php bin/console doctrine:schema:update --force
```

## Test
```
docker exec -w /app  dependencyproject_php80 ./vendor/bin/simple-phpunit
```

## Execution

Build the structure of dependencies
```
docker exec -w /app dependencyproject_php80 php bin/console app:build project1 project2 library1 library3 library5 library7
```

Show the dependencies between projects
```
docker exec -w /app dependencyproject_php80 php bin/console app:show
```

Show the dependencies which are affected for the change of a project
```
docker exec -w /app dependencyproject_php80 php bin/console app:dependencies libreriasiete
```

## After finish executions
```
docker-compose down
```
