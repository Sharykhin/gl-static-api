Static File Service API:
=======================

The response has the following format:
```
{
  "success": true | false,
  "data": {} | string | null,
  "errors": {} | string | null,
  "meta": {} | null
}
```

#### Upload File

*AUTH*: Authorized

Upload the origin image without any transformations.

```bash
POST /upload/barchart

Content-Type: multipart/form-data;
Content-Disposition: form-data; name="file";
```

JSON-out:
```json
{
    "success": true,
    "data": {
        "url": "http://localhost:8002/images/barchart/origin/b1da6814f527de5cd420cb6cbc1aa98e.jpeg",
        "bucket": "barchart",
        "fileName": "b1da6814f527de5cd420cb6cbc1aa98e.jpeg"
    },
    "errors": null,
    "meta": null
}
```

Fields Description:
- *file* - required | file | max:250kb

#### Get Image

*AUTH*: none

```bash
GET /images/{bucket}/{params}/{filename}

Status: 200 OK
```

If image exists it will be returned or 404 error.  
`{params}` is responsible for final image result. By default after uploading the 
origin param is in use, so to return the origin image, use *origin* value.
But at the same time params can be used for transforming origin image on the fly.

Params:
- *w_{int},h_{int}* - [required] crop image by width and height.
- *f_center|f_northwest|f_northeast|f_southwest|f_southeast* - [requiers w_{int},h_{int}] use with cropping to identify coords.
- *r_circle* - whether to transform an image as a circle



Example:
```bash
GET http://localhost:8002/images/barchart/origin/b1da6814f527de5cd420cb6cbc1aa98e.jpeg
GET http://localhost:8002/images/barchart/w_200,h_200/b1da6814f527de5cd420cb6cbc1aa98e.jpeg
```

#### Authorization:

Once you registered you should get access key and secret key with bucket name.
For authorization services uses *Authorization* header and supports two way of authorization:
- by using HMAC-SHA1, which is an algorithm defined by [RFC 2104 - Keyed-Hashing for Message Authentication](http://www.ietf.org/rfc/rfc2104.txt).
- by using [bcrypt](https://en.wikipedia.org/wiki/Bcrypt) hashing.

HMAC-SHA1:
```
Authorization = "GL" + " " + AccessKey + ":" + Signature;
Signature = Base64(HMAC-SHA1(SecretKey, BucketName));
```

The final result may look like this:
```
Authorization: GL 00e316f064eedb0769c2857e8dfb28d6:aGGkcilBfrAiXTSKqELx6unc23Y= 
```

Bcrypt:
```
Authorization = "GLBC" + " " + AccessKey + ":" + Signature;
Signature = BCRYPT("AccessKey:BucketName");
```

The final result may look like this:
```
Authorization: GLBC 00e316f064eedb0769c2857e8dfb28d6:$2a$12$B4Di53hnbQg920TcOd8cSuS2OPKgm6VQZfAeoUQPx2X8dj1myJhWm
```

JWT:
Until auth service is not ready you can use [https://jwt.io/](https://jwt.io/) for
generating token. Public and private keys should be generated and placed 
in */app/app/var* directory. Later we would use only public key from auth service.
```
Authorization = "Bearer" + "" + JWT
```

Currently BucketName doesn't affect *bucket* parameter for upload action.

When environment is set to `dev` by using *_debug=true* query parameter you can
avoid token verification.

Example:
```bash
POST /upload/rideshare?_debug=true
```
