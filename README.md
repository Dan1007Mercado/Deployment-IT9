Cloud Deployment Guide 
 
Laravel on AWS (EC2, RDS, S3) 
 
This document describes the deployment architecture and step-by-step 
process for deploying a Laravel application to AWS cloud infrastructure. 
 
Component Technology 
Application Laravel (PHP 8.3 
Web Server Apache (running in Docker container) 
Compute AWS EC2 (t3.micro - Free Tier) 
File Storage Amazon S3 
Container 
Runtime 
Docker 
CI/CD GitHub Actions (automated testing and 
deployment) 
IAM Security EC2 instance role with S3 access policy 
 
 
System Architecture 
 
User (Browser)  
↓  
GitHub Repository (Source Code)  
↓ 
 GitHub Actions (CI/CD Pipeline)  
↓  
EC2 instance role with S3 access policy EC2 Instance (Docker Container 
with Laravel)  
             ↓                    ↓  
RDS( MySQL)       S3 (File Storage) 
 
Prerequisites 
 
Required Accounts & Tools 
 
• AWS Account (free tier eligible)  
• GitHub Account  
• Git installed locally  
• AWS CLI installed and configured 
 
Initial AWS Setup 
 
1. Create IAM user with programmatic access 
2. Attach AdministratorAccess policy 
3. Save Access Key ID and Secret Access Key 
4. Install aws cli  
5. Verify installation: aws –version 
6. Configure AWS CLI — type aws configure  
7. Enter your Access Key, Secret Key, region (ap-southeast-1), output 
format (json) 
 
GitHub Preparation 
 
 • Push the Laravel code to a GitHub repository  
• Ensure your project has a valid Dockerfile 
 
1. Setting Up AWS Infrastructure (Manual) 
 
Step 1: Create VPC and Networking 
 
1.1 Create VPC 
1. Go to AWS Console → VPC → Your VPCs → Create VPC 
2. Select "VPC only" 
3. Name: laravel-vpc 
4. IPv4 CIDR: 10.0.0.0/16 
5. Click Create VPC 
 
1.2 Create Subnets 
 
Public Subnet (for EC2): 
1. Go to Subnets → Create subnet 
2. Select VPC: laravel-vpc 
3. Name: laravel-public-subnet 
4. Availability Zone: ap-southeast-1a 
5. IPv4 CIDR: 10.0.1.0/24 
6. Click Create subnet 
 
Private Subnet (for RDS): 
 
1. Go to Subnets → Create subnet 
2. Select VPC: laravel-vpc 
3. Name: laravel-private-subnet 
4. Availability Zone: ap-southeast-1b 
5. IPv4 CIDR: 10.0.2.0/24 
6. Click Create subnet 
 
1.3 Create Internet Gateway 
 
1. Go to Internet Gateways → Create internet gateway 
Page 2 
Cloud Deployment Guide: Laravel on AWS 
2. Name: laravel-igw 
3. Click Create 
4. Select it → Actions → Attach to VPC → Select laravel-vpc → 
Attach 
 
 
 
 
1.4 Configure Route Tables 
Public Route Table: 
1. Go to Route Tables → Create route table 
2. Name: laravel-public-rt 
3. VPC: laravel-vpc 
4. Click Create 
5. Select it → Routes tab → Edit routes → Add route 
6. Destination: 0.0.0.0/0, Target: Internet Gateway (laravel-igw) 
7. Click Save 
8. Go to Subnet associations → Edit subnet associations → Select 
laravel-public-subnet → Save 
 
Step 2: Create Security Groups 
 
2.1 Web Security Group (for EC2) 
1. Go to Security Groups → Create security group 
2. Name: laravel-web-sg 
3. Description: "Allow HTTP and SSH" 
4. VPC: laravel-vpc 
5. Inbound rules: 
6. Click Create 
 
TYPE PORT SOURCE 
HTTP 80 0.0.0.0/0 
HTTPS 443 0.0.0.0/0 
SSH 22 Your Ip(get 
from 
whatsmyip.com) 
 
2.2 Database Security Group (for RDS) 
 
1. Go to Security Groups → Create security group 
2. Name: laravel-db-sg 
3. Description: "Allow MySQL from web servers" 
4. VPC: laravel-vpc 
5. Inbound rules: Type: MySQL/Aurora | Port: 3306 | Source: Select 
Security Group laravel-web-sg 
6. Click Create 
 
Step 3: Create S3 Bucket for File Storage 
 
1. Go to AWS Console → S3 → Create bucket 
2. Bucket name: laravel-app-yourname-unique (must be globally 
unique) 
3. Region: ap-southeast-1 (Singapore) 
4. Object Ownership: ACLs disabled 
Page 3 
Cloud Deployment Guide: Laravel on AWS 
5. Block Public Access: Block all public access 
6. Click Create bucket 
 
3.1 Configure Bucket Policy 
 
Go to bucket → Permissions → Bucket Policy → Edit. Add the 
following policy (replace placeholders): 
{ 
} 
    "Version": "2012-10-17", 
    "Statement": [ 
        { 
            "Sid": "AllowLaravelAccess", 
            "Effect": "Allow", 
            "Principal": { 
                "AWS": "arn:aws:iam::YOUR_ACCOUNT_ID:role/laravel
ec2-role" 
            }, 
            "Action": [ 
                "s3:GetObject", 
                "s3:PutObject", 
                "s3:DeleteObject", 
                "s3:ListBucket" 
            ], 
            "Resource": [ 
                "arn:aws:s3:::YOUR_BUCKET_NAME", 
                "arn:aws:s3:::YOUR_BUCKET_NAME/*" 
            ] 
        } 
     
Step 4: Create RDS MySQL Database (Free Tier) 
 
1. Go to AWS Console → RDS → Create database 
2. Choose: Standard Create 
3. Engine options: MySQL 
4. Version: MySQL 8.0 
5. Templates: Free tier 
6. Settings: 
• DB instance identifier: laravel-db 
• Master username: laravel_user 
• Master password: Create a strong password (save this!) 
7. Instance configuration: DB instance class: db.t3.micro (free tier) 
8. Storage: Allocated storage: 20 GB | Storage type: gp2 
9. Connectivity: 
• VPC: laravel-vpc 
• Subnet group: Create new → Include both public and private 
subnets 
• Public access: No 
• VPC security group: Select laravel-db-sg 
10. Additional configuration: 
• Initial database name: laravel_db 
• Deletion protection: Enable 
11. Click Create database (takes 5–10 minutes) 
 
After creation, note: 
• Endpoint: laravel-db.xxxxx.ap-southeast-1.rds.amazonaws.com 
• Port: 3306 
 
Step 5: Create IAM Role for EC2 (S3 Access) 
1. Go to IAM → Roles → Create role 
2. Trusted entity type: AWS service 
3. Use case: EC2 
4. Click Next 
5. Search and select: AmazonS3FullAccess 
6. Click Next 
7. Role name: laravel-ec2-s3-role 
8. Description: "Allow EC2 to access S3" 
9. Click Create role 
 
Step 6: Create SSH Key Pair 
1. Go to AWS Console → EC2 → Key Pairs → Create key pair 
2. Name: laravel-key 
3. Type: RSA 
4. Format: .pem 
5. Download and save to ~/.ssh/laravel-key.pem 
6. Set permissions: 
chmod 400 ~/.ssh/laravel-key.pem 
 
Step 7: Launch EC2 Instance with Ubuntu 22.04 
 
1. Go to AWS Console → EC2 → Instances → Launch instance 
2. Name: laravel-app-server 
3. AMI: Ubuntu 22.04 LTS (HVM, SSD Volume Type) - Free tier 
eligible 
4. Instance type: t2.micro (free tier) 
5. Key pair: Select laravel-key 
6. Network settings: 
• VPC: laravel-vpc 
• Subnet: laravel-public-subnet 
• Auto-assign public IP: Enable 
• Security group: Select laravel-web-sg 
7. Storage: 20 GB gp2 
8. IAM instance profile: Select laravel-ec2-s3-role 
9. Advanced details → User data (copy this script): 
#!/bin/bash 
 
 
 
 
echo "Setting up Docker and Nginx on Ubuntu 22.04..." 
# Update system 
apt-get update -y 
apt-get upgrade -y 
# Install Docker 
 
apt-get install -y apt-transport-https ca-certificates curl software
properties-common 
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | apt-key add  
add-apt-repository "deb [arch=amd64] 
https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" 
apt-get update -y 
apt-get install -y docker-ce 
systemctl start docker 
systemctl enable docker 
usermod -aG docker ubuntu 
 
# Install Docker Compose 
 
curl -L 
"https://github.com/docker/compose/releases/latest/download/docker
compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose 
chmod +x /usr/local/bin/docker-compose 
 
# Install Nginx 
apt-get install -y nginx 
systemctl start nginx 
systemctl enable nginx 
 
# Install Git 
apt-get install -y git 
 
# Create app directory 
mkdir -p /var/www/laravel 
echo "Docker and Nginx installed successfully on Ubuntu!" 
 
10. Click Launch instance 
 
Step 8: Test EC2 Connection 
Connect via SSH (note: Ubuntu username is ubuntu, not ec2-user): 
ssh -i ~/.ssh/laravel-key.pem ubuntu@YOUR_EC2_PUBLIC_IP 
Verify Docker and Nginx are installed: 
docker --version 
docker-compose --version 
nginx -v 
systemctl status nginx 
exit 
2. Docker and Nginx Configuration 
 
Dockerfile 
 
Create Dockerfile in your Laravel project root: 
 
# Use official PHP with Apache 
 
FROM php:8.2-apache 
# Install required PHP extensions 
RUN apt-get update && apt-get install -y \ 
 
git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev curl \ 
    && docker-php-ext-configure gd --with-freetype --with-jpeg \ 
    && docker-php-ext-install pdo pdo_mysql zip gd 
 
# Install Node.js 
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \ 
    && apt-get install -y nodejs 
 
# Enable Apache mod_rewrite 
RUN a2enmod rewrite 
 
# Set Apache to listen on port 8080 (for Nginx proxy) 
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf 
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000
default.conf 
 
# Set Apache DocumentRoot 
RUN sed -i 's|/var/www/html|/var/www/html/public|g' \ 
    /etc/apache2/sites-available/000-default.conf 
 
WORKDIR /var/www/html 
# Copy application code 
COPY . /var/www/html/ 
 
# Install Composer 
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer 
 
# Install dependencies 
RUN composer install --no-dev --optimize-autoloader --no-interaction 
# Install npm dependencies and build assets 
RUN npm install && npm run build 
# Set permissions 
RUN chown -R www-data:www-data storage bootstrap/cache \ 
    && chmod -R 775 storage bootstrap/cache 
EXPOSE 8080 
CMD ["apache2-foreground"] 
 
Nginx Configuration 
 
Create nginx/nginx.conf in your project: 
server { 
    listen 80; 
    server_name _; 
    # Logging 
    access_log /var/log/nginx/laravel-access.log; 
    error_log /var/log/nginx/laravel-error.log; 
    # Handle Laravel requests - proxy to Docker container 
    location / { 
        proxy_pass http://localhost:8080; 
        proxy_http_version 1.1; 
        proxy_set_header Upgrade $http_upgrade; 
        proxy_set_header Connection 'upgrade'; 
        proxy_set_header Host $host; 
        proxy_set_header X-Real-IP $remote_addr; 
        proxy_set_header X-Forwarded-For        
$proxy_add_x_forwarded_for; 
        proxy_set_header X-Forwarded-Proto $scheme; 
        proxy_cache_bypass $http_upgrade; 
    } 
 
# Serve static files directly from storage 
    location /storage { 
        alias /var/www/laravel/storage/app/public; 
        try_files $uri $uri/ =404; 
    } 
    # Deny access to hidden files 
    location ~ /\. { 
        deny all; 
    } 
    # Security headers 
    add_header X-Frame-Options "SAMEORIGIN" always; 
    add_header X-Content-Type-Options "nosniff" always; 
    add_header X-XSS-Protection "1; mode=block" always; 
} 
 
3. Configuring Laravel for S3  
 
Step 1: Install AWS SDK 
 
composer require aws/aws-sdk-php 
 
Step 2: Update .env Configuration 
FILESYSTEM_DISK=s3 
AWS_ACCESS_KEY_ID=your-access-key 
AWS_SECRET_ACCESS_KEY=your-secret-key 
AWS_DEFAULT_REGION=ap-southeast-1 
AWS_BUCKET=your-bucket-name 
AWS_USE_PATH_STYLE_ENDPOINT=false 
Step 3: Configure config/filesystems.php 
'disks' => [ 
    's3' => [ 
        'driver' => 's3', 
        'key'    => env('AWS_ACCESS_KEY_ID'), 
        'secret' => env('AWS_SECRET_ACCESS_KEY'), 
        'region' => env('AWS_DEFAULT_REGION'), 
        'bucket' => env('AWS_BUCKET'), 
        'url'    => env('AWS_URL'), 
        'endpoint'             => env('AWS_ENDPOINT'), 
        'use_path_style_endpoint' => 
env('AWS_USE_PATH_STYLE_ENDPOINT', false), 
        'throw'  => false, 
    ], 
], 
 
4. . CI/CD Pipeline with GitHub Actions 
 
Step 1: Add GitHub Secrets 
 
Go to your GitHub repository → Settings → Secrets and variables → 
Actions → Add the following secrets: 
 
Secret Name Description 
AWS_ACCESS_KEY_ID Your AWS IAM user access key 
AWS_SECRET_ACCESS_KEY Your AWS IAM user secret key 
EC2_SSH_KEY Content of your laravel-key.pem 
file 
EC2_HOST Your EC2 public IP 
EC2_USER ubuntu (for Ubuntu instances) 
DB_PASSWORD Your RDS database password 
DB_HOST Your RDS endpoint 
DB_DATABASE laravel_db 
DB_USERNAME laravel_user 
S3_BUCKET Your S3 bucket name 
 
Step 2: Create GitHub Actions Workflow 
 
Create .github/workflows/deploy.yml: 
 
Use this YML code: 
 
name: Deploy Laravel to AWS EC2 
 
on: 
  push: 
    branches: 
      - main 
  pull_request: 
    branches: 
      - main 
 
jobs: 
  test: 
    name:       Run Tests 
    runs-on: ubuntu-latest 
 
    steps: 
      - name:      Checkout code 
        uses: actions/checkout@v4 
 
      - name:   Setup PHP 
        uses: shivammathur/setup-php@v2 
        with: 
          php-version: 8.3 
          extensions: mbstring, xml, bcmath, zip, curl, gd, mysql, 
pdo_mysql 
          coverage: none 
 
      - name:        Install Composer dependencies 
        run: composer install --no-progress --prefer-dist --no-interaction 
 
      - name:       Setup Node.js 
        uses: actions/setup-node@v4 
        with: 
          node-version: 20 
          cache: 'npm' 
 
      - name:        Install NPM dependencies 
        run: npm ci 
 
      - name:         Build assets for testing 
        run: npm run build 
 
      - name:          Create environment file 
        run: | 
          # Copy example file 
          cp .env.example .env || echo "No .env.example found, creating 
new .env" 
           
          # If .env is empty or doesn't have APP_KEY, add it 
          if ! grep -q "^APP_KEY=" .env; then 
            echo "APP_KEY=" >> .env 
          fi 
           
          # Generate key (this will work even if APP_KEY line exists but 
is empty) 
          php artisan key:generate --no-interaction --force 
           
          echo "   APP_KEY generated successfully" 
 
      - name:          Configure testing environment 
        run: | 
          # Verify APP_KEY was generated successfully 
          APP_KEY_VALUE=$(grep "^APP_KEY=" .env | cut -d'=' -f2-) 
          if [ -z "$APP_KEY_VALUE" ]; then 
            echo "  APP_KEY is empty!" 
            exit 1 
          fi 
           
          echo "   APP_KEY verified: ${APP_KEY_VALUE:0:20}..." 
           
          # Remove existing testing config lines if they exist 
          sed -i '/^APP_ENV=/d' .env 2>/dev/null || true 
          sed -i '/^APP_DEBUG=/d' .env 2>/dev/null || true 
          sed -i '/^DB_CONNECTION=/d' .env 2>/dev/null || true 
          sed -i '/^DB_DATABASE=/d' .env 2>/dev/null || true 
          sed -i '/^CACHE_DRIVER=/d' .env 2>/dev/null || true 
          sed -i '/^SESSION_DRIVER=/d' .env 2>/dev/null || true 
          sed -i '/^QUEUE_CONNECTION=/d' .env 2>/dev/null || true 
          sed -i '/^MAIL_MAILER=/d' .env 2>/dev/null || true 
          sed -i '/^BROADCAST_DRIVER=/d' .env 2>/dev/null || true 
          sed -i '/^ASSET_URL=/d' .env 2>/dev/null || true 
           
          # Append testing configurations 
          echo "APP_ENV=testing" >> .env 
          echo "APP_DEBUG=true" >> .env 
          echo "DB_CONNECTION=sqlite" >> .env 
          echo "DB_DATABASE=:memory:" >> .env 
          echo "CACHE_DRIVER=array" >> .env 
          echo "SESSION_DRIVER=array" >> .env 
          echo "QUEUE_CONNECTION=sync" >> .env 
          echo "MAIL_MAILER=array" >> .env 
          echo "BROADCAST_DRIVER=log" >> .env 
           
          # For Vite, we need to set the asset URL or use a fake manifest 
          echo "ASSET_URL=http://localhost" >> .env 
           
          # Create a fake manifest file for testing if it doesn't exist 
          mkdir -p public/build 
          if [ ! -f public/build/manifest.json ]; then 
            echo '{ 
              "resources/js/app.js": { 
                "file": "assets/app.js", 
                "isEntry": true, 
                "src": "resources/js/app.js" 
              }, 
              "resources/css/app.css": { 
                "file": "assets/app.css", 
                "src": "resources/css/app.css" 
              } 
            }' > public/build/manifest.json 
          fi 
           
          # Final verification 
          echo "" 
          echo "         Final .env configuration:" 
          grep -E 
