# Railway Deployment Guide


Quick setup for deploying MyFoodShare Laravel application to Railway.

## 1. Create a New Project on Railway

Go to https://railway.app/
Click "New Project" or "Deploy from GitHub"

Select your GitHub repository

## 2. Add a MySQL Database

In your Railway project, click "New Service"
Select "Database" > "MySQL"

Railway will create a MySQL database service (default name: "MySQL")

## 3. Configure Your Laravel Service

Click on your Laravel service (not the database)

Go to "Variables" tab

Add environment variables:

### Required Environment Variables

| Name | Value |
|------|-------|-------------|
| APP_ENV | production |
| APP_DEBUG | false |
| APP_KEY | *(auto-generated)* |
| APP_URL | *(your Railway URL)* |
| DB_CONNECTION | mysql |
| DB_HOST | ${{MySQL.HOSTNAME}} |
| DB_PORT | ${{MySQL.PORT}} |
| DB_DATABASE | ${{MySQL.DATABASE}} |
| DB_USERNAME | ${{MySQL.USER}} |
| DB_PASSWORD | ${{MySQL.PASSWORD}} |
| CACHE_DRIVER | file |
| SESSION_DRIVER | file |
| QUEUE_CONNECTION | sync |

**Important**: Use Railway references with MySQL.* (capital M)

## 4. Deploy

Railway will automatically detect code and start building

## 5. Verify Deployment

Go to "Deployments" tab
Check the health status (should show a green checkmark)
Click on your app URL to access it

## Troubleshooting

If health check fails:
Check deployment logs for errors
Verify MySQL service is running and variables are correct

### Railway File Reference

The deployment uses [railway.json](railway.json) configuration file with a clean Nixpacks build configuration.

### Notes

Railway's Nixpacks builder auto-detects Laravel projects
- No additional configuration files needed
- Database migrations run automatically during build phase (when database is ready)
- The  endpoint returns {"status":"ok"} for health checks
