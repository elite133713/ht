## Testing

Copy env file ``cp .env.example .env``

Setup aws credentials or use localstack

Run docker ``docker-compose up -d --build``

When use localstack copy files to S3

Go to container ``docker compose exec -it app bash`` then run ```aws --endpoint-url=http://localstack:4566 s3 cp YOUR_FILE_PATH s3://my-bucket/YOUR_FILE_PATH```

Test copying ``php artisan app:download YOUR_PATH`` 

Example ``php artisan app:download ''`` for root directory
