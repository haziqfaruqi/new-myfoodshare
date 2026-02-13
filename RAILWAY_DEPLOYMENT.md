# Railway Deployment Guide for MyFoodShare

This guide will help you deploy the MyFoodShare Laravel application to Railway.

## Prerequisites

1. A Railway account (sign up at https://railway.app/)
2. GitHub repository with your code
3. This project's code committed and pushed to GitHub

## Step-by-Step Deployment

### 1. Create a New Project on Railway

1. Go to https://railway.app/
2. Click "New Project" or "Deploy from GitHub"
3. Select your GitHub repository

### 2. Add a MySQL Database

1. In your Railway project, click "New Service"
2. Select "Database" > "MySQL"
3. Railway will create a MySQL database service (default name: "MySQL")

**Note:** You can also use PostgreSQL if preferred - just change `DB_CONNECTION=pgsql` and use `${{Postgres.*}}` references.

### 3. Configure Your Laravel Service

1. Click on your Laravel service (not the database)
2. Go to the "Variables" tab
3. Add the following environment variables:

#### Required Environment Variables:

| Name | Value | Description |
|------|-------|-------------|
| `APP_ENV` | `production` | Environment mode |
| `APP_DEBUG` | `false` | Disable debug mode in production |
| `APP_KEY` | *(auto-generated)* | Leave empty, Laravel will generate it |
| `APP_URL` | *(your Railway URL)* | Your deployed app URL |
| `DB_CONNECTION` | `mysql` | Use MySQL |
| `DB_HOST` | `${{MySQL.HOSTNAME}}` | Reference Railway MySQL service |
| `DB_PORT` | `${{MySQL.PORT}}` | Reference Railway MySQL port |
| `DB_DATABASE` | `${{MySQL.DATABASE}}` | Reference Railway MySQL database |
| `DB_USERNAME` | `${{MySQL.USER}` | Reference Railway MySQL user |
| `DB_PASSWORD` | `${{MySQL.PASSWORD}}` | Reference Railway MySQL password |

#### How to Add Railway References:

1. For variables like `DB_HOST`, click the value field
2. Click the `{ }` icon or type `{{`
3. Select `Postgres` > `HOSTNAME`
4. Railway will insert: `${{Postgres.HOSTNAME}}`

### 4. Configure Build Settings

Your `railway.json` file is already configured with:
- Health check path: `/health`
- Start command: `php artisan serve --host=0.0.0.0 --port=${PORT}`

### 5. Run Migrations

Option 1: **Automatic (Recommended)**
- The migrations will run automatically when you deploy

Option 2: **Manual via Railway Console**
1. Go to your service > "Console" tab
2. Open a new console session
3. Run: `php artisan migrate --force`

Option 3: **Via Deploy Script**
1. In your service settings > "Root Directory"
2. Use `deploy.sh` as the deploy script

### 6. Check Deployment Status

1. Go to the "Deployments" tab
2. Wait for the deployment to complete
3. The health check should pass (green checkmark)
4. Click on your service URL to access the app

## Troubleshooting

### Health Check Failing

If the health check fails:

1. **Check Build Logs**
   - Go to "Deployments" > Click on failed deployment > "Build Logs"
   - Look for PHP errors, composer failures, or migration errors

2. **Check Runtime Logs**
   - Go to "Deployments" > "Runtime Logs"
   - Look for Laravel application errors

3. **Open Console**
   - Go to "Console" tab
   - Test manually: `curl http://localhost:3000/health`
   - Should return: `{"status":"ok","timestamp":"..."}`

4. **Verify Database Connection**
   - In Console: `php artisan tinker`
   - Test: `DB::connection()->getPdo();`
   - Should return database connection info

### Common Issues

**Issue: "Class not found"**
- Clear cache: In Console run `php artisan config:clear && php artisan route:clear && php artisan view:clear`
- Redeploy

**Issue: Database connection failed**
- Verify environment variables are using Railway references `${{...}}`
- Check PostgreSQL service is running
- Verify `DB_CONNECTION=pgsql`

**Issue: Storage link not working**
- In Console run: `php artisan storage:link`
- Redeploy

**Issue: Permissions error**
- Railway's filesystem is read-only except for `/storage` and `./bootstrap/cache`
- Ensure logs are written to `storage/logs`

### Getting the Application URL

1. Go to your service on Railway
2. Click the "Generate Domain" button (or use the auto-generated one)
3. Update `APP_URL` environment variable with this URL
4. Redeploy

## Production Checklist

- [ ] Database migrations run successfully
- [ ] `APP_DEBUG=false` is set
- [ ] `APP_KEY` is generated automatically
- [ ] Health check passes
- [ ] Storage link created
- [ ] All environment variables configured
- [ ] Application URL is set correctly

## Architecture

The deployment uses:
- **Runtime**: PHP (auto-detected by Nixpacks)
- **Database**: PostgreSQL (Railway managed)
- **Web Server**: PHP Artisan Serve (built-in Laravel server)
- **Health Check**: `/health` endpoint returning JSON

## Files Modified for Railway

- `.railway/isolate.yml` - Railway build configuration
- `railway.json` - Deployment settings and health check
- `routes/web.php` - Added `/health` endpoint
- `routes/api.php` - Added `/health` endpoint
- `.env.example` - Updated with Railway database references
- `Procfile` - Process management (alternative method)
