#!/bin/bash

echo "🚀 Installing gRPC Microservices Stack on Kubernetes"

# Check if kubectl is available
if ! command -v kubectl &> /dev/null; then
    echo "❌ kubectl is not installed"
    exit 1
fi

# Check if helm is available
if ! command -v helm &> /dev/null; then
    echo "❌ helm is not installed"
    exit 1
fi

# Function to wait for deployment
wait_for_deployment() {
    local deployment=$1
    local namespace=${2:-default}
    echo "⏳ Waiting for $deployment to be ready..."
    kubectl wait --for=condition=available --timeout=300s deployment/$deployment -n $namespace
}

# Function to wait for pods
wait_for_pods() {
    local label=$1
    local namespace=${2:-default}
    echo "⏳ Waiting for pods with label $label to be ready..."
    kubectl wait --for=condition=ready --timeout=300s pods -l $label -n $namespace
}

echo "1️⃣ Building Docker images..."
cd ../
docker build -t category-service:latest ./category-service/
docker build -t product-service:latest ./product-service/
docker build -t go-api-gateway:latest ./go-api-gateway/

echo "2️⃣ Deploying MongoDB for Category Service..."
kubectl apply -f k8s/category/mongodb.yaml
wait_for_deployment "mongodb-category"

echo "3️⃣ Deploying Category Service..."
kubectl apply -f k8s/category/deployment.yaml
kubectl apply -f k8s/category/service.yaml
wait_for_deployment "category-service"

echo "4️⃣ Deploying MongoDB for Product Service..."
kubectl apply -f k8s/product/mongodb.yaml
wait_for_deployment "mongodb-product"

echo "5️⃣ Deploying Product Service..."
kubectl apply -f k8s/product/deployment.yaml
kubectl apply -f k8s/product/service.yaml
wait_for_deployment "product-service"

echo "6️⃣ Deploying API Gateway..."
kubectl apply -f k8s/controller/deployment.yaml
kubectl apply -f k8s/controller/service.yaml
wait_for_deployment "api-gateway"

echo "7️⃣ Installing Kong OSS..."
helm repo add kong https://charts.konghq.com
helm repo update

# Apply Kong configuration
kubectl apply -f k8s/kong/kong-config.yaml

# Install Kong with custom values
helm upgrade --install kong kong/kong --namespace default -f k8s/kong/values.yaml

echo "⏳ Waiting for Kong to be ready..."
sleep 30
kubectl wait --for=condition=available --timeout=300s deployment/kong-kong

echo "8️⃣ Installing SigNoz for Observability..."
helm repo add signoz https://charts.signoz.io
helm repo update

# Create namespace for SigNoz
kubectl create namespace platform --dry-run=client -o yaml | kubectl apply -f -

# Install SigNoz
helm upgrade --install signoz signoz/signoz --namespace platform -f k8s/signoz/values.yaml

echo "✅ Deployment completed!"
echo ""
echo "🔗 Access URLs:"
echo "Kong Proxy: http://localhost (if using LoadBalancer)"
echo "Kong Admin: http://localhost:8001"
echo "Kong Manager: http://localhost:8002"
echo "SigNoz: http://localhost:3301 (in platform namespace)"
echo ""
echo "🧪 Test the API:"
echo "curl http://localhost/api/categories"
echo "curl -X POST http://localhost/api/categories -H 'Content-Type: application/json' -d '{\"name\":\"Electronics\",\"description\":\"Electronic devices\"}'"
echo ""
echo "📊 Check pod status:"
echo "kubectl get pods -o wide"
echo "kubectl get services"