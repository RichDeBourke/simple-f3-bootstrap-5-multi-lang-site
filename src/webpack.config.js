const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer');

module.exports = (env, argv) => {
  const isProduction = argv.mode === 'production';

  return {
    mode: argv.mode || 'development',
   
    entry: {
      main: './src/js/main.js',
      style: './src/scss/style.scss'
    },
   
    output: {
      filename: isProduction ? '[name].min.js' : '[name].js',
      path: path.resolve(__dirname, 'js')
    },
   
    // Modified: Enable source maps for both development and production
    devtool: isProduction ? 'nosources-source-map' : 'eval-source-map',
   
    optimization: {
      minimize: isProduction,
      usedExports: true,
      sideEffects: false,
      providedExports: true,
      minimizer: [
        new TerserPlugin({
          extractComments: false, // Don't create separate license file
          terserOptions: {
            compress: {
              drop_console: isProduction, // Remove console.log in production
              drop_debugger: true,
              pure_funcs: ['console.log', 'console.info', 'console.warn'],
              unused: true
            },
            mangle: {
              safari10: true
            }
          }
        }),
        new CssMinimizerPlugin(),
      ],
    },
   
    module: {
      rules: [
        {
          test: /\.scss$/,
          use: [
            MiniCssExtractPlugin.loader,
            {
              loader: 'css-loader',
              options: {
                sourceMap: true
              }
            },
            {
              loader: 'postcss-loader',
              options: {
                sourceMap: true
              }
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: true,
                sassOptions: {
                  silenceDeprecations: [
                    'legacy-js-api',
                    'global-builtin',
                    'mixed-decls',
                    'color-functions',
                    'import'
                  ]
                }
              }
            }
          ]
        }
      ]
    },
   
    plugins: [
      new MiniCssExtractPlugin({
        filename: isProduction ? '../css/style.min.css' : '../css/style.css'
      }),
      ...(env.analyze ? [new BundleAnalyzerPlugin({
        analyzerMode: 'static',
        openAnalyzer: true,
        reportFilename: '../reports/bundle-report.html',
        defaultSizes: 'parsed',
        generateStatsFile: true,
        statsFilename: '../reports/stats.json'
      })] : [])
    ],
   
    resolve: {
      extensions: ['.js', '.scss', '.css'],
      alias: {
        'bootstrap/js/src/offcanvas': path.resolve(__dirname, 'src/js/offcanvas-custom.js')
      }
    }
  };
};