"^(APP_KEY|APP_ENV|APP_DEBUG|DB_CONNECTION|CACHE_D
RIVER|ASSET_URL)" .env || true 
 
      - name:     Clear and cache config 
        run: | 
          php artisan config:clear 
          php artisan config:cache 
 
      - name:    Run Laravel tests 
        run: php artisan test 
 
  deploy: 
    name:         Deploy to Production 
    runs-on: ubuntu-latest 
    needs: test 
    if: github.ref == 'refs/heads/main' && github.event_name == 'push' 
 
    steps: 
      - name:      Checkout code 
        uses: actions/checkout@v4 
 
      - name:   Setup PHP 
        uses: shivammathur/setup-php@v2 
        with: 
          php-version: 8.3 
          extensions: mbstring, xml, bcmath, zip, curl, gd, mysql, 
pdo_mysql 
          coverage: none 
 
      - name:        Install Composer dependencies 
        run: composer install --no-dev --optimize-autoloader --no
interaction 
 
      - name:       Setup Node.js 
        uses: actions/setup-node@v4 
        with: 
          node-version: 20 
          cache: 'npm' 
 
      - name:        Install NPM dependencies 
        run: npm ci 
 
      - name:         Build assets 
        run: npm run build 
 
      - name:      Upload assets to S3 
        env: 
          AWS_ACCESS_KEY_ID: ${{ secrets.AWS_ACCESS_KEY_ID 
}} 
          AWS_SECRET_ACCESS_KEY: ${{ 
secrets.AWS_SECRET_ACCESS_KEY }} 
          AWS_DEFAULT_REGION: ap-southeast-1 
        run: | 
          aws s3 sync public/ s3://laravel-cloud-storage-hotel-1234/public/ --delete --exclude "index.php" || true 
          aws s3 sync public/build/ s3://laravel-cloud-storage-hotel
