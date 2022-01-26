# Base URL:
```
http://example.com/api/

```
## Global Headers:

Accept: application/json

*******************************************************************************
## Wallpapers:
```
GET wall/
GET wall/?page=2
GET wall/?s=bikes

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
        1,
        9
    ],
    "license": "CC0-OPTIONAL",
    "author": "Mogli Nath Anna-OPTIONAL"
}

```
************************************************************
## Categories
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

