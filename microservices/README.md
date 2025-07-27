# gRPC Microservices with Zero-Code Observability

This project demonstrates **zero-code observability** for gRPC microservices using **Istio service mesh** with automatic distributed tracing, metrics, and monitoring.

## 🏗️ Architecture

```
External HTTP → Kong Gateway → API Gateway → gRPC Services (Category/Product) → MongoDB
                    ↓              ↓                ↓                           ↓
               Istio Envoy    Istio Envoy    Istio Envoy               Istio Envoy
                    ↓              ↓                ↓                           ↓
                         Automatic Observability (Zero Code Changes)
                                        ↓
                    Prometheus + Grafana + Jaeger + Kiali
```

## 🚀 Services

- **Kong Gateway**: API Gateway with load balancing
- **API Gateway (Go)**: HTTP to gRPC translation service
- **Category Service (Node.js)**: gRPC service for category management
- **Product Service (Node.js)**: gRPC service for product management  
- **MongoDB**: Database for each service

## 📊 Observability Stack

- **Istio**: Service mesh with automatic sidecar injection
- **Jaeger**: Distributed tracing for gRPC calls
- **Prometheus**: Metrics collection
- **Grafana**: Metrics visualization and dashboards
- **Kiali**: Service mesh topology visualization
- **Access Logs**: Automatic request/response logging via Envoy

## 🎯 Zero-Code Features

✅ **Automatic gRPC Tracing** - No application code changes needed  
✅ **Request/Response Metrics** - Latency, throughput, error rates  
✅ **Access Logs** - Structured HTTP/gRPC request logging  
✅ **Service Topology** - Visual service dependencies  
✅ **Traffic Management** - Load balancing, circuit breaking  
✅ **Security** - mTLS between services  

## 🛠️ Prerequisites

- Kubernetes cluster (tested on DigitalOcean)
- `kubectl` configured
- `helm` installed
- `curl` for testing

## 📦 Installation

### 1. Deploy Core Services

```bash
# Create namespaces
kubectl apply -f k8s/namespace.yaml

# Deploy MongoDB databases
kubectl apply -f k8s/category/
kubectl apply -f k8s/product/

# Deploy API Gateway
kubectl apply -f k8s/controller/

# Install Kong Gateway
helm repo add kong https://charts.konghq.com
helm install kong kong/kong -n kong --create-namespace -f k8s/kong/values.yaml
```

### 2. Install Istio Service Mesh

```bash
# Download and install Istio
curl -L https://istio.io/downloadIstio | sh -
export PATH="$PATH:./istio-1.26.2/bin"

# Pre-installation check
istioctl x precheck

# Install Istio with demo profile
istioctl install --set profile=demo -y

# Enable sidecar injection for all namespaces
kubectl label namespace category-service istio-injection=enabled
kubectl label namespace product-service istio-injection=enabled  
kubectl label namespace controller-service istio-injection=enabled
kubectl label namespace kong istio-injection=enabled

# Restart deployments to inject sidecars
kubectl rollout restart deployment --all-namespaces
```

### 3. Install Observability Tools

```bash
cd istio-1.26.2

# Install Prometheus for metrics
kubectl apply -f samples/addons/prometheus.yaml

# Install Grafana for dashboards
kubectl apply -f samples/addons/grafana.yaml

# Install Jaeger for tracing
kubectl apply -f samples/addons/jaeger.yaml

# Install Kiali for service mesh visualization
kubectl apply -f samples/addons/kiali.yaml

# Configure telemetry for gRPC tracing
kubectl apply -f k8s/istio/telemetry.yaml

# Enable access logs
kubectl apply -f k8s/istio/access-logs.yaml
```

## 🔧 Configuration

### Kong Routes Configuration

```bash
# Get Kong Admin API endpoint
export KONG_ADMIN=$(kubectl get svc kong-kong-admin -n kong -o jsonpath='{.status.loadBalancer.ingress[0].ip}')

# Configure API routes (done automatically via Kong Ingress Controller)
```

### Istio Telemetry Configuration

```yaml
# k8s/istio/telemetry.yaml
apiVersion: telemetry.istio.io/v1
kind: Telemetry
metadata:
  name: grpc-tracing
  namespace: istio-system
spec:
  tracing:
  - providers:
    - name: jaeger
    randomSamplingPercentage: 100.0
```

## 🌐 Access Observability UIs

### Port Forward to Access UIs

```bash
# Jaeger (Distributed Tracing)
kubectl port-forward -n istio-system svc/tracing 16686:80
# Access: http://localhost:16686

# Grafana (Metrics & Dashboards) 
kubectl port-forward -n istio-system svc/grafana 3000:3000
# Access: http://localhost:3000

# Kiali (Service Mesh Topology)
kubectl port-forward -n istio-system svc/kiali 20001:20001  
# Access: http://localhost:20001

# Prometheus (Raw Metrics)
kubectl port-forward -n istio-system svc/prometheus 9090:9090
# Access: http://localhost:9090
```

## 📋 Viewing Logs

