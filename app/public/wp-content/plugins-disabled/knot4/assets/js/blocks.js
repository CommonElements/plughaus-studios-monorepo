/**
 * Knot4 Gutenberg Blocks
 * 
 * @package Knot4
 * @since 1.0.0
 */

(function() {
    'use strict';
    
    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { 
        InspectorControls, 
        PanelColorSettings,
        BlockControls,
        AlignmentToolbar,
        FontSizePicker,
        withColors,
        getColorClassName
    } = wp.blockEditor;
    const { 
        PanelBody, 
        TextControl, 
        ToggleControl, 
        SelectControl, 
        RangeControl,
        ButtonGroup,
        Button,
        CheckboxControl,
        TextareaControl,
        ServerSideRender
    } = wp.components;
    const { __ } = wp.i18n;
    const { withSelect } = wp.data;
    
    // Helper function to create block icon
    function createIcon(iconName) {
        return el('svg', {
            width: 24,
            height: 24,
            viewBox: '0 0 24 24'
        }, el('path', {
            d: getIconPath(iconName)
        }));
    }
    
    function getIconPath(iconName) {
        const icons = {
            'donation': 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
            'events': 'M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z',
            'volunteer': 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
            'newsletter': 'M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z',
            'stats': 'M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z',
            'info': 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z'
        };
        return icons[iconName] || icons['info'];
    }
    
    // Register Donation Form Block
    registerBlockType('knot4/donation-form', {
        title: __('Donation Form', 'knot4'),
        description: __('Add a comprehensive donation form with customizable options.', 'knot4'),
        icon: createIcon('donation'),
        category: 'knot4',
        keywords: [__('donation', 'knot4'), __('form', 'knot4'), __('nonprofit', 'knot4')],
        
        attributes: {
            title: { type: 'string', default: __('Make a Donation', 'knot4') },
            suggestedAmounts: { type: 'string', default: '25,50,100,250' },
            allowCustom: { type: 'boolean', default: true },
            showFrequency: { type: 'boolean', default: true },
            showDedication: { type: 'boolean', default: true },
            showAddress: { type: 'boolean', default: false },
            showPhone: { type: 'boolean', default: false },
            campaignId: { type: 'string', default: '' },
            fundDesignation: { type: 'string', default: '' },
            buttonText: { type: 'string', default: __('Donate Now', 'knot4') },
            style: { type: 'string', default: 'default' }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            
            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Form Settings', 'knot4'), initialOpen: true },
                        el(TextControl, {
                            label: __('Form Title', 'knot4'),
                            value: attributes.title,
                            onChange: (value) => setAttributes({ title: value })
                        }),
                        el(TextControl, {
                            label: __('Suggested Amounts', 'knot4'),
                            help: __('Comma-separated list of amounts (e.g. 25,50,100,250)', 'knot4'),
                            value: attributes.suggestedAmounts,
                            onChange: (value) => setAttributes({ suggestedAmounts: value })
                        }),
                        el(TextControl, {
                            label: __('Button Text', 'knot4'),
                            value: attributes.buttonText,
                            onChange: (value) => setAttributes({ buttonText: value })
                        }),
                        el(SelectControl, {
                            label: __('Form Style', 'knot4'),
                            value: attributes.style,
                            options: [
                                { label: __('Default', 'knot4'), value: 'default' },
                                { label: __('Compact', 'knot4'), value: 'compact' },
                                { label: __('Modern', 'knot4'), value: 'modern' }
                            ],
                            onChange: (value) => setAttributes({ style: value })
                        })
                    ),
                    el(PanelBody, { title: __('Form Options', 'knot4'), initialOpen: false },
                        el(ToggleControl, {
                            label: __('Allow Custom Amount', 'knot4'),
                            checked: attributes.allowCustom,
                            onChange: (value) => setAttributes({ allowCustom: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Frequency Options', 'knot4'),
                            checked: attributes.showFrequency,
                            onChange: (value) => setAttributes({ showFrequency: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Dedication Fields', 'knot4'),
                            checked: attributes.showDedication,
                            onChange: (value) => setAttributes({ showDedication: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Address Field', 'knot4'),
                            checked: attributes.showAddress,
                            onChange: (value) => setAttributes({ showAddress: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Phone Field', 'knot4'),
                            checked: attributes.showPhone,
                            onChange: (value) => setAttributes({ showPhone: value })
                        })
                    ),
                    el(PanelBody, { title: __('Campaign Settings', 'knot4'), initialOpen: false },
                        el(TextControl, {
                            label: __('Campaign ID', 'knot4'),
                            help: __('Optional: Link this form to a specific campaign', 'knot4'),
                            value: attributes.campaignId,
                            onChange: (value) => setAttributes({ campaignId: value })
                        }),
                        el(TextControl, {
                            label: __('Fund Designation', 'knot4'),
                            help: __('Optional: Specify how donations will be used', 'knot4'),
                            value: attributes.fundDesignation,
                            onChange: (value) => setAttributes({ fundDesignation: value })
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'knot4/donation-form',
                    attributes: attributes
                })
            );
        },
        
        save: function() {
            return null; // Server-side rendered
        }
    });
    
    // Register Donation Button Block
    registerBlockType('knot4/donation-button', {
        title: __('Donation Button', 'knot4'),
        description: __('Add a simple donation button that links to your donation page.', 'knot4'),
        icon: createIcon('donation'),
        category: 'knot4',
        keywords: [__('donation', 'knot4'), __('button', 'knot4'), __('link', 'knot4')],
        
        attributes: {
            text: { type: 'string', default: __('Donate Now', 'knot4') },
            url: { type: 'string', default: '' },
            amount: { type: 'string', default: '' },
            style: { type: 'string', default: 'primary' },
            size: { type: 'string', default: 'medium' },
            newWindow: { type: 'boolean', default: false }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            
            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Button Settings', 'knot4'), initialOpen: true },
                        el(TextControl, {
                            label: __('Button Text', 'knot4'),
                            value: attributes.text,
                            onChange: (value) => setAttributes({ text: value })
                        }),
                        el(TextControl, {
                            label: __('Destination URL', 'knot4'),
                            help: __('Leave blank to use default donation page', 'knot4'),
                            value: attributes.url,
                            onChange: (value) => setAttributes({ url: value })
                        }),
                        el(TextControl, {
                            label: __('Pre-fill Amount', 'knot4'),
                            help: __('Optional: Pre-select a specific donation amount', 'knot4'),
                            value: attributes.amount,
                            onChange: (value) => setAttributes({ amount: value })
                        }),
                        el(SelectControl, {
                            label: __('Button Style', 'knot4'),
                            value: attributes.style,
                            options: [
                                { label: __('Primary', 'knot4'), value: 'primary' },
                                { label: __('Secondary', 'knot4'), value: 'secondary' },
                                { label: __('Outline', 'knot4'), value: 'outline' }
                            ],
                            onChange: (value) => setAttributes({ style: value })
                        }),
                        el(SelectControl, {
                            label: __('Button Size', 'knot4'),
                            value: attributes.size,
                            options: [
                                { label: __('Small', 'knot4'), value: 'small' },
                                { label: __('Medium', 'knot4'), value: 'medium' },
                                { label: __('Large', 'knot4'), value: 'large' }
                            ],
                            onChange: (value) => setAttributes({ size: value })
                        }),
                        el(ToggleControl, {
                            label: __('Open in New Window', 'knot4'),
                            checked: attributes.newWindow,
                            onChange: (value) => setAttributes({ newWindow: value })
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'knot4/donation-button',
                    attributes: attributes
                })
            );
        },
        
        save: function() {
            return null;
        }
    });
    
    // Register Donation Total Block
    registerBlockType('knot4/donation-total', {
        title: __('Donation Total', 'knot4'),
        description: __('Display the total amount raised.', 'knot4'),
        icon: createIcon('stats'),
        category: 'knot4',
        keywords: [__('donation', 'knot4'), __('total', 'knot4'), __('stats', 'knot4')],
        
        attributes: {
            period: { type: 'string', default: 'all' },
            campaignId: { type: 'string', default: '' },
            format: { type: 'string', default: 'currency' },
            showLabel: { type: 'boolean', default: true },
            label: { type: 'string', default: __('Total Raised', 'knot4') },
            fontSize: { type: 'string', default: 'large' },
            textAlign: { type: 'string', default: 'left' },
            backgroundColor: { type: 'string', default: '' },
            textColor: { type: 'string', default: '' }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            
            return el(Fragment, {},
                el(BlockControls, {},
                    el(AlignmentToolbar, {
                        value: attributes.textAlign,
                        onChange: (value) => setAttributes({ textAlign: value })
                    })
                ),
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Display Settings', 'knot4'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Time Period', 'knot4'),
                            value: attributes.period,
                            options: [
                                { label: __('All Time', 'knot4'), value: 'all' },
                                { label: __('This Year', 'knot4'), value: 'year' },
                                { label: __('This Month', 'knot4'), value: 'month' }
                            ],
                            onChange: (value) => setAttributes({ period: value })
                        }),
                        el(SelectControl, {
                            label: __('Format', 'knot4'),
                            value: attributes.format,
                            options: [
                                { label: __('Currency', 'knot4'), value: 'currency' },
                                { label: __('Number', 'knot4'), value: 'number' }
                            ],
                            onChange: (value) => setAttributes({ format: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Label', 'knot4'),
                            checked: attributes.showLabel,
                            onChange: (value) => setAttributes({ showLabel: value })
                        }),
                        attributes.showLabel && el(TextControl, {
                            label: __('Label Text', 'knot4'),
                            value: attributes.label,
                            onChange: (value) => setAttributes({ label: value })
                        }),
                        el(SelectControl, {
                            label: __('Font Size', 'knot4'),
                            value: attributes.fontSize,
                            options: [
                                { label: __('Small', 'knot4'), value: 'small' },
                                { label: __('Medium', 'knot4'), value: 'medium' },
                                { label: __('Large', 'knot4'), value: 'large' },
                                { label: __('Extra Large', 'knot4'), value: 'x-large' }
                            ],
                            onChange: (value) => setAttributes({ fontSize: value })
                        })
                    ),
                    el(PanelColorSettings, {
                        title: __('Color Settings', 'knot4'),
                        colorSettings: [
                            {
                                value: attributes.backgroundColor,
                                onChange: (value) => setAttributes({ backgroundColor: value }),
                                label: __('Background Color', 'knot4')
                            },
                            {
                                value: attributes.textColor,
                                onChange: (value) => setAttributes({ textColor: value }),
                                label: __('Text Color', 'knot4')
                            }
                        ]
                    })
                ),
                el(ServerSideRender, {
                    block: 'knot4/donation-total',
                    attributes: attributes
                })
            );
        },
        
        save: function() {
            return null;
        }
    });
    
    // Register Events List Block
    registerBlockType('knot4/events-list', {
        title: __('Events List', 'knot4'),
        description: __('Display a list of upcoming events.', 'knot4'),
        icon: createIcon('events'),
        category: 'knot4',
        keywords: [__('events', 'knot4'), __('calendar', 'knot4'), __('list', 'knot4')],
        
        attributes: {
            limit: { type: 'number', default: 6 },
            category: { type: 'string', default: '' },
            showPast: { type: 'boolean', default: false },
            layout: { type: 'string', default: 'grid' },
            showExcerpt: { type: 'boolean', default: true },
            showDate: { type: 'boolean', default: true },
            showLocation: { type: 'boolean', default: true },
            showPrice: { type: 'boolean', default: true },
            showRegistration: { type: 'boolean', default: true },
            columns: { type: 'number', default: 3 }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            
            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Events Settings', 'knot4'), initialOpen: true },
                        el(RangeControl, {
                            label: __('Number of Events', 'knot4'),
                            value: attributes.limit,
                            onChange: (value) => setAttributes({ limit: value }),
                            min: 1,
                            max: 12
                        }),
                        el(SelectControl, {
                            label: __('Layout', 'knot4'),
                            value: attributes.layout,
                            options: [
                                { label: __('Grid', 'knot4'), value: 'grid' },
                                { label: __('List', 'knot4'), value: 'list' },
                                { label: __('Calendar', 'knot4'), value: 'calendar' }
                            ],
                            onChange: (value) => setAttributes({ layout: value })
                        }),
                        attributes.layout === 'grid' && el(RangeControl, {
                            label: __('Columns', 'knot4'),
                            value: attributes.columns,
                            onChange: (value) => setAttributes({ columns: value }),
                            min: 1,
                            max: 4
                        }),
                        el(ToggleControl, {
                            label: __('Show Past Events', 'knot4'),
                            checked: attributes.showPast,
                            onChange: (value) => setAttributes({ showPast: value })
                        })
                    ),
                    el(PanelBody, { title: __('Display Options', 'knot4'), initialOpen: false },
                        el(ToggleControl, {
                            label: __('Show Excerpt', 'knot4'),
                            checked: attributes.showExcerpt,
                            onChange: (value) => setAttributes({ showExcerpt: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Date', 'knot4'),
                            checked: attributes.showDate,
                            onChange: (value) => setAttributes({ showDate: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Location', 'knot4'),
                            checked: attributes.showLocation,
                            onChange: (value) => setAttributes({ showLocation: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Price', 'knot4'),
                            checked: attributes.showPrice,
                            onChange: (value) => setAttributes({ showPrice: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Registration Button', 'knot4'),
                            checked: attributes.showRegistration,
                            onChange: (value) => setAttributes({ showRegistration: value })
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'knot4/events-list',
                    attributes: attributes
                })
            );
        },
        
        save: function() {
            return null;
        }
    });
    
    // Register Newsletter Signup Block
    registerBlockType('knot4/newsletter-signup', {
        title: __('Newsletter Signup', 'knot4'),
        description: __('Add a newsletter subscription form.', 'knot4'),
        icon: createIcon('newsletter'),
        category: 'knot4',
        keywords: [__('newsletter', 'knot4'), __('subscribe', 'knot4'), __('email', 'knot4')],
        
        attributes: {
            title: { type: 'string', default: __('Stay Updated', 'knot4') },
            description: { type: 'string', default: __('Subscribe to our newsletter for the latest updates.', 'knot4') },
            buttonText: { type: 'string', default: __('Subscribe', 'knot4') },
            layout: { type: 'string', default: 'horizontal' },
            showName: { type: 'boolean', default: false },
            backgroundColor: { type: 'string', default: '' },
            textColor: { type: 'string', default: '' }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            
            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Newsletter Settings', 'knot4'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'knot4'),
                            value: attributes.title,
                            onChange: (value) => setAttributes({ title: value })
                        }),
                        el(TextareaControl, {
                            label: __('Description', 'knot4'),
                            value: attributes.description,
                            onChange: (value) => setAttributes({ description: value })
                        }),
                        el(TextControl, {
                            label: __('Button Text', 'knot4'),
                            value: attributes.buttonText,
                            onChange: (value) => setAttributes({ buttonText: value })
                        }),
                        el(SelectControl, {
                            label: __('Layout', 'knot4'),
                            value: attributes.layout,
                            options: [
                                { label: __('Horizontal', 'knot4'), value: 'horizontal' },
                                { label: __('Vertical', 'knot4'), value: 'vertical' }
                            ],
                            onChange: (value) => setAttributes({ layout: value })
                        }),
                        el(ToggleControl, {
                            label: __('Show Name Field', 'knot4'),
                            checked: attributes.showName,
                            onChange: (value) => setAttributes({ showName: value })
                        })
                    ),
                    el(PanelColorSettings, {
                        title: __('Color Settings', 'knot4'),
                        colorSettings: [
                            {
                                value: attributes.backgroundColor,
                                onChange: (value) => setAttributes({ backgroundColor: value }),
                                label: __('Background Color', 'knot4')
                            },
                            {
                                value: attributes.textColor,
                                onChange: (value) => setAttributes({ textColor: value }),
                                label: __('Text Color', 'knot4')
                            }
                        ]
                    })
                ),
                el(ServerSideRender, {
                    block: 'knot4/newsletter-signup',
                    attributes: attributes
                })
            );
        },
        
        save: function() {
            return null;
        }
    });
    
    // Register Stats Display Block
    registerBlockType('knot4/stats-display', {
        title: __('Stats Display', 'knot4'),
        description: __('Show organization statistics and achievements.', 'knot4'),
        icon: createIcon('stats'),
        category: 'knot4',
        keywords: [__('stats', 'knot4'), __('statistics', 'knot4'), __('numbers', 'knot4')],
        
        attributes: {
            stats: { type: 'array', default: ['donations', 'donors', 'events'] },
            layout: { type: 'string', default: 'grid' },
            columns: { type: 'number', default: 3 },
            showIcons: { type: 'boolean', default: true },
            animateNumbers: { type: 'boolean', default: true }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            
            const availableStats = [
                { label: __('Total Donations', 'knot4'), value: 'donations' },
                { label: __('Number of Donors', 'knot4'), value: 'donors' },
                { label: __('Events Held', 'knot4'), value: 'events' }
            ];
            
            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Stats Settings', 'knot4'), initialOpen: true },
                        el('div', { style: { marginBottom: '16px' } },
                            el('label', { style: { fontWeight: 'bold', marginBottom: '8px', display: 'block' } }, 
                                __('Statistics to Display', 'knot4')
                            ),
                            availableStats.map((stat) =>
                                el(CheckboxControl, {
                                    key: stat.value,
                                    label: stat.label,
                                    checked: attributes.stats.includes(stat.value),
                                    onChange: (checked) => {
                                        const newStats = checked 
                                            ? [...attributes.stats, stat.value]
                                            : attributes.stats.filter(s => s !== stat.value);
                                        setAttributes({ stats: newStats });
                                    }
                                })
                            )
                        ),
                        el(SelectControl, {
                            label: __('Layout', 'knot4'),
                            value: attributes.layout,
                            options: [
                                { label: __('Grid', 'knot4'), value: 'grid' },
                                { label: __('Horizontal', 'knot4'), value: 'horizontal' }
                            ],
                            onChange: (value) => setAttributes({ layout: value })
                        }),
                        el(RangeControl, {
                            label: __('Columns', 'knot4'),
                            value: attributes.columns,
                            onChange: (value) => setAttributes({ columns: value }),
                            min: 1,
                            max: 4
                        }),
                        el(ToggleControl, {
                            label: __('Show Icons', 'knot4'),
                            checked: attributes.showIcons,
                            onChange: (value) => setAttributes({ showIcons: value })
                        }),
                        el(ToggleControl, {
                            label: __('Animate Numbers', 'knot4'),
                            checked: attributes.animateNumbers,
                            onChange: (value) => setAttributes({ animateNumbers: value })
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'knot4/stats-display',
                    attributes: attributes
                })
            );
        },
        
        save: function() {
            return null;
        }
    });
    
    // Register additional blocks for Pro features
    if (knot4Blocks && knot4Blocks.isPro) {
        // Campaign Progress Block (Pro)
        registerBlockType('knot4/campaign-progress', {
            title: __('Campaign Progress', 'knot4'),
            description: __('Display fundraising campaign progress with goal tracking.', 'knot4'),
            icon: createIcon('stats'),
            category: 'knot4',
            keywords: [__('campaign', 'knot4'), __('progress', 'knot4'), __('goal', 'knot4')],
            
            attributes: {
                campaignId: { type: 'number', default: 0 },
                showGoal: { type: 'boolean', default: true },
                showPercentage: { type: 'boolean', default: true },
                showRaised: { type: 'boolean', default: true },
                layout: { type: 'string', default: 'horizontal' }
            },
            
            edit: function(props) {
                const { attributes, setAttributes } = props;
                
                return el(Fragment, {},
                    el(InspectorControls, {},
                        el(PanelBody, { title: __('Campaign Settings', 'knot4'), initialOpen: true },
                            el(TextControl, {
                                label: __('Campaign ID', 'knot4'),
                                help: __('Enter the ID of the campaign to display', 'knot4'),
                                value: attributes.campaignId,
                                onChange: (value) => setAttributes({ campaignId: parseInt(value) || 0 })
                            }),
                            el(SelectControl, {
                                label: __('Layout', 'knot4'),
                                value: attributes.layout,
                                options: [
                                    { label: __('Horizontal', 'knot4'), value: 'horizontal' },
                                    { label: __('Vertical', 'knot4'), value: 'vertical' },
                                    { label: __('Circular', 'knot4'), value: 'circular' }
                                ],
                                onChange: (value) => setAttributes({ layout: value })
                            }),
                            el(ToggleControl, {
                                label: __('Show Goal Amount', 'knot4'),
                                checked: attributes.showGoal,
                                onChange: (value) => setAttributes({ showGoal: value })
                            }),
                            el(ToggleControl, {
                                label: __('Show Percentage', 'knot4'),
                                checked: attributes.showPercentage,
                                onChange: (value) => setAttributes({ showPercentage: value })
                            }),
                            el(ToggleControl, {
                                label: __('Show Amount Raised', 'knot4'),
                                checked: attributes.showRaised,
                                onChange: (value) => setAttributes({ showRaised: value })
                            })
                        )
                    ),
                    el(ServerSideRender, {
                        block: 'knot4/campaign-progress',
                        attributes: attributes
                    })
                );
            },
            
            save: function() {
                return null;
            }
        });
    }
    
})();