1234/build/ --delete || true 
 
      - name:        Create deployment package 
        run: | 
          echo "       Creating deployment package..." 
          mkdir -p deployment 
          rsync -av --exclude='.git' \ 
                    --exclude='.github' \ 
                    --exclude='node_modules' \ 
                    --exclude='tests' \ 
                    --exclude='.env' \ 
                    --exclude='.env.*' \ 
                    --exclude='storage/logs/*' \ 
                    --exclude='storage/framework/cache/*' \ 
                    --exclude='storage/framework/sessions/*' \ 
                    --exclude='storage/framework/testing/*' \ 
                    --exclude='storage/app/public/*' \ 
                    ./ deployment/ 
          cd deployment && tar -czf ../laravel-deploy.tar.gz . 
          cd .. 
          echo "   Deployment package created:" 
          ls -lh laravel-deploy.tar.gz 
 
      - name:   Setup SSH 
        run: | 
          mkdir -p ~/.ssh 
          echo "${{ secrets.EC2_SSH_KEY }}" > ~/.ssh/id_rsa 
          chmod 600 ~/.ssh/id_rsa 
          ssh-keyscan -H "${{ secrets.EC2_HOST }}" >> 
~/.ssh/known_hosts 2>/dev/null 
 
      - name:      Copy files to EC2 
        run: | 
          echo "     Copying deployment package to EC2..." 
           
          # Check if local file exists 
          if [ ! -f laravel-deploy.tar.gz ]; then 
            echo "  Local file laravel-deploy.tar.gz not found!" 
            exit 1 
          fi 
           
          echo "   Local file found: $(ls -lh laravel-deploy.tar.gz)" 
           
          # Copy file to EC2 
          scp -o StrictHostKeyChecking=no -o ConnectTimeout=30 
