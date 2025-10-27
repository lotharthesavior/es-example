#!/bin/bash

kubectl apply -f deployment.yaml

kubectl patch ingress default-nginx-ingress --type='merge' -p="$(kubectl get ingress default-nginx-ingress -o json | jq '{spec: {rules: [.spec.rules[] | select(.host != "health.resende.us")]}}')"

kubectl patch ingress default-nginx-ingress --type='json' -p='[{"op": "add", "path": "/spec/rules/-", "value": {"host": "health.resende.us", "http": {"paths": [{"path": "/", "pathType": "Prefix", "backend": {"service": {"name": "health-app", "port": {"number": 80}}}}]}}}]'
