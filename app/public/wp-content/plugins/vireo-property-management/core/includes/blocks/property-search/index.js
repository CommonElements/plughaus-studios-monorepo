/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { search } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * Register the Property Search block
 */
registerBlockType(metadata.name, {
	icon: search,
	edit,
	save,
});