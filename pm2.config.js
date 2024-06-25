module.exports = {
  apps: [{
    name: 'next-app',
    script: 'npx',
    args: 'next -H 68.183.73.29 -p 3000',
    cwd: '/var/www/html/staging.helpii.se', // Set the current working directory
    instances: 1,  // You can set more instances according to your needs
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: 'production',
    },
  }],
};

