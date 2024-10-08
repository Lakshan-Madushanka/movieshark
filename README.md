<p align="center">
    <img src="https://github.com/Lakshan-Madushanka/movieshark/assets/47297673/80bc9013-7748-4ff1-af99-4b96410175bd" width="100" height="100"/>
</p>
<p align="center">
    <img src="https://github.com/Lakshan-Madushanka/movieshark/assets/47297673/b3e78673-78b3-4904-a8ff-d905f75ca950" width="800" height="400"/>
</p>

## Contents
- [About Movieshark](#about-movieshark)
- [Features](#features)
- [Demo](#demo)
- [Requirements](#requirements)
- [Installation](#installation)
- [Torrenting](#torrenting)
- [Disclaimer](#disclaimer)
- [Disclaimer](#disclaimer)

## About Movieshark
MovieShark is your ultimate destination for discovering and managing movies. Our user-friendly
interface allows you to effortlessly browse through a vast collection of films, filter, search,
or sort using our advanced filters section, and organize them with ease using our elegant admin
panel. Whether you're a casual movie enthusiast or a dedicated cinephile, MovieShark provides a
seamless experience tailored to your needs. Explore new releases, keep track of your movie
records using the dashboard, and effortlessly manage your favorite films. Join MovieShark today
and elevate your movie-watching experience.

## Features

- Responsive Design
- Clean & beautiful UI.
- Browse Movies
    - Search
    - Filters
- Movie Details
- Trailer & subtitle downloader
- Torrents ([Disabled by defaults](#torrenting))
- Dashboard
- WatchList
- Lot more...

## Demo
[![Click here for demo video](https://github.com/Lakshan-Madushanka/movieshark/assets/47297673/9e04f947-91b1-47c4-82ed-3b443abf5109)](https://vimeo.com/949909446?share=copy "Demo")

## Requirements
- PHP >= 8.2
- composer

## Installation
### Local
- Clone the repository.
- copy .env.example to .env
- Open a terminal and run following commands
    - `composer install`
    - `php artisan key:generate`
    - `npm run build or npm run dev`

### Docker
- `docker-compose up`
- URL - http://localhost:821/

### Sail
Consult the [documentation](https://laravel.com/docs/11.x/sail)

## Torrenting
Torrenting is considered an offensive crime in many countries, and we do not encourage its use. 
However, for educational purposes, we have provided this feature, which is disabled by default. 
You can enable it by setting the ALLOW_TORRENTING variable to true in the .env file as shown below.
```php
 // .env file
ALLOW_TORRENTING=true;
```
> [!WARNING]
> Please use this feature responsibly and always respect the rights of the owners of their digital products.

## Disclaimer
None of the content of the website belongs to me,this website will never be hosted online. 
It was made just for educational purpose and personal usage, Copyrighted content was never downloaded or shared either.

This Project does not intend to promote piracy in any form. If you found anything disturbing or some content that belongs to you, contact me and project will be taken down ASAP.




