# Railway Deployment Guide for MyFoodShare

This guide covers deploying the MyFoodShare Laravel application to Railway using the Nixpacks builder.

## Quick Setup (For New Deployments)

1. **Create a New Project on Railway**
   - Go to https://railway.app/
   - Click "New Project" or "Deploy from GitHub"
   - Select your GitHub repository

2. **Add a MySQL Database**
   - In your Railway project, click "New Service"
   - Select "Database" > "MySQL"
   - Railway will create a MySQL database service (default name: "MySQL")

3. **Configure Your Laravel Service**
   - Click on your Laravel service (not the database)
   - Go to "Variables" tab
   - Add the following environment variables:

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

**Important**: Make sure all Railway references use `MySQL.*` (with capital M) not `Postgres.*`.

4. **Deploy**
   - Railway will automatically detect the code and start building
   - Wait for the deployment to complete

5. **Verify Deployment**
   - Go to "Deployments" tab
   - Check the health status (should show a green checkmark)
   - Click on your app URL to access it

## Troubleshooting

### Health Check Failing

If the health check fails:

1. **Check "Deployments" tab → Click on deployment → "View Logs"
2. **Look for errors in the build output** - should show "Building..." then "Built successfully"
3. **Common issues**:
   - **Missing APP_KEY**: Add your local APP_KEY value from your local `.env` file
   - **Database connection errors**: Verify MySQL service is running and variables are correct

### Railway File Reference

The deployment uses [railway.json](railway.json) configuration file with a clean Nixpacks build configuration.

### Notes

- Railway's Nixpacks builder auto-detects Laravel projects
- No additional configuration files needed beyond `railway.json`
- Database migrations run automatically during build phase (when database is ready)
- The `/health` endpoint returns `{"status":"ok"}` for health checks