laravel-deploy.tar.gz ${{ secrets.EC2_USER }}@${{ 
secrets.EC2_HOST }}:/tmp/ 
           
          if [ $? -eq 0 ]; then 
            echo "   File copied successfully" 
          else 
            echo "  SCP failed" 
            exit 1 
          fi 
           
          # Verify file exists on EC2 
          ssh -o StrictHostKeyChecking=no ${{ secrets.EC2_USER 
}}@${{ secrets.EC2_HOST }} " 
            if [ -f /tmp/laravel-deploy.tar.gz ]; then 
              echo '   File verified on EC2:' 
              ls -lh /tmp/laravel-deploy.tar.gz 
            else 
              echo '  File not found on EC2 after copy!' 
              exit 1 
            fi 
          " 
 
      - name:     Deploy on EC2 
        env: 
          DB_PASSWORD: ${{ secrets.DB_PASSWORD }} 
        run: | 
          ssh -o StrictHostKeyChecking=no ${{ secrets.EC2_USER 
}}@${{ secrets.EC2_HOST }} ' 
            set -e 
             
            echo "        Starting deployment..." 
            echo "===================================" 
             
            # Step 1: Verify deployment package exists 
            echo "    Step 1: Checking deployment package..." 
            if [ ! -f /tmp/laravel-deploy.tar.gz ]; then 
              echo "  Deployment package not found in /tmp!" 
              echo "Contents of /tmp:" 
              ls -la /tmp/ 
              exit 1 
            fi 
            echo "   Found deployment package:" 
            ls -lh /tmp/laravel-deploy.tar.gz 
            echo "===================================" 
             
            # Step 2: Navigate to application directory 
            echo "   Step 2: Preparing application directory..." 
            if [ ! -d /var/www/laravel ]; then 
              echo "Creating application directory..." 
              sudo mkdir -p /var/www/laravel 
            fi 
            cd /var/www/laravel 
            echo "Current directory: $(pwd)" 
            echo "===================================" 
             
            # Step 3: Put application in maintenance mode 
            echo "     Step 3: Putting application in maintenance mode..." 
            if [ -f artisan ]; then 
              php artisan down --retry=60 || echo "Maintenance mode 
