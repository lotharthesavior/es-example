#!/bin/bash

IMAGE_NAME="lotharthesavior/health-app"
DEFAULT_TAG="beta-5"
TAG="${1:-$DEFAULT_TAG}"

docker build -t "$IMAGE_NAME:$TAG" .
docker push "$IMAGE_NAME:$TAG"
