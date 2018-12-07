const { registerBlockType } = window.wp.blocks;
const { __ } = window.wp.i18n;
const { Button, Dropdown } = window.wp.components;
var elem = wp.element.createElement;

registerBlockType( 'wp-oer-plugin/oer-resource-block', {
    title: __( 'OER Resource' ),
    category: 'widgets',
    icon: {
        foreground: '#121212',
        src: 'media-document'
    },
    keywords: [
        __( 'OER' ),
        __( 'Resource' ),
        __( 'History' )
    ],
    attributes: {
        resource: {
            type: 'string',
            source: 'html',
            selector: '.oer-resource-dropdown'
        }
    },
    edit: function( props ){
        function onChange(event) {
            props.setAttributes( { resource: event.target.value } );
        }
        
        return elem('select', { value: props.attributes.resource, onChange: onChange });
    },
    save: function( attributes ) {
        return elem( 'p', attributes.resource, 'Saved Embed Resource' );
    }
} );