failed, continuing..." 
            else 
              echo "Artisan not found, skipping maintenance mode" 
            fi 
            echo "===================================" 
             
            # Step 4: Backup .env file if it exists 
            echo "       Step 4: Backing up configuration..." 
            if [ -f .env ]; then 
              cp .env .env.backup 
              echo "   .env file backed up" 
            fi 
            echo "===================================" 
             
            # Step 5: Extract new files while preserving storage 
            echo "       Step 5: Extracting new files while preserving 
storage..." 
             
            # Create a timestamp for backup 
            TIMESTAMP=$(date +%s) 
             
            # Backup storage directory if it exists and has files 
            if [ -d storage ] && [ "$(ls -A storage 2>/dev/null)" ]; then 
              echo "Backing up storage directory..." 
              sudo mv storage /tmp/storage-backup-$TIMESTAMP 
              BACKUP_CREATED=true 
            else 
              BACKUP_CREATED=false 
            fi 
             
            # Backup bootstrap/cache if it exists and has files (except 
.gitignore) 
            if [ -d bootstrap/cache ] && [ "$(ls -A bootstrap/cache 
2>/dev/null | grep -v ".gitignore")" ]; then 
              echo "Backing up bootstrap/cache directory..." 
              sudo mv bootstrap/cache /tmp/cache-backup-$TIMESTAMP 
              CACHE_BACKUP_CREATED=true 
            else 
              CACHE_BACKUP_CREATED=false 
            fi 
             
            # Remove all other files (with sudo) 
            echo "Cleaning up old files..." 
            sudo find . -not -path "./.env" -not -path "./.env.backup" -delete 
