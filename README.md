# Installation:

- Clone this repository and to load required dependencies run

```
composer install
```

- Create your own .env with the help of .env.example

- In .env provide your db configration and also add a new API_KEY. You can replace YOUR_API_KEY with any random string which is used to authenticate your REST API requests.

```
API_KEY=YOUR_API_KEY
```

- Now Run
```
php artisan config:cache
php artisan migrate
```

- Your server is ready to serve
```
php artisan serve
```

# REST API Documentation:

## Base URL:
```
http://127.0.0.1:8000/api/
```
### Global Headers:

```
Accept: application/json
Authorization: Bearer YOUR_API_KEY
```

### For Wallpapers:
```
GET wall/
GET wall/?page=2
GET wall/?s=bikes
GET <category-name>/wall/?page=5
GET <category-name>/wall/?s=hills&page=5

POST wall/
Content-Type: application/json

{
    "name": "Mountain Hills",
    "source": "https://images.unsplash.com/image-web-page-url",
    "color": "#0099ff",
    "tags": [
        "nature",
        "sky",
        "valley",
        "peak"
    ],
    "urls": {
        "full":"https://images.unsplash.com/photo-high-quality-url",
        "small":"https://images.unsplash.com/photo-low-quality-url",
        "raw":"https://images.unsplash.com/photo-max-quality-url-OPTIONAL",
        "regular":"https://images.unsplash.com/photo-average-quality-url-OPTIONAL"
    },
    "categories": [
        "nature",
        "skys"
    ],
    "license": "CC0-OPTIONAL",
    "author": "Mogli Nath Anna-OPTIONAL"
}

```

### For Categories:
```
GET category/

POST category/
Content-Type: application/json

{
    "name": "Nature",
    "preview_urls": [
        "https://images.unsplash.com/photo-1469474968028",
        "https://images.unsplash.com/photo-1475924156734",
        "https://images.unsplash.com/photo-1431794062232"
    ]
}
```
