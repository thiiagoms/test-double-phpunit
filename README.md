# Dubles de testes com PHPUnit

## Instalação:

1. Clone o repositório; 

```bash
$ git clone https://github.com/thiiagoms/test-double-phpunit test-double
$ cd test-double
test-double$
```

2. Faça o `setup` dos containers:

```bash
test-double$ docker compose up -d
```

3. Vá até o container da aplicação (`app`):

```bash
test-double$ docker compose exec app bash
root@ebe8f78cd462:/var/www# composer install
```

4. Executar os testes:

```bash
root@ebe8f78cd462:/var/www# composer tests
```
