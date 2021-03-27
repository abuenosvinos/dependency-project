## Preparation

```
docker-compose up --build -d
```

## Installation
```
docker run --rm -v $(pwd):/app -w /app  dependencyproject_php80 composer install
```

## Test
```
docker exec -w /app  dependencyproject_php80 ./vendor/bin/simple-phpunit
```

## Execution
```
docker exec -w /app dependencyproject_php80 php bin/console app:build project1 project2 library1 library3 library5 library7
```

```
docker exec -w /app dependencyproject_php80 php bin/console app:show
```

```
docker exec -w /app dependencyproject_php80 php bin/console app:dependencies library5
```

## After finish executions
```
docker-compose down  
```
