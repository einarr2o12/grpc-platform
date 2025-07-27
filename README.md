# 🚀 gRPC Developer Platform

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=grpc-platform-go-api-gateway&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=grpc-platform-go-api-gateway)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=grpc-platform-go-api-gateway&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=grpc-platform-go-api-gateway)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=grpc-platform-go-api-gateway&metric=coverage)](https://sonarcloud.io/summary/new_code?id=grpc-platform-go-api-gateway)

## 🎯 Enterprise-grade gRPC Microservices Platform

This project demonstrates a production-ready microservices architecture with:

- **🔄 CI/CD Pipelines**: GitHub Actions with quality gates
- **🛡️ Security Scanning**: SonarCloud + Trivy integration  
- **📊 Code Quality**: Automated quality gates and reporting
- **🌐 Multi-language Services**: Go, Node.js microservices
- **☸️ Kubernetes Ready**: Complete infrastructure as code
- **📈 Observability**: Zero-code monitoring with Istio

## 🏗️ Architecture

```
External Traffic → Kong Gateway → Go API Gateway → gRPC Services
                                                 ├── Category Service (Node.js)
                                                 ├── Product Service (Node.js)
                                                 └── MongoDB Databases
```

## 🚀 Services

| Service | Language | Port | Purpose |
|---------|----------|------|---------|
| **go-api-gateway** | Go | 8081 | HTTP-to-gRPC translation |
| **category-service** | Node.js | 50051 | Category management |
| **product-service** | Node.js | 50052 | Product management |

## 🔧 Developer Experience

### **Quality Gates**
- ✅ **Code Coverage**: Minimum 80% required
- ✅ **Security Scanning**: Zero critical vulnerabilities
- ✅ **Code Quality**: SonarCloud quality gates
- ✅ **Linting**: Language-specific best practices

### **CI/CD Pipeline**
Each service has automated:
1. **Code Quality Analysis** (SonarCloud)
2. **Security Scanning** (Trivy)
3. **Unit Testing** with coverage
4. **Docker Build & Push**
5. **Infrastructure Updates**

## 🛡️ Security

- **Secret Scanning**: Trivy detects committed secrets
- **Dependency Scanning**: Vulnerability analysis for Go modules, npm packages
- **Container Scanning**: Docker image security analysis
- **SAST**: Static application security testing

## 📊 Monitoring & Observability

- **Zero-code Observability**: Istio service mesh
- **Metrics**: Prometheus integration
- **Tracing**: Jaeger distributed tracing
- **Dashboards**: Grafana visualizations
- **Service Mesh**: Kiali topology visualization

## 🚀 Quick Start

```bash
# Clone repository
git clone <your-repo-url>

# Local development with Docker Compose
cd infrastructure/docker
docker-compose up -d

# Deploy to Kubernetes
kubectl apply -f infrastructure/k8s/
```

## 🎯 LSEG Developer Platform Skills Demonstrated

This project showcases skills required for LSEG Developer Platform Engineer role:

- ✅ **Infrastructure as Code**: Kubernetes manifests, Terraform modules
- ✅ **CI/CD Pipelines**: GitHub Actions with quality gates
- ✅ **Code Quality Scanning**: SonarCloud integration
- ✅ **Security Scanning**: Trivy vulnerability analysis  
- ✅ **Multi-language Proficiency**: Go, JavaScript/Node.js
- ✅ **Container Technologies**: Docker, Kubernetes
- ✅ **Service Mesh**: Istio for observability
- ✅ **Developer Experience**: Automated tooling and workflows

## 📈 Quality Metrics

Live quality metrics and security analysis available in:
- [SonarCloud Dashboard](https://sonarcloud.io/organizations/YOUR_ORG)
- [GitHub Security](../../security)
- [Actions Workflows](../../actions)

---

**Built for enterprise-grade development workflows** 🚀# Updated SonarCloud token
