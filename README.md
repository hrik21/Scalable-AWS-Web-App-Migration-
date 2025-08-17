# AWS PHP Data Platform

A secure, scalable, and cost-efficient PHP-based application infrastructure on AWS featuring automated data ingestion and processing capabilities across multiple Availability Zones.

## 🏗️ Architecture Overview

This project implements a re-architected PHP application with MySQL database using AWS managed services:

- **Aurora Serverless v2**: Auto-scaling MySQL-compatible database
- **EC2 Auto Scaling**: Automatic instance scaling based on demand
- **Application Load Balancer**: Traffic distribution and SSL termination
- **VPC with Private Subnets**: Secure network isolation
- **Secrets Manager**: Automated credential management
- **Multi-AZ Deployment**: High availability and fault tolerance

## 🚀 Key Features

- **Auto-scaling Infrastructure**: Automatically scales from 2 to 10 EC2 instances based on CPU utilization
- **Serverless Database**: Aurora Serverless scales from 0.5 to 16 ACUs based on demand
- **High Availability**: Multi-AZ deployment with automatic failover
- **Security First**: Private subnets, Security Groups, and encrypted connections
- **Cost Optimization**: Pay-per-use Aurora Serverless and intelligent auto-scaling
- **Automated Data Processing**: Built-in data ingestion and processing workflows
- **Comprehensive Monitoring**: CloudWatch metrics, alarms, and SNS notifications

## 📁 Project Structure

```
aws-php-data-platform/
├── .kiro/specs/aws-php-data-platform/
│   ├── requirements.md          # Detailed requirements and acceptance criteria
│   ├── design.md               # Architecture and technical design
│   └── tasks.md                # Implementation task breakdown
├── terraform/
│   ├── modules/
│   │   ├── vpc/                # VPC and networking resources
│   │   ├── security/           # Security Groups and IAM roles
│   │   ├── database/           # Aurora Serverless configuration
│   │   ├── compute/            # EC2 and Auto Scaling setup
│   │   └── load-balancer/      # ALB configuration
│   ├── environments/
│   │   ├── dev/                # Development environment
│   │   ├── staging/            # Staging environment
│   │   └── prod/               # Production environment
│   └── main.tf                 # Root Terraform configuration
├── src/
│   ├── app/
│   │   ├── Controllers/        # API controllers
│   │   ├── Models/             # Data models
│   │   ├── Services/           # Business logic services
│   │   └── Middleware/         # Request middleware
│   ├── config/
│   │   ├── database.php        # Database configuration
│   │   ├── secrets.php         # Secrets Manager integration
│   │   └── app.php             # Application configuration
│   ├── public/
│   │   ├── index.php           # Application entry point
│   │   └── health.php          # Health check endpoint
│   └── tests/
│       ├── Unit/               # Unit tests
│       ├── Integration/        # Integration tests
│       └── E2E/                # End-to-end tests
├── scripts/
│   ├── deploy.sh               # Deployment script
│   ├── build-ami.sh            # Custom AMI creation
│   └── database-migrate.sh     # Database migration script
├── docker/
│   ├── Dockerfile              # Application container
│   └── docker-compose.yml      # Local development setup
└── README.md                   # This file
```

## 🛠️ Prerequisites

- AWS CLI configured with appropriate permissions
- Terraform >= 1.0
- PHP >= 8.1
- Docker (for local development)
- Node.js (for build tools)

## 🚀 Quick Start

### 1. Clone and Setup

```bash
git clone <your-repo-url>
cd aws-php-data-platform
```

### 2. Configure AWS Credentials

```bash
aws configure
# Enter your AWS Access Key ID, Secret Access Key, and region
```

### 3. Initialize Terraform

```bash
cd terraform
terraform init
```

### 4. Deploy Infrastructure

```bash
# Deploy to development environment
cd environments/dev
terraform plan
terraform apply
```

### 5. Deploy Application

