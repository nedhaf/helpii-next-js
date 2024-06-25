// next.config.mjs

/** @type {import('next').NextConfig} */
const nextConfig = {
  distDir: 'build',
  experimental: {
    workerThreads: false,
    cpus: 4,
  },
  webpack: (config, { buildId, dev, isServer, defaultLoaders, webpack }) => {
    // Add jQuery plugin
    config.plugins.push(
      new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
      })
    );

    // Add custom loaders for sass, less, and css
    config.module.rules.push({
      test: /\.(sass|scss|less|css)$/,
      use: ['style-loader', 'css-loader', 'sass-loader', 'less-loader'],
    });

    // Fix for asset modules
    if (config.module.generator) {
      config.module.generator['asset/resource'] = config.module.generator['asset'];
      config.module.generator['asset/source'] = config.module.generator['asset'];
      delete config.module.generator['asset'];
    }

    // Important: return the modified config
    return config;
  },
};

export default nextConfig;