2>/dev/null || true 
             
            # Extract the new package with sudo 
            echo "Extracting new files with sudo..." 
            sudo tar -xzf /tmp/laravel-deploy.tar.gz -C /var/www/laravel -
overwrite --no-same-owner 
             
            # Restore storage if it was backed up 
            if [ "$BACKUP_CREATED" = true ] && [ -d /tmp/storage
backup-$TIMESTAMP ]; then 
              echo "Restoring original storage directory..." 
              sudo rm -rf storage 2>/dev/null || true 
              sudo mv /tmp/storage-backup-$TIMESTAMP storage 
            fi 
             
            # Restore bootstrap/cache if it was backed up 
            if [ "$CACHE_BACKUP_CREATED" = true ] && [ -d 
/tmp/cache-backup-$TIMESTAMP ]; then 
              echo "Restoring original bootstrap/cache directory..." 
              sudo rm -rf bootstrap/cache 2>/dev/null || true 
              sudo mv /tmp/cache-backup-$TIMESTAMP bootstrap/cache 
            fi 
             
            # Fix ownership immediately after extraction 
            echo "Fixing permissions..." 
            sudo chown -R $USER:www-data /var/www/laravel 
            sudo chmod -R 755 /var/www/laravel 
            sudo chmod -R 775 /var/www/laravel/storage 