```bash
# Build and deploy the PHP application
./scripts/deploy.sh dev
```

## 🏗️ Infrastructure Components

### Networking
- **VPC**: Custom VPC spanning 2 Availability Zones
- **Public Subnets**: Host Application Load Balancer
- **Private Subnets**: Host EC2 instances and Aurora cluster
- **Security Groups**: Least-privilege network access control

### Compute
- **EC2 Instances**: t3.medium instances with PHP 8.1 and Nginx
- **Auto Scaling Group**: 2-10 instances based on CPU utilization
- **Launch Template**: Automated instance configuration and bootstrapping

### Database
- **Aurora Serverless v2**: MySQL 8.0 compatible with automatic scaling
- **Multi-AZ**: Automatic failover and high availability
- **Encryption**: At-rest and in-transit encryption enabled

### Load Balancing
- **Application Load Balancer**: Internet-facing with SSL termination
- **Target Groups**: Health check monitoring and traffic distribution
- **SSL/TLS**: Automatic certificate management with ACM

## 📊 Monitoring and Alerting

### CloudWatch Metrics
- CPU utilization and memory usage
- Database connections and query performance
- Application response times and error rates
- Custom business metrics

### Alarms and Notifications
- Auto-scaling triggers (CPU > 80% or < 30%)
- Database connection limits
- Application error rates
- Cost threshold alerts

## 🔒 Security Features

### Network Security
- Private subnets for application and database tiers
- Security Groups with minimal required access
- VPC Flow Logs for network monitoring
- NACLs for additional network-level protection

### Data Security
- AWS Secrets Manager for credential management
- Encryption at rest and in transit
- IAM roles with least privilege access
- Automated credential rotation

### Application Security
- Input validation and sanitization
- SQL injection prevention
- HTTPS enforcement
- Security headers implementation

## 💰 Cost Optimization

### Aurora Serverless Benefits
- Pay only for database capacity used
- Automatic scaling down during idle periods
- No need to provision database instances

### Auto Scaling Benefits
- Automatic instance termination during low traffic
- Right-sizing based on actual demand
- Spot instance integration for cost savings

### Monitoring and Alerts
- Cost budgets and threshold alerts
- Resource utilization monitoring
- Automated cost optimization recommendations

## 🧪 Testing

### Run Unit Tests
```bash
cd src
composer test
```

### Run Integration Tests
```bash
./scripts/test-integration.sh
```

### Load Testing
```bash
./scripts/load-test.sh
```

## 📈 Scaling and Performance

### Automatic Scaling Triggers
- **Scale Out**: CPU > 80% for 2 consecutive periods
- **Scale In**: CPU < 30% for 5 consecutive periods
- **Database**: Aurora Serverless scales automatically based on load

### Performance Optimization
- Connection pooling for database efficiency
- Nginx caching for static content
- CloudFront CDN integration (optional)
- Application-level caching with Redis (optional)

## 🔧 Maintenance and Operations

### Deployment
- Blue-green deployment for zero downtime
- Automated rollback capabilities
- Database migration scripts
- Configuration management

### Monitoring
- CloudWatch dashboards for real-time metrics
- Log aggregation and analysis
- Performance monitoring and alerting
- Health check endpoints

### Backup and Recovery
- Automated Aurora backups (7-day retention)
- Point-in-time recovery capabilities
- Cross-region backup replication (optional)
- Disaster recovery procedures

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Check the [troubleshooting guide](docs/troubleshooting.md)
- Review the [FAQ](docs/faq.md)
- Open an issue in this repository
- Contact the development team

## 🔄 Roadmap

- [ ] Container orchestration with ECS/EKS
- [ ] Advanced monitoring with X-Ray tracing
- [ ] Multi-region deployment
- [ ] CI/CD pipeline integration
- [ ] Advanced security scanning
- [ ] Performance optimization features

---

**Built with ❤️ for scalable, secure, and cost-efficient cloud infrastructure**