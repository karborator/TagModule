# TagModule
ZF3 tag module

Manage tags for specific context through an Rest API.

# Installation
  Use composer 

# Configuration

Tags that can be used from the system internally. 

    'system_tags'            => [
            'image' => 'media.image',
            'video' => 'media.video',            
        ],


# Usage Rest 

# GET : http://localhost:8080/tag 
# 
    {
      "_links": {
        "self": {
          "href": "http://localhost:8080/tag?page=1"
        },
        "first": {
          "href": "http://localhost:8080/tag"
        },
        "last": {
          "href": "http://localhost:8080/tag?page=2"
        },
        "next": {
          "href": "http://localhost:8080/tag?page=2"
        }
      },
      "_embedded": {
        "tag": [
          {
            "id": 95,
            "tag": "Media\\Service\\ImageContentProvider::id=184::media.image",
            "context": "Settings\\Service\\ServicesContentProvider::11",
            "_links": {
              "self": {
                "href": "http://localhost:8080/tag/95"
              }
            }
          }
        ]
      },
      "page_count": 2,
      "page_size": 25,
      "total_items": 38,
      "page": 1
    }

# GET : http://localhost:8080/tag/95
#
    {
      "id": 95,
      "tag": "http://localhost:8080/content-article/31",
      "context": "http://localhost:8080/media-image/231",
      "_links": {
        "self": {
          "href": "http://localhost:8080/tag/95"
        }
      }
    }

# POST: http://localhost:8080/tag
#
    {
    	"tag": "http://localhost:8080/content-article/31",
    	"context": "http://localhost:8080/media-image/231"
    }
    
    {
      "id": 147,
      "tag": "Content\\Service\\ArticleContentProvider::id=31",
      "context": "Media\\Service\\ImageContentProvider::231",
      "_links": {
        "self": {
          "href": "http://localhost:8080/tag/147"
        }
      }
    }

# PATCH: http://localhost:8080/tag/147
#
    {
      "tag": "http://localhost:8080/content-article/32",
      "context": "http://localhost:8080/media-image/231"
    }
    
    {
      "id": 147,
      "tag": "Content\\Service\\ArticleContentProvider::id=32",
      "context": "Media\\Service\\ImageContentProvider::231",
      "_links": {
        "self": {
          "href": "http://localhost:8080/tag/147"
        }
      }
    }

# DELETE: http://localhost:8080/tag/147
    
    no content returned
