#!/bin/bash
# =============================================================================
# ArgoCD Installation using Helm Chart
# =============================================================================
# Purpose: Install ArgoCD using Helm for better configuration management
# LSEG Requirement: "GitOps principles and practices"
# Benefits: Professional deployment, easy upgrades, customizable configuration

set -e

echo "🚀 Installing ArgoCD using Helm Chart..."

# Add ArgoCD Helm repository
echo "📦 Adding ArgoCD Helm repository..."
helm repo add argo https://argoproj.github.io/argo-helm
helm repo update

# Create ArgoCD namespace
echo "🏗️  Creating ArgoCD namespace..."
kubectl create namespace argocd --dry-run=client -o yaml | kubectl apply -f -

# Install ArgoCD using Helm with custom values
echo "⚡ Installing ArgoCD with Helm..."
helm upgrade --install argocd argo/argo-cd \
  --namespace argocd \
  --values values.yaml \
  --wait \
  --timeout 10m

echo "⏳ Waiting for ArgoCD server to be ready..."
kubectl wait --for=condition=available --timeout=300s deployment/argocd-server -n argocd

echo ""
echo "🎉 ArgoCD installed successfully using Helm!"
echo ""

# Get ArgoCD admin password
echo "🔑 ArgoCD Admin Credentials:"
echo "Username: admin"
echo -n "Password: "
kubectl -n argocd get secret argocd-initial-admin-secret -o jsonpath="{.data.password}" | base64 -d
echo ""
echo ""

# Instructions for accessing ArgoCD
echo "🌐 Access ArgoCD UI:"
echo "   Method 1 (Port Forward):"
echo "   kubectl port-forward svc/argocd-server -n argocd 8080:443"
echo "   Then open: https://localhost:8080"
echo ""
echo "   Method 2 (LoadBalancer - if available):"
echo "   kubectl get svc argocd-server-lb -n argocd"
echo ""

# Install ArgoCD CLI (optional)
echo "💻 To install ArgoCD CLI:"
echo "   # macOS"
echo "   brew install argocd"
echo ""
echo "   # Linux"
echo "   curl -sSL -o /usr/local/bin/argocd https://github.com/argoproj/argo-cd/releases/latest/download/argocd-linux-amd64"
echo "   chmod +x /usr/local/bin/argocd"
echo ""

echo "✅ ArgoCD setup complete! Ready for GitOps deployments."