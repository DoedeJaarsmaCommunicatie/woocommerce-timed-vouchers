import { terser } from 'rollup-plugin-terser';

export default [
	{
		input: 'assets/scripts/main.js',
		output: [
			{
				file: 'dist/main.min.js',
				format: 'cjs'
			},
			{
				file: 'dist/main.iife.min.js',
				format: 'iife',
			}
		],
		plugins: [ terser() ],
	},
	{
		input: 'assets/scripts/main.js',
		output: [
			{
				file: 'dist/main.iife.js',
				format: 'iife',
			}
		],
	}
]
