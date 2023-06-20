#!/bin/bash

# Wait for LocalStack to be ready
while ! nc -z localstack 4566; do
    echo "Waiting for LocalStack..."
    sleep 1
done

# Create S3 bucket
aws --endpoint-url=http://localstack:4566 s3 mb s3://my-bucket

# Create SQS queue
aws --endpoint-url=http://localstack:4566 sqs create-queue --queue-name my-queue

# Run Supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
