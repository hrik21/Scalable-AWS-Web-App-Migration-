# Deployment Guide

This guide covers the deployment process for the AWS PHP Data Platform.

## Prerequisites

Before deploying, ensure you have:

1. **AWS CLI configured** with appropriate permissions
2. **Terraform >= 1.0** installed
3. **PHP >= 8.1** and Composer installed
4. **S3 bucket** for Terraform state storage
5. **DynamoDB table** for state locking (optional but recommended)

## Required AWS Permissions

Your AWS user/role needs the following permissions:
- EC2 full access
- VPC full access
- RDS full access
- IAM role creation and management
- Secrets Manager access
- CloudWatch access
- Application Load Balancer access

## Step-by-Step Deployment

### 1. Initial Setup

```bash
# Clone the repository
git clone <repository-url>
cd aws-php-data-platform

# Run setup script
./scripts/setup.sh
```

### 2. Configure Backend State Storage

Create an S3 bucket for Terraform state:

```bash
aws s3 mb s3://your-terraform-state-bucket --region us-east-1
```

Create DynamoDB table for state locking:

```bash
aws dynamodb create-table \
    --table-name terraform-state-lock \
    --attribute-definitions AttributeName=LockID,AttributeType=S \
    --key-schema AttributeName=LockID,KeyType=HASH \
    --provisioned-throughput ReadCapacityUnits=5,WriteCapacityUnits=5 \
    --region us-east-1
```

### 3. Configure Terraform Backend

Update `terraform/backend.tf`:

```hcl
terraform {
  backend "s3" {
    bucket         = "your-terraform-state-bucket"
    key            = "aws-php-data-platform/terraform.tfstate"
    region         = "us-east-1"
    encrypt        = true
    dynamodb_table = "terraform-state-lock"
  }
}
```

### 4. Configure Variables

Update `terraform/terraform.tfvars`:

```hcl
aws_region = "us-east-1"
environment = "dev"
project_name = "aws-php-data-platform"

vpc_cidr = "10.0.0.0/16"
availability_zones = ["us-east-1a", "us-east-1b"]

instance_type = "t3.medium"
min_size = 2
max_size = 10
desired_capacity = 2
```

### 5. Configure Application Environment

Update `app/.env`:

```bash
APP_ENV=production
APP_DEBUG=false
DB_HOST=your-aurora-endpoint
DB_NAME=aws_php_platform
AWS_REGION=us-east-1
SECRETS_MANAGER_SECRET_NAME=aurora-credentials
```

### 6. Deploy Infrastructure

```bash
# Initialize Terraform
cd terraform
terraform init

# Plan deployment
terraform plan

# Apply changes
terraform apply
```

### 7. Deploy Application

```bash
# Run deployment script
./scripts/deploy.sh dev
```

## Environment-Specific Deployments

### Development Environment

```bash
./scripts/deploy.sh dev
```

Configuration:
- Smaller instance types
- Reduced auto-scaling limits
- Development database settings

### Staging Environment

```bash
./scripts/deploy.sh staging
```

Configuration:
- Production-like settings
- Reduced capacity for cost savings
- Full monitoring enabled

### Production Environment

```bash
./scripts/deploy.sh prod
```

Configuration:
- High availability settings
- Full auto-scaling capacity
- Enhanced monitoring and alerting
- Backup and disaster recovery

## Post-Deployment Verification

### 1. Check Infrastructure

```bash
# Verify Terraform outputs
terraform output

# Check AWS resources
aws ec2 describe-instances --filters "Name=tag:Project,Values=aws-php-data-platform"
aws rds describe-db-clusters --db-cluster-identifier aws-php-data-platform-aurora
```

### 2. Test Application

```bash
# Get ALB DNS name
ALB_DNS=$(terraform output -raw alb_dns_name)

# Test health endpoint
curl http://$ALB_DNS/health

# Test main application
curl http://$ALB_DNS/
```

### 3. Monitor Deployment

- Check CloudWatch metrics
- Verify Auto Scaling Group status
- Monitor Aurora Serverless scaling
- Review application logs

## Rollback Procedures

### Infrastructure Rollback

```bash
# Rollback to previous Terraform state
terraform plan -destroy
terraform apply -destroy

# Restore from backup if needed
terraform import <resource_type>.<resource_name> <resource_id>
```

### Application Rollback

```bash
# Deploy previous version
git checkout <previous-commit>
./scripts/deploy.sh <environment>
```

## Troubleshooting

### Common Issues

1. **Terraform State Lock**
   ```bash
   # Force unlock if needed
   terraform force-unlock <lock-id>
   ```

2. **Aurora Connection Issues**
   - Check Security Group rules
   - Verify Secrets Manager configuration
   - Check VPC routing

3. **Auto Scaling Issues**
   - Review CloudWatch alarms
   - Check instance health
   - Verify launch template configuration

4. **Load Balancer Issues**
   - Check target group health
   - Verify Security Group rules
   - Review ALB access logs

### Logs and Monitoring

- **Application Logs**: CloudWatch Logs `/aws/ec2/php-app`
- **Infrastructure Logs**: CloudTrail for API calls
- **Performance Metrics**: CloudWatch dashboards
- **Alerts**: SNS notifications for critical issues

## Maintenance

### Regular Tasks

1. **Update Dependencies**
   ```bash
   cd app
   composer update
   ```

2. **Terraform Updates**
   ```bash
   terraform plan
   terraform apply
   ```

3. **Security Updates**
   - Update AMI with latest patches
   - Review Security Group rules
   - Rotate secrets in Secrets Manager

4. **Cost Optimization**
   - Review CloudWatch cost metrics
   - Optimize instance types
   - Adjust auto-scaling policies

### Backup and Recovery

- **Database**: Automated Aurora backups (7-day retention)
- **Application**: Code stored in Git repository
- **Infrastructure**: Terraform state in S3 with versioning
- **Configuration**: Environment files backed up securely

## Security Considerations

1. **Network Security**
   - All application servers in private subnets
   - Minimal Security Group rules
   - VPC Flow Logs enabled

2. **Data Security**
   - Encryption at rest and in transit
   - Secrets Manager for credentials
   - IAM roles with least privilege

3. **Access Control**
   - MFA required for AWS console access
   - Separate IAM roles for different environments
   - Regular access reviews

## Performance Optimization

1. **Database Performance**
   - Monitor Aurora Serverless scaling
   - Optimize queries and indexes
   - Use connection pooling

2. **Application Performance**
   - Enable PHP OPcache
   - Implement application caching
   - Optimize Nginx configuration

3. **Infrastructure Performance**
   - Monitor CloudWatch metrics
   - Adjust auto-scaling policies
   - Use appropriate instance types

## Cost Management

1. **Monitor Costs**
   - Set up billing alerts
   - Review AWS Cost Explorer
   - Track resource utilization

2. **Optimize Resources**
   - Use Aurora Serverless scaling
   - Implement auto-scaling policies
   - Consider Reserved Instances for stable workloads

3. **Regular Reviews**
   - Monthly cost reviews
   - Resource utilization analysis
   - Optimization recommendations