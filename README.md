# Installation:

- Clone this repository and to load required dependencies run

```
composer install
```

- Create your own .env with the help of .env.example

- In .env provide your db configration and also add a new API_KEY and ADMIN_KEY. You can replace these with any random string which is used to authenticate your REST API requests.

```
API_KEY=YOUR_API_KEY

ADMIN_KEY=YOUR_ADMIN_KEY
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
Authorization: Bearer <YOUR_API_KEY or YOUR_ADMIN_KEY>
```
NOTE: In Authorization header, pass either `YOUR_API_KEY` or `YOUR_ADMIN_KEY`. With `YOUR_API_KEY`, you can only send GET requests but with `YOUR_ADMIN_KEY`, you can access all the Requests i.e. GET, POST, DELETE, etc. It is recommanded to put only `YOUR_API_KEY` in your user's App.


## User Routes:
### For Wallpapers:
```
GET wall/
GET wall/?page=2
GET wall/?s=bikes
GET wall/?category=nature
GET wall/?color=red

GET wall/?order_by=downloads
GET wall/?order_by=newest

GET wall/download/{id}

POST wall/list/
Content-Type: application/json

{
	"list": [
		23,
        24,
        33,
        142,
        45,
        60
	]
}
```

### For Categories and Colors:
```
GET list/category
GET list/color
GET init
```
## Admin Routes:

### For Wallpapers:

```
DELETE wall/{id}

POST wall/
Content-Type: application/json

{
    "source": "https://images.unsplash.com/image-web-page-url",
    "color": "#0099ff",
    "urls": {
        "full":"https://images.unsplash.com/photo-high-quality-url",
        "small":"https://images.unsplash.com/photo-low-quality-url",
        "raw":"https://images.unsplash.com/photo-max-quality-url-OPTIONAL",
        "regular":"https://images.unsplash.com/photo-average-quality-url-OPTIONAL"
    },
    "categories": [
        "nature",
        "amoled"
    ],
    "colors": [
        "blue",
        "white"
    ],
    "tags": [
        "nature",
        "sky",
        "valley",
        "peak"
    ],
    "author": {
        "user_name": "@mogli",
        "name": "Mogli",
        "url": "https://images.unsplash.com/@mogli-OPTIONAL",
        "image": "https://images.unsplash.com/mogli-image-OPTIONAL"
    },
    "rotation": 90,
    "flip": v,
    "license": "CC0-OPTIONAL"
}

POST wall/validate
Content-Type: application/json
{
    "sources": [
        "https://images.unsplash.com/image-web-page-url",
        "https://images.unsplash.com/image-web-page-url2"
    ]
}

Response:
[
    true,
    false
]
```

### For Categories and Colors:
```
POST add/category
Content-Type: application/json

{
    "name": "Nature"
}

POST add/color
Content-Type: application/json

{
    "name": "Blue",
    "value": "#0099ff"
}
```