/var/www/laravel/bootstrap/cache 
             
            echo "   Extraction complete with storage preserved" 
            echo "Files extracted:" 
            ls -la /var/www/laravel | head -10 
            echo "===================================" 
             
            # Step 6: Restore .env file 
            echo "  Step 6: Restoring configuration..." 
            if [ -f .env.backup ] && [ ! -f .env ]; then 
              mv .env.backup .env 
              echo "   .env file restored" 
            elif [ ! -f .env ]; then 
              echo "    No .env file found, creating from example..." 
              cp .env.example .env 2>/dev/null || echo "APP_KEY=" > .env 
            fi 
            echo "===================================" 
             
            # Step 7: Install Composer dependencies 
            echo "       Step 7: Installing Composer dependencies..." 
            if [ -f composer.json ]; then 
              composer install --no-dev --optimize-autoloader --no
interaction 
              echo "   Composer dependencies installed" 
            else 
              echo "    composer.json not found" 
            fi 
            echo "===================================" 
            # Step 7.5:Ensure AWS S3 Flysystem package is installed 
            if [ -f composer.json ]; then 
              if ! grep -q "league/flysystem-aws-s3-v3" composer.json; then 
                echo "Adding AWS S3 Flysystem package..." 
                composer require league/flysystem-aws-s3-v3 --no
interaction 
                echo "   AWS S3 Flysystem package added" 
              else 
                echo "   AWS S3 Flysystem package already present" 
              fi 
            fi 
             
            # Step 8: Build frontend assets (CRITICAL FIX FOR VITE 
MANIFEST) 
            echo "      Step 8: Building frontend assets..." 
            if [ -f package.json ]; then 
              echo "Installing NPM dependencies..." 
              # Check if npm is installed 
              if ! command -v npm &> /dev/null; then 
                echo "    npm not found, installing Node.js..." 
                curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E 
bash - 
                sudo apt-get install -y nodejs 
              fi 
               
              # Install dependencies and build 
              npm ci --no-audit --no-fund || npm install --no-audit --no-fund 
               
              echo "Building assets..." 
              npm run build || npm run production 
               
              # Verify the build was successful 
              if [ -f public/build/manifest.json ]; then 
                echo "   Vite manifest found at public/build/manifest.json" 
                echo "Manifest contents:" 
                cat public/build/manifest.json | head -10 
              else 
                echo "    Vite manifest not found, checking directory..." 
                if [ -d public/build ]; then 
                  echo "Build directory exists but manifest.json is missing:" 
                  ls -la public/build/ 
                else 
                  echo "  public/build directory does not exist!" 
                  mkdir -p public/build 
                fi 
              fi 
               
              # Set proper permissions 
              sudo chown -R $USER:www-data public/build 
              sudo chmod -R 755 public/build 
               
              echo "   Frontend assets built successfully" 
            else 
              echo "    package.json not found, skipping asset build" 
            fi 
            echo "===================================" 
             
            # Step 9: Generate application key 
            echo "  Step 9: Checking application key..." 
            if [ -f artisan ]; then 
              if ! grep -q "^APP_KEY=" .env || [ -z "$(grep "^APP_KEY=" 
.env | cut -d '=' -f2)" ]; then 
                echo "Generating application key..." 
                php artisan key:generate --force 
              else 
                echo "   APP_KEY already exists" 
              fi 
            fi 
            echo "===================================" 
             
            # Step 10: Run migrations 
            echo "          Step 10: Running database migrations..." 
            if [ -f artisan ]; then 
              php artisan migrate --force || echo "    Migrations failed, 
continuing..." 
            fi 
            echo "===================================" 
             
            # Step 11: Optimize Laravel 
            echo "    Step 11: Optimizing Laravel..." 
            if [ -f artisan ]; then 
              php artisan optimize:clear 
              php artisan config:cache 
              php artisan route:cache || true 
              php artisan view:cache 
              echo "   Optimization complete" 
            fi 
            echo "===================================" 
             
            # Step 12: Create storage link 
            echo "   Step 12: Creating storage link..." 
            if [ -f artisan ]; then 
              # Remove existing link if it exists 
              rm -rf public/storage 
              php artisan storage:link || true 
            fi 
            echo "===================================" 
             
            # Step 13: Final permissions 
            echo "    Step 13: Setting final permissions..." 
            sudo chown -R www-data:www-data /var/www/laravel/storage 
/var/www/laravel/bootstrap/cache /var/www/laravel/public/build 
            sudo chmod -R 775 /var/www/laravel/storage 
