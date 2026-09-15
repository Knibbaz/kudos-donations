const defaultConfig = require('@wordpress/scripts/config/webpack.config');

// Allows the admin and front dev servers to run side by side on separate ports.
const port = Number(process.env.WP_DEV_SERVER_PORT) || 8887;

module.exports = {
	...defaultConfig,
	devServer: {
		...defaultConfig.devServer,
		port,
		proxy: [
			{
				pathFilter: '/build',
				pathRewrite: { '^/build': '' },
				target: `http://localhost:${port}`,
			},
		],
	},
	optimization: {
		...defaultConfig.optimization,
		runtimeChunk: false,
	},
};
