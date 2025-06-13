/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { building } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * Register the Property Listings block
 */
registerBlockType(metadata.name, {
	icon: building,
	edit,
	save,
});