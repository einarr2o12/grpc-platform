#!/bin/bash
# =============================================================================
# ArgoCD Local Installation for Docker Desktop Kubernetes
# =============================================================================
# Purpose: Install ArgoCD on Docker Desktop for local testing
# Prerequisites: Docker Desktop with Kubernetes enabled

set -e

echo "🐳 ArgoCD Installation for Docker Desktop Kubernetes"
echo "=================================================="

# Check if kubectl is available
if ! command -v kubectl &> /dev/null; then
    echo "❌ kubectl not found. Please install kubectl first."
    exit 1
fi

# Check if Kubernetes is running
echo "🔍 Checking Docker Desktop Kubernetes..."
if ! kubectl cluster-info &> /dev/null; then
    echo "❌ Kubernetes is not running. Please enable Kubernetes in Docker Desktop:"
    echo "   1. Open Docker Desktop"
    echo "   2. Go to Settings > Kubernetes"
    echo "   3. Check 'Enable Kubernetes'"
    echo "   4. Click 'Apply & Restart'"
    exit 1
fi

echo "✅ Docker Desktop Kubernetes is running!"
kubectl cluster-info

# Check if Helm is installed
if ! command -v helm &> /dev/null; then
    echo "📦 Installing Helm..."
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        brew install helm
    else
        # Linux/WSL
        curl https://get.helm.sh/helm-v3.12.0-linux-amd64.tar.gz | tar xz
        sudo mv linux-amd64/helm /usr/local/bin/
        rm -rf linux-amd64
    fi
fi

# Install ArgoCD
echo ""
echo "🚀 Installing ArgoCD..."
echo ""

# Add ArgoCD Helm repository
helm repo add argo https://argoproj.github.io/argo-helm
helm repo update

# Create ArgoCD namespace
kubectl create namespace argocd --dry-run=client -o yaml | kubectl apply -f -

# Install ArgoCD with Docker Desktop optimized settings
helm upgrade --install argocd argo/argo-cd \
  --namespace argocd \
  --values values-docker-desktop.yaml \
  --wait \
  --timeout 10m

echo ""
echo "⏳ Waiting for ArgoCD to be ready..."
kubectl wait --for=condition=available --timeout=300s deployment/argocd-server -n argocd

# Get admin password
echo ""
echo "🔑 ArgoCD Admin Credentials:"
echo "================================"
echo "Username: admin"
echo -n "Password: "
kubectl -n argocd get secret argocd-initial-admin-secret -o jsonpath="{.data.password}" | base64 -d
echo ""
echo ""

# Start port-forward in background
echo "🌐 Starting port-forward to ArgoCD..."
echo "================================"
kubectl port-forward svc/argocd-server -n argocd 8080:443 > /dev/null 2>&1 &
PORT_FORWARD_PID=$!
echo "Port-forward PID: $PORT_FORWARD_PID"

echo ""
echo "✅ ArgoCD is ready for local testing!"
echo ""
echo "📋 Quick Start Guide:"
echo "===================="
echo "1. ArgoCD UI: https://localhost:8080"
echo "2. Accept the self-signed certificate"
echo "3. Login with the credentials above"
echo ""
echo "📦 To deploy your microservices:"
echo "   kubectl apply -f applications.yaml"
echo ""
echo "🛑 To stop port-forward:"
echo "   kill $PORT_FORWARD_PID"
echo ""
echo "🗑️  To uninstall ArgoCD:"
echo "   helm uninstall argocd -n argocd"
echo "   kubectl delete namespace argocd"
echo ""

# Keep script running to maintain port-forward
echo "Press Ctrl+C to stop port-forwarding and exit..."
wait $PORT_FORWARD_PID