### Access Logs
```bash
# View HTTP/gRPC access logs from API Gateway
kubectl logs -n controller-service -l app=api-gateway -c istio-proxy

# View access logs from Category Service
kubectl logs -n category-service -l app=category-service -c istio-proxy

# View access logs from Product Service  
kubectl logs -n product-service -l app=product-service -c istio-proxy
```

**Log Format**: Each log entry includes timestamp, HTTP method, path, response code, latency, source/destination IPs, user agent, and gRPC status for gRPC calls.

## 🧪 Testing

### Generate Test Data

```bash
# Get Kong Proxy endpoint
export KONG_PROXY=$(kubectl get svc kong-kong-proxy -n kong -o jsonpath='{.status.loadBalancer.ingress[0].ip}')

# Create a category
curl -X POST "http://$KONG_PROXY:8000/api/categories" \
  -H "Content-Type: application/json" \
  -d '{"name": "Electronics", "description": "Electronic devices"}'

# Create a product  
curl -X POST "http://$KONG_PROXY:8000/api/products" \
  -H "Content-Type: application/json" \
  -d '{"name": "iPhone 15", "description": "Latest iPhone", "price": 999.99, "category_id": "CATEGORY_ID"}'
```

### Test gRPC Communication

```bash
# Get categories
curl "http://$KONG_PROXY:8000/api/categories"

# Get products by category
curl "http://$KONG_PROXY:8000/api/products/category/CATEGORY_ID"

# Generate continuous traffic for metrics
for i in {1..100}; do
  curl -s "http://$KONG_PROXY:8000/api/categories" > /dev/null
  sleep 1
done
```

## 📈 Grafana Dashboards

### Key PromQL Queries

#### Request Rate
```promql
# Requests per second to controller-service
sum(rate(istio_requests_total{destination_app="api-gateway"}[1m]))

# Request count per minute
increase(istio_requests_total{destination_app="api-gateway"}[1m])
```

#### Latency
```promql
# P95 latency for API Gateway
histogram_quantile(0.95, sum(rate(istio_request_duration_milliseconds_bucket{destination_app="api-gateway"}[1m])) by (le))
```

#### Error Rate
```promql
# Error rate percentage
sum(rate(istio_requests_total{destination_app="api-gateway", response_code=~"5.."}[1m])) / sum(rate(istio_requests_total{destination_app="api-gateway"}[1m])) * 100
```

#### Resource Usage
```promql
# CPU usage per service
sum(rate(container_cpu_usage_seconds_total{namespace="controller-service"}[5m])) by (pod)

# Memory usage per service
sum(container_memory_working_set_bytes{namespace="controller-service"}) by (pod)
```

## 🔍 What You'll See

### Jaeger Traces
- Full distributed traces across HTTP → gRPC calls
- gRPC method names and timing
- Service dependencies and call graphs
- Request flow visualization

### Grafana Metrics
- Request rates per service (req/s)
- Response latency percentiles (P50, P95, P99)
- Error rates and success rates
- Resource utilization (CPU, memory)

### Kiali Service Mesh
- Real-time service topology
- Traffic flow between services
- Health status indicators
- Security policy visualization

### Access Logs
- Structured request/response logs
- HTTP and gRPC call details
- Performance metrics per request
- Source/destination service tracking

## 🐛 Troubleshooting

### Check Pod Status
```bash
# Verify all pods have Istio sidecars (2/2 ready)
kubectl get pods --all-namespaces

# Check Istio injection
kubectl get namespace -L istio-injection
```

### Verify Metrics
```bash
# Check if metrics are being generated
kubectl exec -n istio-system deployment/prometheus -- \
  promtool query instant 'istio_requests_total'
```

### Debug Tracing
```bash
# Check Jaeger collector logs
kubectl logs -n istio-system deployment/jaeger

# Verify telemetry configuration
kubectl get telemetry -n istio-system
```

## 🔧 Customization

### Adjust Sampling Rate
```yaml
# Reduce sampling for production (in telemetry.yaml)
randomSamplingPercentage: 1.0  # 1% sampling
```

### Add Custom Metrics
```yaml
# Add custom telemetry configuration
metrics:
- providers:
  - name: prometheus
  overrides:
  - match:
      metric: REQUEST_COUNT
    tagOverrides:
      custom_label:
        value: custom_value
```

## 📚 Key Benefits

1. **Zero Code Changes**: No application instrumentation required
2. **Automatic Discovery**: Services are automatically discovered and monitored  
3. **gRPC Support**: Full visibility into gRPC method calls
4. **Production Ready**: Battle-tested Istio service mesh
5. **Rich Visualizations**: Multiple tools for different observability needs

## 🏷️ Tech Stack

- **Languages**: Go (API Gateway), Node.js (gRPC Services)
- **Protocols**: HTTP/REST, gRPC, Protocol Buffers
- **Infrastructure**: Kubernetes, Docker
- **Service Mesh**: Istio with Envoy Proxy
- **Observability**: Prometheus, Grafana, Jaeger, Kiali
- **Gateway**: Kong API Gateway
- **Database**: MongoDB

## 📄 License

---