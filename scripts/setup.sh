#!/bin/bash

# Setup script for AWS PHP Data Platform development environment
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${YELLOW}[INFO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

echo -e "${GREEN}Setting up AWS PHP Data Platform development environment${NC}"

# Check if running on macOS
if [[ "$OSTYPE" == "darwin"* ]]; then
    print_status "Detected macOS environment"
    
    # Check for Homebrew
    if ! command -v brew &> /dev/null; then
        print_error "Homebrew is not installed. Please install it first: https://brew.sh/"
        exit 1
    fi
    
    # Install PHP if not present
    if ! command -v php &> /dev/null; then
        print_status "Installing PHP..."
        brew install php
    fi
    
    # Install Composer if not present
    if ! command -v composer &> /dev/null; then
        print_status "Installing Composer..."
        brew install composer
    fi
    
    # Install Terraform if not present
    if ! command -v terraform &> /dev/null; then
        print_status "Installing Terraform..."
        brew install terraform
    fi
    
    # Install AWS CLI if not present
    if ! command -v aws &> /dev/null; then
        print_status "Installing AWS CLI..."
        brew install awscli
    fi
fi

# Setup PHP application
print_status "Setting up PHP application..."
cd app

if [ ! -f "composer.json" ]; then
    print_error "composer.json not found in app directory"
    exit 1
fi

# Install PHP dependencies
print_status "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Copy environment file if it doesn't exist
if [ ! -f ".env" ]; then
    print_status "Creating .env file from example..."
    cp .env.example .env
    print_status "Please update .env file with your configuration"
fi

cd ..

# Setup Terraform configuration
print_status "Setting up Terraform configuration..."
cd terraform

# Copy backend configuration if it doesn't exist
if [ ! -f "backend.tf" ]; then
    print_status "Creating backend.tf from example..."
    cp ../config/backend.tf.example backend.tf
    print_status "Please update backend.tf with your S3 bucket configuration"
fi

# Copy terraform variables if they don't exist
if [ ! -f "terraform.tfvars" ]; then
    print_status "Creating terraform.tfvars from example..."
    cp ../config/terraform.tfvars.example terraform.tfvars
    print_status "Please update terraform.tfvars with your configuration"
fi

cd ..

print_success "Setup completed successfully!"
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Update app/.env with your database and AWS configuration"
echo "2. Update terraform/backend.tf with your S3 bucket for state storage"
echo "3. Update terraform/terraform.tfvars with your deployment configuration"
echo "4. Run './scripts/deploy.sh' to deploy the infrastructure"