/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	BlockControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
	ToolbarGroup,
	ToolbarButton,
	Placeholder,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { grid, list, building } from '@wordpress/icons';

/**
 * Edit function for Property Listings block
 */
export default function Edit({ attributes, setAttributes }) {
	const {
		layout,
		columns,
		postsPerPage,
		showPagination,
		propertyType,
		orderBy,
		order,
		showExcerpt,
		showPrice,
		showLocation,
	} = attributes;

	const blockProps = useBlockProps({
		className: `vpm-property-listings layout-${layout} columns-${columns}`,
	});

	// Fetch properties for preview
	const { properties, isLoading } = useSelect((select) => {
		const { getEntityRecords, isResolving } = select(coreStore);
		const query = {
			per_page: postsPerPage,
			order,
			orderby: orderBy,
			status: 'publish',
		};

		if (propertyType && propertyType !== 'all') {
			query.property_type = propertyType;
		}

		return {
			properties: getEntityRecords('postType', 'property', query) || [],
			isLoading: isResolving('getEntityRecords', ['postType', 'property', query]),
		};
	}, [postsPerPage, orderBy, order, propertyType]);

	// Fetch property types for filter
	const { propertyTypes } = useSelect((select) => {
		const { getEntityRecords } = select(coreStore);
		return {
			propertyTypes: getEntityRecords('taxonomy', 'property_type') || [],
		};
	}, []);

	const onLayoutChange = (newLayout) => {
		setAttributes({ layout: newLayout });
	};

	const propertyTypeOptions = [
		{ label: __('All Types', 'vireo-property'), value: 'all' },
		...propertyTypes.map(type => ({
			label: type.name,
			value: type.slug,
		})),
	];

	const PropertyPreview = ({ property }) => (
		<div className="vpm-property-item">
			<div className="vpm-property-image">
				{property.featured_media ? (
					<img src={property.featured_media} alt={property.title.rendered} />
				) : (
					<div className="vpm-property-placeholder">
						<building />
					</div>
				)}
			</div>
			<div className="vpm-property-content">
				<h3 className="vpm-property-title">{property.title.rendered}</h3>
				{showLocation && property.meta?.location && (
					<p className="vpm-property-location">{property.meta.location}</p>
				)}
				{showPrice && property.meta?.price && (
					<p className="vpm-property-price">${property.meta.price}</p>
				)}
				{showExcerpt && property.excerpt?.rendered && (
					<div
						className="vpm-property-excerpt"
						dangerouslySetInnerHTML={{ __html: property.excerpt.rendered }}
					/>
				)}
			</div>
		</div>
	);

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={grid}
						label={__('Grid View', 'vireo-property')}
						isPressed={layout === 'grid'}
						onClick={() => onLayoutChange('grid')}
					/>
					<ToolbarButton
						icon={list}
						label={__('List View', 'vireo-property')}
						isPressed={layout === 'list'}
						onClick={() => onLayoutChange('list')}
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={__('Layout Settings', 'vireo-property')}>
					<SelectControl
						label={__('Layout', 'vireo-property')}
						value={layout}
						options={[
							{ label: __('Grid', 'vireo-property'), value: 'grid' },
							{ label: __('List', 'vireo-property'), value: 'list' },
						]}
						onChange={(value) => setAttributes({ layout: value })}
					/>
					{layout === 'grid' && (
						<RangeControl
							label={__('Columns', 'vireo-property')}
							value={columns}
							onChange={(value) => setAttributes({ columns: value })}
							min={1}
							max={6}
						/>
					)}
				</PanelBody>

				<PanelBody title={__('Query Settings', 'vireo-property')}>
					<RangeControl
						label={__('Number of Properties', 'vireo-property')}
						value={postsPerPage}
						onChange={(value) => setAttributes({ postsPerPage: value })}
						min={1}
						max={20}
					/>
					<SelectControl
						label={__('Property Type', 'vireo-property')}
						value={propertyType}
						options={propertyTypeOptions}
						onChange={(value) => setAttributes({ propertyType: value })}
					/>
					<SelectControl
						label={__('Order By', 'vireo-property')}
						value={orderBy}
						options={[
							{ label: __('Date', 'vireo-property'), value: 'date' },
							{ label: __('Title', 'vireo-property'), value: 'title' },
							{ label: __('Menu Order', 'vireo-property'), value: 'menu_order' },
						]}
						onChange={(value) => setAttributes({ orderBy: value })}
					/>
					<SelectControl
						label={__('Order', 'vireo-property')}
						value={order}
						options={[
							{ label: __('Descending', 'vireo-property'), value: 'DESC' },
							{ label: __('Ascending', 'vireo-property'), value: 'ASC' },
						]}
						onChange={(value) => setAttributes({ order: value })}
					/>
				</PanelBody>

				<PanelBody title={__('Display Settings', 'vireo-property')}>
					<ToggleControl
						label={__('Show Excerpt', 'vireo-property')}
						checked={showExcerpt}
						onChange={(value) => setAttributes({ showExcerpt: value })}
					/>
					<ToggleControl
						label={__('Show Price', 'vireo-property')}
						checked={showPrice}
						onChange={(value) => setAttributes({ showPrice: value })}
					/>
					<ToggleControl
						label={__('Show Location', 'vireo-property')}
						checked={showLocation}
						onChange={(value) => setAttributes({ showLocation: value })}
					/>
					<ToggleControl
						label={__('Show Pagination', 'vireo-property')}
						checked={showPagination}
						onChange={(value) => setAttributes({ showPagination: value })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{isLoading ? (
					<Placeholder icon={building} label={__('Property Listings', 'vireo-property')}>
						<Spinner />
					</Placeholder>
				) : properties.length === 0 ? (
					<Placeholder 
						icon={building} 
						label={__('Property Listings', 'vireo-property')}
						instructions={__('No properties found. Create some properties to see them here.', 'vireo-property')}
					/>
				) : (
					<div className={`vpm-properties-grid layout-${layout} columns-${columns}`}>
						{properties.map((property) => (
							<PropertyPreview key={property.id} property={property} />
						))}
					</div>
				)}
			</div>
		</>
	);
}