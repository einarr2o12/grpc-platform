# GitOps Setup Guide with ArgoCD

## Overview

This guide explains how to set up the complete GitOps pipeline with GitHub Actions and ArgoCD.

## Architecture

```
Developer Push → GitHub Actions → Build Image → Push to Registry → Update K8s Manifests → ArgoCD Syncs → Deploy to K8s
```

## Prerequisites

1. **Docker Hub Account** (or another container registry)
2. **GitHub Repository** with secrets configured
3. **ArgoCD** installed in your Kubernetes cluster
4. **kubectl** access to your cluster

## Step 1: Configure GitHub Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions

Add these secrets:

### Required Secrets:
- `DOCKERHUB_USERNAME`: Your Docker Hub username
- `DOCKERHUB_TOKEN`: Docker Hub access token (not password!)
- `SONAR_TOKEN`: From SonarCloud (you already have this)
- `SONAR_ORGANIZATION`: Your SonarCloud org (you already have this)
- `SONAR_PROJECT_KEY`: Your SonarCloud project key (you already have this)

### How to get Docker Hub Token:
1. Log in to [Docker Hub](https://hub.docker.com)
2. Go to Account Settings → Security
3. Click "New Access Token"
4. Give it a name (e.g., "github-actions")
5. Copy the token and save it as `DOCKERHUB_TOKEN`

## Step 2: Update Kubernetes Manifests

Update your deployment files to use your Docker Hub username:

```bash
# Example: Update category-service deployment
sed -i 's|image: 1911997/bff-category:v1.0|image: YOUR_DOCKERHUB_USERNAME/category-service:latest|g' infrastructure/k8s/category/deployment.yaml
```

## Step 3: Create ArgoCD Applications

Apply the ArgoCD applications to watch your repository:

```bash
# Make sure you're in the correct directory
cd infrastructure/argocd

# Apply the applications
kubectl apply -f applications.yaml
```

## Step 4: How the Pipeline Works

1. **Developer pushes code** to main branch
2. **GitHub Actions triggers**:
   - Runs tests and quality checks
   - Runs security scans
   - Builds Docker image with unique tag
   - Pushes to Docker Hub
   - Updates K8s manifest with new image tag
   - Commits the change back to Git
3. **ArgoCD detects change**:
   - Sees the updated manifest
   - Pulls new image from Docker Hub
   - Deploys to Kubernetes

## Step 5: Monitoring Deployments

### In ArgoCD UI:
- Watch applications sync automatically
- See deployment history
- Rollback if needed

### In GitHub Actions:
- Monitor build status
- Check image tags in job summaries
- Review manifest updates in commits

## Step 6: Testing the Pipeline

1. Make a small change to any microservice
2. Push to main branch
3. Watch the GitHub Actions workflow
4. Check ArgoCD UI for automatic deployment
5. Verify the service is updated in Kubernetes

## Troubleshooting

### Pipeline fails at Docker push:
- Check `DOCKERHUB_USERNAME` and `DOCKERHUB_TOKEN` secrets
- Ensure token has push permissions

### ArgoCD not syncing:
- Check if ArgoCD application is created
- Verify repository URL in ArgoCD app
- Check ArgoCD has access to your Git repo

### Image not updating:
- Check the manifest was updated in Git
- Verify ArgoCD sync policy is set to automatic
- Check image pull policy in deployment

## Benefits of This Setup

1. **Automated**: No manual deployments
2. **GitOps**: Git as single source of truth
3. **Auditable**: Every change tracked in Git
4. **Rollback**: Easy via Git revert or ArgoCD
5. **Multi-environment**: Easy to add dev/staging/prod

## Next Steps

1. Add environment-specific configurations
2. Implement progressive deployments
3. Add deployment notifications
4. Set up multi-cluster deployments