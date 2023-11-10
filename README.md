# Blog application with Laravel and React

Setting up Laravel and React in the local environment with Docker that includes: Nginx, POSTGRES_QL, PHP, and REACT.

## How to Install and Run the Project

1. `docker-compose exec app composer install`
2. Copy `.env.example` to `.env`
3. `docker-compose build`
4. `docker compose up -d`
5. You can see the backend project on `127.0.0.1:8080`
6. You can see the frontend project on `127.0.0.1:3000`

## How to use PostgreSQL as a database

1. Replace your PostgreSQL db credentials and db name in the PostgreSQL configuration inside the `docker-compose.yml` including: `db`
2. Copy `.env.example` to `.env`
3. Change `DB_CONNECTION` to `pgsql`
4. Change `DB_PORT` to `5432`

## How to scrape data

I have 3 files with the name following names `theguardian_scrapper.py`, `webbnews_scrapper.py` and `bbcnews_scrapper.py`. You need to change the database credentials accordingly with your your credentials that you will setup while building docker. After that you need to run the following commands to scrape data

1. `python theguardian_scrapper.py`
2. `python webbnews_scrapper.py`
3. `python bbcnews_scrapper.py`

After executing the scrapper commands you will see that the data is scrapped and saved in the articles table.

I also create proper command in laravel and by running this command my data will be scrapped and save into the database. But due to python psycopg2 module which is not able to install that's why i recommend you the alternate way to run the data scrapper commands.

This is the command for scrapping data with laravel command that i have setup

1. `docker exec -it {container_id_of_backend_app} php artisan app:web-scraper:run`

## How to run Laravel Commands with Docker Compose

1. `cd src`
2. `docker-compose exec app php artisan {your command}`
