# Railway Deployment Guide for MyFoodShare

This guide covers deploying the MyFoodShare Laravel application to Railway using the Nixpacks builder.

## Step-by-Step Deployment

### 1. Create a New Project on Railway

1. Go to https://railway.app/
2. Click "New Project" or "Deploy from GitHub"
3. Select your GitHub repository
4. Click "Set up deployments"
5. Connect your GitHub account if prompted

### 2. Add a MySQL Database

1. In your Railway project, click "New Service"
2. Select "Database" > "MySQL"
3. Railway will create a MySQL database service (default name: "MySQL")
4. Wait for the database to be provision (shows a green checkmark when ready)

### 3. Configure Your Laravel Service

1. Click on your Laravel service (not the database)
2. Go to "Variables" tab
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
| `DB_USERNAME` | `${{MySQL.USER}}` | Reference Railway MySQL user |
| `DB_PASSWORD` | `${{MySQL.PASSWORD}}` | Reference Railway MySQL password |
| `CACHE_DRIVER` | `file` | Use file cache |
| `SESSION_DRIVER` | `file` | Use file sessions |
| `QUEUE_CONNECTION` | `sync` | Use sync queue driver |

#### How to Add Railway References:

For variables like `DB_HOST`, click the value field, then click the `{ }` icon or type `{{`. Select your MySQL service to reference the connection variables.

**Important**: Make sure all Railway references use `MySQL.*` (with capital M) not `Postgres.*`.

### 4. Deploy

1. Commit and push all your changes to GitHub
2. Railway will automatically detect the code and start building
3. The build process will:
   - Install Composer dependencies
   - Install and build npm assets
   - Run database migrations (`php artisan migrate --force`)
   - Create storage link
   - Cache Laravel configs
4. Once build completes, Railway will start the application with:
   ```
   php artisan serve --host=0.0.0.0 --port=$PORT
   ```
`

### 5. Verify Deployment

1. Go to "Deployments" tab in Railway
2. Wait for the deployment to complete
3. Check the health status (should show a green checkmark)
4. Click on your app URL to access it

### 6. Custom Domain (Optional)

If you have a custom domain:

1. Go to "Settings" > "Networking"
2. Add your custom domain
3. Railway will show a DNS target (e.g., `cname.railway.app`)
4. Add this CNAME record at your domain registrar:
   - **Type**: CNAME
   - **Name/Host**: `@` or your domain root
   - **Value**: The DNS target from Railway

5. Wait for DNS propagation (can take 5 minutes to 24 hours)
6. Update `APP_URL` environment variable to your custom domain

## Troubleshooting

### Health Check Failing

If the health check fails:

1. Check "Deployments" > Click on deployment > "View Logs"
2. Look for errors in the build output
3. Common issues:
   - **Missing APP_KEY**: Add your local APP_KEY value from `.env`
   - **Database connection errors**: Verify MySQL service is running and variables are correct
   - **Migration errors**: Check if migrations ran successfully in build logs

### Railway File Reference

The deployment uses this configuration file:

- **[railway.json](railway.json)** - Main Railway configuration
  - Uses Nixpacks builder
  - Runs migrations during build phase
  - Starts Laravel with `php artisan serve`

## Notes

- Railway's Nixpacks builder auto-detects Laravel projects
- No additional configuration files needed
- Database migrations run automatically during build (not on server start)
- Health check endpoint: `/health` (returns JSON status)
