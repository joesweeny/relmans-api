#!/bin/bash

set -e

aws ecr get-login --no-include-email --region $AWS_DEFAULT_REGION | bash

docker tag "relmansapi_api" "$AWS_ECR_ACCOUNT_URL/relmans-api:$CIRCLE_SHA1"
docker push "$AWS_ECR_ACCOUNT_URL/relmans-api:$CIRCLE_SHA1"
