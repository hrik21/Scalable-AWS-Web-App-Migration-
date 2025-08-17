# Module declarations for AWS PHP Data Platform

# Networking Module
module "networking" {
  source = "./modules/networking"
  
  project_name       = var.project_name
  vpc_cidr          = var.vpc_cidr
  availability_zones = var.availability_zones
}

# Security Module (placeholder for future implementation)
# module "security" {
#   source = "./modules/security"
#   
#   project_name = var.project_name
#   vpc_id       = module.networking.vpc_id
# }

# Database Module (placeholder for future implementation)
# module "database" {
#   source = "./modules/database"
#   
#   project_name        = var.project_name
#   vpc_id             = module.networking.vpc_id
#   private_subnet_ids = module.networking.private_subnet_ids
#   security_group_ids = [module.security.aurora_security_group_id]
# }

# Compute Module (placeholder for future implementation)
# module "compute" {
#   source = "./modules/compute"
#   
#   project_name        = var.project_name
#   vpc_id             = module.networking.vpc_id
#   private_subnet_ids = module.networking.private_subnet_ids
#   instance_type      = var.instance_type
#   min_size           = var.min_size
#   max_size           = var.max_size
#   desired_capacity   = var.desired_capacity
# }

# Load Balancer Module (placeholder for future implementation)
# module "load_balancer" {
#   source = "./modules/load_balancer"
#   
#   project_name       = var.project_name
#   vpc_id            = module.networking.vpc_id
#   public_subnet_ids = module.networking.public_subnet_ids
#   target_group_arn  = module.compute.target_group_arn
# }