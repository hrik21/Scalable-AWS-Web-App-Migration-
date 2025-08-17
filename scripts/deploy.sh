#!/bin/bash

# Deployment script for AWS PHP Data Platform
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-dev}
TERRAFORM_DIR="terraform"
APP_DIR="app"

echo -e "${GREEN}Starting deployment for environment: ${ENVIRONMENT}${NC}"

# Function to print status
print_status() {
    echo -e "${YELLOW}[INFO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

# Check prerequisites
print_status "Checking prerequisites..."

if ! command -v terraform &> /dev/null; then
    print_error "Terraform is not installed"
    exit 1
fi

if ! command -v aws &> /dev/null; then
    print_error "AWS CLI is not installed"
    exit 1
fi

if ! aws sts get-caller-identity &> /dev/null; then
    print_error "AWS credentials not configured"
    exit 1
fi

print_success "Prerequisites check passed"

# Initialize Terraform
print_status "Initializing Terraform..."
cd $TERRAFORM_DIR

if [ ! -f "terraform.tfvars" ]; then
    print_error "terraform.tfvars not found. Copy from terraform.tfvars.example and configure."
    exit 1
fi

terraform init
print_success "Terraform initialized"

# Plan Terraform changes
print_status "Planning Terraform changes..."
terraform plan -var="environment=${ENVIRONMENT}" -out=tfplan
print_success "Terraform plan completed"

# Apply Terraform changes
print_status "Applying Terraform changes..."
read -p "Do you want to apply these changes? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    terraform apply tfplan
    print_success "Terraform apply completed"
else
    print_status "Deployment cancelled"
    exit 0
fi

# Get outputs
print_status "Getting deployment outputs..."
ALB_DNS=$(terraform output -raw alb_dns_name 2>/dev/null || echo "Not available yet")
VPC_ID=$(terraform output -raw vpc_id 2>/dev/null || echo "Not available yet")

print_success "Deployment completed successfully!"
echo -e "${GREEN}Application URL: http://${ALB_DNS}${NC}"
echo -e "${GREEN}VPC ID: ${VPC_ID}${NC}"

cd ..
print_status "Deployment script finished"