const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';
    
    return {
        entry: {
            // Theme main assets
            'theme': './app/public/wp-content/themes/vireo-designs/assets/src/js/theme.js',
            'admin': './app/public/wp-content/themes/vireo-designs/assets/src/js/admin.js',
            'ui-enhancements': './app/public/wp-content/themes/vireo-designs/assets/src/js/ui-enhancements.js',
            
            // SCSS entry points
            'main': './app/public/wp-content/themes/vireo-designs/assets/src/scss/main.scss',
            'admin-styles': './app/public/wp-content/themes/vireo-designs/assets/src/scss/admin.scss',
            'components': './app/public/wp-content/themes/vireo-designs/assets/src/scss/components.scss',
        },
        
        output: {
            path: path.resolve(__dirname, 'app/public/wp-content/themes/vireo-designs/assets/dist'),
            filename: 'js/[name].js',
            clean: true,
        },
        
        module: {
            rules: [
                // JavaScript
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env'],
                        },
                    },
                },
                
                // SCSS/CSS
                {
                    test: /\.(scss|sass|css)$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                sourceMap: !isProduction,
                            },
                        },
                        {
                            loader: 'postcss-loader',
                            options: {
                                sourceMap: !isProduction,
                                postcssOptions: {
                                    plugins: [
                                        require('autoprefixer'),
                                    ],
                                },
                            },
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: !isProduction,
                                sassOptions: {
                                    outputStyle: isProduction ? 'compressed' : 'expanded',
                                },
                            },
                        },
                    ],
                },
                
                // Images
                {
                    test: /\.(png|jpg|jpeg|gif|svg|webp)$/i,
                    type: 'asset/resource',
                    generator: {
                        filename: 'images/[name].[contenthash][ext]',
                    },
                },
                
                // Fonts
                {
                    test: /\.(woff|woff2|eot|ttf|otf)$/i,
                    type: 'asset/resource',
                    generator: {
                        filename: 'fonts/[name].[contenthash][ext]',
                    },
                },
            ],
        },
        
        plugins: [
            new CleanWebpackPlugin(),
            
            new MiniCssExtractPlugin({
                filename: 'css/[name].css',
                chunkFilename: 'css/[id].css',
            }),
        ],
        
        optimization: {
            minimize: isProduction,
            minimizer: [
                new TerserPlugin({
                    terserOptions: {
                        compress: {
                            drop_console: isProduction,
                        },
                    },
                }),
                new CssMinimizerPlugin(),
            ],
            
            splitChunks: {
                chunks: 'all',
                cacheGroups: {
                    vendor: {
                        test: /[\\/]node_modules[\\/]/,
                        name: 'vendors',
                        chunks: 'all',
                    },
                },
            },
        },
        
        resolve: {
            extensions: ['.js', '.scss', '.css'],
            alias: {
                '@': path.resolve(__dirname, 'app/public/wp-content/themes/vireo-designs/assets/src'),
                '@scss': path.resolve(__dirname, 'app/public/wp-content/themes/vireo-designs/assets/src/scss'),
                '@js': path.resolve(__dirname, 'app/public/wp-content/themes/vireo-designs/assets/src/js'),
                '@images': path.resolve(__dirname, 'app/public/wp-content/themes/vireo-designs/assets/src/images'),
            },
        },
        
        devtool: isProduction ? false : 'source-map',
        
        stats: {
            colors: true,
            modules: false,
            children: false,
            chunks: false,
            chunkModules: false,
        },
        
        performance: {
            hints: isProduction ? 'warning' : false,
            maxAssetSize: 250000,
            maxEntrypointSize: 250000,
        },
    };
};