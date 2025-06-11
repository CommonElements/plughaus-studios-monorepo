(function(api) {

    api.sectionConstructor['bridal-dresses-upsell'] = api.Section.extend({

        // Remove events for this section.
        attachEvents: function() {},

        // Ensure this section is active. Normally, sections without contents aren't visible.
        isContextuallyActive: function() {
            return true;
        }
    });

    const bridal_dresses_section_lists = ['banner', 'services'];
    bridal_dresses_section_lists.forEach(bridal_dresses_homepage_scroll);

    function bridal_dresses_homepage_scroll(item, index) {
        // Detect when the front page sections section is expanded (or closed) so we can adjust the preview accordingly.
        item = item.replace(/-/g, '_');
        wp.customize.section('bridal_dresses_' + item + '_section', function(section) {
            section.expanded.bind(function(isExpanding) {
                // Value of isExpanding will = true if you're entering the section, false if you're leaving it.
                wp.customize.previewer.send(item, { expanded: isExpanding });
            });
        });
    }
})(wp.customize);

// Example JavaScript/jQuery for tab interaction
jQuery(document).ready(function($) {
    $('.customize-control-radio input[type="radio"]').change(function() {
        // Remove active class from all labels
        $('.customize-control-radio label').removeClass('active');
        
        // Add active class to the selected label
        $(this).next('label').addClass('active');
    });
});

jQuery(document).ready(function($) {
    $('#bridal-dresses-custom-container img').on('click', function() {
        $('#bridal-dresses-custom-container img').removeClass('bridal-dresses-selected-img');
        $(this).addClass('bridal-dresses-selected-img');
        $(this).prev('input[type="radio"]').prop('checked', true).trigger('change');
    });
});