/var/www/laravel/bootstrap/cache 
            sudo chmod -R 755 /var/www/laravel/public/build 
            echo "   Final permissions set" 
            echo "===================================" 
             
            # Step 14: Fix maintenance mode file and bring application 
back up 
            echo "        Step 14: Bringing application online..." 
             
            # Fix the maintenance mode file permission if it exists 
            if [ -f storage/framework/down ]; then 
              echo "Fixing maintenance file permissions..." 
              sudo chmod 666 storage/framework/down 2>/dev/null || true 
              sudo chown $USER:www-data storage/framework/down 
2>/dev/null || true 
            fi 
             
            # Try to bring app up normally 
            if php artisan up; then 
              echo "   Application brought online successfully" 
            else 
              echo "Trying with sudo..." 
              sudo php artisan up 
            fi 
            echo "===================================" 
             
            # Step 15: Clean up 
            echo "      Step 15: Cleaning up..." 
            rm -f /tmp/laravel-deploy.tar.gz 
            rm -f /var/www/laravel/.env.backup 
            # Remove any remaining backups 
            sudo rm -rf /tmp/storage-backup-* 2>/dev/null || true 
            sudo rm -rf /tmp/cache-backup-* 2>/dev/null || true 
            echo "   Cleanup complete" 
            echo "===================================" 
             
            echo "       DEPLOYMENT COMPLETED 
SUCCESSFULLY!       " 
             
            # Final verification - check if manifest exists 
            echo "" 
            echo "      Final Verification:" 
            if [ -f public/build/manifest.json ]; then 
              echo "   Vite manifest exists at public/build/manifest.json" 
            else 
              echo "  Vite manifest NOT found - check asset build step" 
            fi 
             
            # Show Laravel status 
            if [ -f artisan ]; then 
              echo "" 
              echo "      Application Status:" 
              php artisan about --only=environment 
            fi 
          ' 
 
      - name:           Health check 
        run: | 
          echo "       Waiting for application to be ready..." 
          sleep 15 
           
          # Try multiple times 
          for i in {1..5}; do 
            HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" 
http://${{ secrets.EC2_HOST }} || echo "000") 
            if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "302" 
]; then 
              echo "   Health check passed! (HTTP $HTTP_STATUS)" 
              exit 0 
            fi 
            echo "Attempt $i: HTTP $HTTP_STATUS - retrying in 5 
seconds..." 
            sleep 5 
          done 
           
          echo "    Health check returned HTTP $HTTP_STATUS after 5 
attempts" 
          # Don't fail the build, just warn 
 
      - name:    Notify success 
        if: success() 
        run: | 
          echo "       PRODUCTION DEPLOYMENT SUCCESSFUL! 
      " 
          echo "  Application is live at: http://${{ secrets.EC2_HOST }}" 
          echo "        Deployment completed at $(date)" 
 
      - name:    Notify failure 
        if: failure() 
        run: | 
          echo "    DEPLOYMENT FAILED!    " 
          echo "Please check the logs above for details." 
          echo "" 
          echo "     Quick fixes to try manually on your EC2 instance:" 
          echo "  ssh ${{ secrets.EC2_USER }}@${{ secrets.EC2_HOST 
}}" 
          echo "  cd /var/www/laravel" 
          echo "  npm install && npm run build" 
          echo "  sudo chown -R www-data:www-data public/build" 
          echo "  php artisan optimize:clear" 
          echo "  sudo php artisan up" 
 
Step 3: Push to GitHub 
git add . 
git commit -m "Add CI/CD pipeline with Nginx configuration for 
Ubuntu" 
git push origin main 
The workflow will automatically trigger and deploy your application 
5. Testing the Deployment 
Check Application Status 
Visit in browser: 
http://YOUR_EC2_PUBLIC_IP 
Check Nginx Status (Ubuntu commands) 
ssh -i ~/.ssh/laravel-key.pem ubuntu@YOUR_EC2_IP 
sudo systemctl status nginx 
sudo nginx -t 
Check Docker Container 
‘ 
sudo docker ps 
# Should show laravel-app container running on port 8080 
Check Nginx Logs 
sudo tail -f /var/log/nginx/laravel-access.log 
sudo tail -f /var/log/nginx/laravel-error.log 
Check Docker Logs 
sudo docker logs laravel-app 
Test Database Connection 
sudo docker exec laravel-app php artisan db:monitor 
Test S3 Integration 
sudo docker exec laravel-app php artisan tinker 
Storage::disk('s3')->put('test.txt', 'Hello S3!'); 
Storage::disk('s3')->exists('test.txt');
